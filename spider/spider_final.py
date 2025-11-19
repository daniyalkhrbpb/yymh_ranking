import requests
from bs4 import BeautifulSoup
import pymysql
import time
import random
import re
import os  # 新增: 用于文件路径操作
import hashlib  # 新增: 用于生成唯一文件名
from urllib.parse import urljoin
from requests.adapters import HTTPAdapter
from urllib3.util.retry import Retry

# ==========================================
# 1. 配置区域
# ==========================================
DB_CONFIG = {
    'host': '192.168.86.128',  # 您的本地 IP 配置
    'user': 'root',
    'password': '123456',  # 您的数据库密码
    'db': 'yymh_ranking',  # 您的数据库名
    'charset': 'utf8mb4',
    'cursorclass': pymysql.cursors.DictCursor
}

BASE_URL = "https://www.syyym.net"
HEADERS = {
    'User-Agent': 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1',
    'Referer': BASE_URL
}

# 新增: 图片本地化配置
# 注意: 这个路径应该是Laravel项目 public 目录下的一个绝对路径
# 例如：'/var/www/yymh_ranking/ranking/public/covers/' 或 'C:/path/to/ranking/public/covers/'
# 请根据您的实际部署环境修改此绝对路径，并确保目录存在且爬虫有写入权限。
#linux路径
#LOCAL_IMAGE_BASE_DIR = '/path/to/your/laravel/public/covers/'
#windows路径
LOCAL_IMAGE_BASE_DIR = 'C:\\laragon\\www\\ranking\\public\\covers'

# 数据库中存储的相对路径前缀 (假设在public目录下)
DB_PATH_PREFIX = 'covers/'


# ==========================================
# 2. 工具函数
# ==========================================

def get_http_session():
    session = requests.Session()
    retry = Retry(total=3, read=3, connect=3, backoff_factor=1, status_forcelist=[500, 502, 503, 504])
    adapter = HTTPAdapter(max_retries=retry)
    session.mount('http://', adapter)
    session.mount('https://', adapter)
    session.headers.update(HEADERS)
    return session


def get_db_connection():
    return pymysql.connect(**DB_CONFIG)


def get_or_create_category_id(cursor, category_name):
    if not category_name: return 0
    cursor.execute("SELECT id FROM categories WHERE name = %s", (category_name,))
    result = cursor.fetchone()
    if result:
        return result['id']
    cursor.execute("INSERT INTO categories (name) VALUES (%s)", (category_name,))
    return cursor.lastrowid


def clean_summary(text):
    """清洗简介"""
    if not text: return "暂无简介"
    keywords = ["漫画讲述了:", "漫画简介："]
    for kw in keywords:
        if kw in text: return text.split(kw)[-1].strip()
    return text


def download_cover_image(image_url, title, session):
    """下载封面图片到本地, 并返回相对路径"""
    if not image_url:
        return ""

    full_image_url = urljoin(BASE_URL, image_url)
    if not full_image_url.startswith('http'):
        # 如果不是完整的URL，可能本身就是相对路径，直接返回原值（不推荐，但作为极端情况处理）
        return image_url

    try:
        # 1. 确保本地存储目录存在
        os.makedirs(LOCAL_IMAGE_BASE_DIR, exist_ok=True)

        # 2. 生成唯一文件名 (使用URL的哈希值和原始文件名后缀)
        # 移除URL中的查询参数，获取文件后缀名
        url_without_params = full_image_url.split('?')[0]
        ext = os.path.splitext(url_without_params)[-1].lower()
        if not ext or ext not in ['.jpg', '.jpeg', '.png', '.gif']:
            ext = '.jpg'  # 默认后缀

        # 使用完整URL的MD5哈希值作为文件名，确保唯一性并避免过长文件名
        filename_base = hashlib.md5(full_image_url.encode('utf-8')).hexdigest()
        local_filename = filename_base + ext
        local_filepath_abs = os.path.join(LOCAL_IMAGE_BASE_DIR, local_filename)

        # 3. 检查文件是否已存在 (避免重复下载)
        local_path_db = DB_PATH_PREFIX + local_filename
        if os.path.exists(local_filepath_abs):
            # print(f"  [i] 封面已存在: {local_path_db}")
            return local_path_db

        # 4. 下载图片
        print(f"  [+] 正在下载封面: {full_image_url}")
        response = session.get(full_image_url, timeout=10)
        if response.status_code == 200:
            with open(local_filepath_abs, 'wb') as f:
                f.write(response.content)

            return local_path_db
        else:
            print(f"  [X] 下载图片失败: {full_image_url}, 状态码: {response.status_code}")
            return ""

    except Exception as e:
        print(f"  [!] 下载图片时发生错误: {e}")
        return ""


