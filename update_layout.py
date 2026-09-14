import os
import re

dashboard_path = r"c:\xampp\htdocs\CAPSTONE SQUIFM\resources\views\customer\dashboard.blade.php"
with open(dashboard_path, "r", encoding="utf-8") as f:
    dashboard_content = f.read()

top_part = re.search(r"(?s)(<!DOCTYPE html>.*?<main class=\"flex-1\">)", dashboard_content).group(1)
footer_part = re.search(r"(?s)(</main>\s*</div>\s*<!-- Footer -->.*?</html>)", dashboard_content)
if footer_part:
    footer_part = footer_part.group(1)
else:
    footer_part = re.search(r"(?s)(</main>\s*</div>\s*<!-- Footer -->.*?)</script>)", dashboard_content).group(1) + "</script>\n</body>\n</html>"

top_part = top_part.replace("<title>My Account | Escalona's Farm</title>", "<title>Escalona's Farm</title>")

files_to_update = [
    r"c:\xampp\htdocs\CAPSTONE SQUIFM\resources\views\order.blade.php"
]

for file_path in files_to_update:
    if os.path.exists(file_path):
        with open(file_path, "r", encoding="utf-8") as f:
            content = f.read()
        
        inner_content_match = re.search(r"(?s)<main[^>]*>(.*?)</main>", content)
        if inner_content_match:
            inner_content = inner_content_match.group(1)
            new_content = top_part + "\n" + inner_content + "\n" + footer_part
            with open(file_path, "w", encoding="utf-8") as f:
                f.write(new_content)
            print(f"Updated {file_path}")
        else:
            print(f"Could not find <main> in {file_path}")
print("Done")
