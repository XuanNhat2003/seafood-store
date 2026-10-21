import json, os, urllib.parse, urllib.request, concurrent.futures, sys, time

API = "http://localhost:5230"
OUT = os.path.dirname(os.path.abspath(__file__))
MODE = sys.argv[1] if len(sys.argv) > 1 else "all"  # prod | cat | all

def kw(name):
    n = name.lower()
    # order: specific -> generic
    table = [
        ("tôm hùm baby", "fresh raw baby lobster"),
        ("tôm hùm", "fresh raw spiny lobster whole"),
        ("tôm càng xanh", "fresh giant river prawn with blue claws"),
        ("tôm tích", "fresh raw mantis shrimp"),
        ("bề bề", "fresh raw mantis shrimp"),
        ("tôm sú", "fresh raw black tiger shrimp"),
        ("tôm thẻ", "fresh raw whiteleg shrimp"),
        ("tôm viên", "shrimp meatballs"),
        ("tôm", "fresh raw shrimp"),
        ("cua hoàng đế", "alaskan king crab whole"),
        ("chân cua hoàng đế", "alaskan king crab legs"),
        ("cua gạch", "fresh mud crab full of roe"),
        ("cua thịt", "fresh meaty mud crab"),
        ("cua", "fresh mud crab"),
        ("ghẹ", "fresh blue swimming crab"),
        ("cá hồi", "fresh norwegian salmon fillet"),
        ("đầu cá hồi", "fresh salmon head"),
        ("cá thu", "fresh mackerel fish steak"),
        ("cá ngừ", "fresh raw tuna fillet"),
        ("cá bớp", "fresh cobia fish steak"),
        ("cá mú", "fresh live grouper fish"),
        ("cá chẽm", "fresh sea bass fish whole"),
        ("cá dứa", "dried catfish"),
        ("cá chỉ vàng", "dried yellow stripe fish"),
        ("khô cá", "dried fish"),
        ("cá khô", "dried fish"),
        ("mực một nắng", "semi dried squid"),
        ("mực ống", "fresh raw squid tubes"),
        ("mực lá", "fresh bigfin reef squid"),
        ("khô mực", "dried squid snack"),
        ("chả mực", "golden fried squid cake patties"),
        ("chả cá", "vietnamese fish cake"),
        ("mực", "fresh raw squid"),
        ("bạch tuộc", "fresh raw octopus"),
        ("ốc hương", "fresh babylonia sweet sea snails"),
        ("sò huyết", "fresh blood cockles"),
        ("sò điệp", "fresh scallops on the half shell"),
        ("cồi sò điệp", "fresh raw scallop meat"),
        ("hàu", "fresh oysters on the half shell"),
        ("nghêu", "fresh clams"),
        ("cá viên", "assorted seafood balls"),
        ("viên", "assorted seafood balls"),
        ("cá", "fresh whole fish"),
    ]
    for key, val in table:
        if key in n:
            return val
    return "fresh assorted seafood"

def prompt_for(name, size):
    desc = kw(name)
    p = (f"professional food photography of {desc}, vietnamese seafood market, "
         f"on crushed ice, fresh, top view, studio lighting, high detail, "
         f"appetizing, clean background, no text, no watermark")
    return (f"https://image.pollinations.ai/prompt/{urllib.parse.quote(p)}"
            f"?width={size}&height={size}&nologo=true&seed={abs(hash(name)) % 90000 + 1000}")

def fetch(url, path, tries=5):
    # resume: skip already-downloaded valid files
    if os.path.exists(path) and os.path.getsize(path) > 6000:
        return True
    backoff = 4
    for t in range(tries):
        try:
            req = urllib.request.Request(url, headers={"User-Agent": "Mozilla/5.0"})
            with urllib.request.urlopen(req, timeout=120) as r:
                data = r.read()
            if len(data) > 6000:
                with open(path, "wb") as f:
                    f.write(data)
                return True
        except urllib.error.HTTPError as e:
            if e.code == 429 and t < tries - 1:
                time.sleep(backoff); backoff *= 2; continue
            if t == tries - 1:
                print("  FAIL", os.path.basename(path), str(e)[:50])
        except Exception as e:
            if t == tries - 1:
                print("  FAIL", os.path.basename(path), str(e)[:50])
            time.sleep(backoff); backoff *= 2
    return False

def get(url):
    req = urllib.request.Request(url, headers={"User-Agent": "Mozilla/5.0"})
    with urllib.request.urlopen(req, timeout=30) as r:
        return json.load(r)

# 1) products that currently use the duplicated /uploads images
prods = get(f"{API}/api/products?pageSize=200")["items"]
targets = [p for p in prods if "/uploads/" in (p.get("imageUrl") or "")]
print(f"Products to generate: {len(targets)}")

# 2) all categories
cats = get(f"{API}/api/categories").get("data", [])
print(f"Categories to generate: {len(cats)}")

jobs = []  # (kind, id, url, path)
if MODE in ("prod", "all"):
    for p in targets:
        jobs.append(("prod", p["id"], prompt_for(p["name"], 700),
                     os.path.join(OUT, f"prod_{p['id']}.jpg")))
if MODE in ("cat", "all"):
    for c in cats:
        jobs.append(("cat", c["id"], prompt_for(c["name"], 500),
                     os.path.join(OUT, f"cat_{c['id']}.jpg")))

ok_prod, ok_cat = [], []
def run(job):
    kind, _id, url, path = job
    if fetch(url, path):
        return (kind, _id)
    return None

# gentle concurrency to avoid 429 rate limiting
with concurrent.futures.ThreadPoolExecutor(max_workers=2) as ex:
    for res in ex.map(run, jobs):
        if res:
            (ok_prod if res[0] == "prod" else ok_cat).append(res[1])

print(f"OK products: {len(ok_prod)} | OK categories: {len(ok_cat)}")

# 3) emit SQL to repoint DB to the freshly saved local files
sql = []
B = "http://localhost:5230/images/local"
if ok_prod:
    ids = ",".join(str(i) for i in ok_prod)
    sql.append(f"UPDATE products SET \"ImageUrl\"='{B}/prod_'||\"Id\"||'.jpg' WHERE \"Id\" IN ({ids});")
    sql.append(f"UPDATE product_images pi SET \"ImageUrl\"=p.\"ImageUrl\" FROM products p "
               f"WHERE pi.\"ProductId\"=p.\"Id\" AND pi.\"IsPrimary\"=true AND p.\"Id\" IN ({ids});")
if ok_cat:
    ids = ",".join(str(i) for i in ok_cat)
    sql.append(f"UPDATE categories SET \"ImageUrl\"='{B}/cat_'||\"Id\"||'.jpg' WHERE \"Id\" IN ({ids});")

with open(os.path.join(OUT, "_update_imgs.sql"), "w", encoding="utf-8") as f:
    f.write("\n".join(sql))
print("SQL written to _update_imgs.sql")