# ==========================================
# 3. 核心采集逻辑
# ==========================================

def parse_detail_and_save(task_data):
    url = task_data.get('url')
    if not url: return

    flags = task_data.get('flags', {})
    homepage_cover = task_data.get('cover', '')
    views = task_data.get('views', 0)

    session = get_http_session()
    conn = get_db_connection()

    try:
        response = session.get(url, timeout=15)
        response.encoding = 'utf-8'
        if response.status_code != 200:
            print(f"  [X] 无法访问详情页: {url}, 状态码: {response.status_code}")
            return

        soup = BeautifulSoup(response.text, 'html.parser')

        # 1. 标题 (H1或Meta)
        title = soup.select_one('h1.text-left').text.strip() if soup.select_one('h1.text-left') else ""
        if not title:
            tag = soup.find('meta', property='og:title')
            title = tag['content'].strip() if tag else "未知漫画"

        # 2. 简介 (精准定位 .info2 p 并清洗)
        summary_tag = soup.select_one('.info2 p')
        summary = clean_summary(summary_tag.text) if summary_tag else "暂无简介"

        # 3. 作者 (H3)
        author_tag = soup.select_one('.info2 h3')
        author = author_tag.text.replace("漫画作者：", "").strip() if author_tag else "未知"

        # 4. 封面 (优先用详情页 .info1 img)
        cover_tag = soup.select_one('.info1 img')
        final_cover = cover_tag.get('src') if (cover_tag and cover_tag.get('src')) else homepage_cover

        # 5. 元数据 (重点抓取 update_time)
        def get_meta(prop):
            t = soup.find('meta', property=prop)
            return t['content'].strip() if t else ""

        category_name = get_meta('og:novel:category') or "其他"
        status = get_meta('og:novel:status') or "连载中"
        update_time = get_meta('og:novel:update_time')  # 抓取 '2025-11-17 09:35:05'
        latest_chapter = get_meta('og:novel:latest_chapter_name')

        print(f"  入库: 《{title}》 | 更新: {update_time} | 点击: {views}")

        with conn.cursor() as cursor:
            cat_id = get_or_create_category_id(cursor, category_name)

            # 存入漫画表 (此处不变)
            sql = """
                INSERT INTO comics 
                (category_id, title, origin_url, cover_url, author, status, summary, last_updated_time, latest_chapter_title, 
                 is_recommend, is_hot_search, is_new, is_ranking, is_latest, views)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                ON DUPLICATE KEY UPDATE 
                category_id=VALUES(category_id), title=VALUES(title), cover_url=VALUES(cover_url), 
                status=VALUES(status), summary=VALUES(summary), last_updated_time=VALUES(last_updated_time),
                latest_chapter_title=VALUES(latest_chapter_title), updated_at=NOW(),
                is_recommend=VALUES(is_recommend), is_hot_search=VALUES(is_hot_search),
                is_new=VALUES(is_new), is_ranking=VALUES(is_ranking), is_latest=VALUES(is_latest),
                views=VALUES(views)
            """
            cursor.execute(sql, (
                cat_id, title, url, final_cover, author, status, summary, update_time, latest_chapter,
                flags.get('is_recommend', 0), flags.get('is_hot_search', 0),
                flags.get('is_new', 0), flags.get('is_ranking', 0), flags.get('is_latest', 0),
                views
            ))

            cursor.execute("SELECT id FROM comics WHERE origin_url = %s", (url,))
            comic_id = cursor.fetchone()['id']

            # ==============================================================
            # 【修改】存章节：基于章节标题去重
            # ==============================================================

            # 1. 查询该漫画已有的章节标题集合
            # 注意：这里我们使用 title 作为唯一标识
            cursor.execute("SELECT title FROM chapters WHERE comic_id = %s", (comic_id,))
            existing_titles = {row['title'].strip() for row in cursor.fetchall()}

            # 2. 采集新的章节列表
            chapter_list = soup.select('ul.list-charts li a, #chapter-list li a')

            if chapter_list:
                chapter_data_to_insert = []

                for i, link in enumerate(chapter_list):
                    c_title = link.text.strip()
                    c_href = link.get('href')
                    if not c_href: continue
                    c_url = urljoin(BASE_URL, c_href)

                    # 3. 检查章节标题是否已存在，如果不存在则添加到待插入列表
                    if c_title not in existing_titles:
                        chapter_data_to_insert.append((comic_id, c_title, c_url, i))

                        # 立即添加到集合，防止同一批次中有重复标题被插入
                        existing_titles.add(c_title)

                        # 4. 只插入新的章节
                if chapter_data_to_insert:
                    print(f"  [+] 发现 {len(chapter_data_to_insert)} 个新章节并准备插入 (基于标题去重)。")

                    # 使用普通的 INSERT 即可
                    cursor.executemany(
                        "INSERT INTO chapters (comic_id, title, url, sort_order) VALUES (%s, %s, %s, %s)",
                        chapter_data_to_insert
                    )
                else:
                    print("  [i] 未发现新章节。")

            # ==============================================================
            # 【修改结束】
            # ==============================================================

            conn.commit()

    except Exception as e:
        # print(f"  [!] {url} 错误: {e}")
        # 为了调试方便，打印完整的错误堆栈
        import traceback
        traceback.print_exc()
    finally:
        if 'conn' in locals() and conn.open:
            conn.close()


