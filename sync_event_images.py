from pathlib import Path
import re

root = Path('.')
js_file = root / 'js' / 'eventos.js'
php_dir = root / 'php' / 'eventos'
text = js_file.read_text(encoding='utf-8')

# Extract mapping using the event listing data
entries = re.findall(r"\{[^\}]*nombre:\s*\"([^\"]+)\"[^\}]*imagen:\s*\"([^\"]+)\"", text)
entries += re.findall(r"\{[^\}]*nombre:\s*\"([^\"]+)\"[^\}]*imagen:\s*'([^']+)'", text)

# Normalize slugs to compare with filenames
import unicodedata

def slug(value):
    s = unicodedata.normalize('NFD', value.lower())
    s = ''.join(ch for ch in s if unicodedata.category(ch) != 'Mn')
    s = re.sub(r'[^a-z0-9]+', '-', s)
    return re.sub(r'(^-|-$)', '', s)

mapping = {slug(name): img for name, img in entries}

# Manual overrides for file names not matching exact event names
manual = {
    'bambola': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRh8BnVQ1qdPDXqoHVHylKrGhwk3guImN0d1ZRYbBNmSXoKPhqmCE-pMsE&s=10',
    'carrera-de-bicicleta': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ7b44_MpWq3Wzray5_wVaB-4meo0Tam3gwbPNK_1AJ74I1Xo5EhmUKhTIE&s=10',
    'carrera-nocturna': '../img/carrera_noche.webp',
    'circuito-en-bicicleta': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ7b44_MpWq3Wzray5_wVaB-4meo0Tam3gwbPNK_1AJ74I1Xo5EhmUKhTIE&s=10',
    'copa-de-natacion': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTfYqzP-3hljVNvWo0X2mA8fxJY9EOZKFXquVDdbwRnvST-uFmxjLEum-d&s=10',
    'exposalto': '../img/exposalto.jpeg',
    'feria-de-emprendedores': '../img/feria_emprendedores.jpg',
    'festival-de-la-naranja': '../img/festival_naranja.jpg',
    'futbol-x5': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLGcgcjLaLtUFzXSfs5EzS0x3c23cfZtXgew3MzIyfJWBgxce4Bah7vhA&s=10',
    'la-ferne': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSzXMkEsvlia-KLamghIoD7xL9Rpz72SnkxikCvS_uH0Q&s',
    'lafosa-bike': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTLYlimLpyzEAGbkjhkZr3cPP7pON89wJlOJPhKhGMLPM6xUx1nw3Pfj4s&s=10',
    'muestra-de-danza': '../img/danza.jpg',
    'polo': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTrj22POvl9MEEF961dr53OM-Hpxz75raAMOqjgqS_fNJ15QnNkQmBHkx_z&s=10',
    'porco-negro': '../img/porco.avif',
    'rally': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQvOwKgEnUpg8aNJEn8g9CuGqAW1ofcfyKSsld8e55fDCk-2MzS0IOWpOI&s=10',
    'streetball-salto': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ0pAIE9pTlfySurzoR4nHmaJctr45FdL9iY9xVQXMfhhcFj8Gn_pXJ5z99&s=10',
    'surf-y-kayak': 'https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg',
    'torneo-de-ajedrez': 'https://www.clarin.com/2024/10/10/IUl8ywHqRO_2000x1500__1.jpg',
    'torneo-de-beach-volley': 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTturliqhgPakucL8C4kedxXyT6XEhQKkcXrZRh-af6MZf5zDZvjFQPZmV2TLieDskgPNvK3PyipLTjJc6wCuBY4T-08gd08muSeZ8sHo8&s=10',
}

for file in sorted(php_dir.glob('*.php')):
    key = file.stem
    image = mapping.get(key) or manual.get(key)
    if not image:
        print(f'WARN missing image mapping for {file.name}')
        continue
    content = file.read_text(encoding='utf-8')
    new_content = re.sub(r'style="background-image:url\([^\)]*\)"', f'style="background-image:url(\'{image}\')"', content)
    if new_content != content:
        file.write_text(new_content, encoding='utf-8')
        print(f'PATCHED {file.name} -> {image}')
    else:
        print(f'NO CHANGE {file.name}')
"}