def run_spider():
    print("--- 开始采集 (V8: 移除自动清缓存，保证所有数据不限制数量) ---")
    session = get_http_session()
    tasks = {}

    def add_task(url, flag_name, cover="", views=0):
        if not url or '/yy/' not in url: return
        full_url = urljoin(BASE_URL, url)

        if full_url not in tasks:
            tasks[full_url] = {'url': full_url, 'flags': {}, 'cover': cover, 'views': 0}

        tasks[full_url]['flags'][flag_name] = 1
        if cover: tasks[full_url]['cover'] = cover
        if views > 0: tasks[full_url]['views'] = views

    try:
        response = session.get(BASE_URL, timeout=15)
        soup = BeautifulSoup(response.text, 'html.parser')

        tasks = {}

        # 1. [网友热搜]
        hot_text = soup.find(string="网友热搜")
        if hot_text and hot_text.find_parent('div', class_='panel'):
            for a in hot_text.find_parent('div', class_='panel').select('.panel-body li a'):
                add_task(a.get('href'), 'is_hot_search')

        # 2. [热门推荐]
        rec_text = soup.find(string=re.compile("热门推荐"))
        if rec_text and rec_text.find_parent('div', class_='panel'):
            for item in rec_text.find_parent('div', class_='panel').select('.media'):
                a = item.select_one('h3.book-title a')
                img = item.select_one('img')
                if a:
                    add_task(a.get('href'), 'is_recommend', img.get('src') if img else "")

        # 3. [近期更新]
        update_table = soup.select_one('#llastupdate')
        if update_table:
            for tr in update_table.select('tr'):
                a = tr.select_one('td:first-child a')
                if a: add_task(a.get('href'), 'is_latest')

        # 4. [新漫上线]
        new_text = soup.find(string=re.compile("新漫上线"))
        if new_text and new_text.find_parent('div', class_='panel'):
            for li in new_text.find_parent('div', class_='panel').select('ul li'):
                a = li.select_one('a')
                if a:
                    # 尝试从 badge 提取日期，但爬虫主要只负责传递链接
                    add_task(a.get('href'), 'is_new')

        # 5. [热门排行] (抓取点击量 Badge)
        rank_texts = soup.find_all(string=re.compile("热门排行"))
        for r_text in rank_texts:
            panel = r_text.find_parent('div', class_='panel')
            if panel:
                for li in panel.select('ul li'):
                    a = li.select_one('a')
                    badge = li.select_one('.badge')
                    if a:
                        views_num = 0
                        if badge:
                            try:
                                # 清理数字里的非数字字符，防止报错
                                views_num = int(re.sub(r'\D', '', badge.text.strip()))
                            except:
                                views_num = 0
                        add_task(a.get('href'), 'is_ranking', views=views_num)
                break

        print(f"共发现 {len(tasks)} 部漫画。")
        for i, (url, data) in enumerate(tasks.items(), 1):
            print(f"[{i}/{len(tasks)}] ", end="")
            parse_detail_and_save(data)
            time.sleep(random.uniform(0.5, 1.5))

        print("--- 采集结束。请等待缓存到期或手动执行：php artisan responsecache:clear ---")

    except Exception as e:
        import traceback
        traceback.print_exc()


if __name__ == "__main__":
    run_spider()