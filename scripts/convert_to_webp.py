#!/usr/bin/env python3
"""
Script de optimización y conversión a formato WebP para el menú de ISTPET Bar.
Convierte imágenes JPG/PNG a WebP con compresión de alto rendimiento y calidad 85%.
"""
import os
import sys
from pathlib import Path
from PIL import Image

SOURCE_DIR = Path("resources/images_source")
PUBLIC_PROD_DIR = Path("public/images/productos")
PUBLIC_COMBO_DIR = Path("public/images/combos")
STORAGE_PROD_DIR = Path("storage/app/public/productos")
STORAGE_COMBO_DIR = Path("storage/app/public/combos")

COMBOS = ["combo-clasico", "combo-familiar", "combo-desayuno"]

def setup_directories():
    for d in [PUBLIC_PROD_DIR, PUBLIC_COMBO_DIR, STORAGE_PROD_DIR, STORAGE_COMBO_DIR]:
        d.mkdir(parents=True, exist_ok=True)

def process_image(src_path: Path):
    name = src_path.stem
    is_combo = name in COMBOS

    dest_public_dir = PUBLIC_COMBO_DIR if is_combo else PUBLIC_PROD_DIR
    dest_storage_dir = STORAGE_COMBO_DIR if is_combo else STORAGE_PROD_DIR

    try:
        with Image.open(src_path) as img:
            # Convertir a RGB si está en RGBA, CMYK o P
            if img.mode in ("RGBA", "LA"):
                background = Image.new("RGB", img.size, (255, 255, 255))
                background.paste(img, mask=img.split()[-1])
                img = background
            elif img.mode != "RGB":
                img = img.convert("RGB")

            # Redimensionar a tamaño optimizado de 800x600 manteniendo relación de aspecto
            max_width, max_height = 800, 600
            img.thumbnail((max_width, max_height), Image.Resampling.LANCZOS)

            # Destinos WebP
            webp_public = dest_public_dir / f"{name}.webp"
            webp_storage = dest_storage_dir / f"{name}.webp"

            # Guardar en WebP con calidad 85%
            img.save(webp_public, "WEBP", quality=85, method=6)
            img.save(webp_storage, "WEBP", quality=85, method=6)

            # También mantener versión JPG por retrocompatibilidad si el sistema lo requiere
            jpg_public = dest_public_dir / f"{name}.jpg"
            img.save(jpg_public, "JPEG", quality=85, optimize=True)

            size_webp_kb = webp_public.stat().st_size / 1024
            size_orig_kb = src_path.stat().st_size / 1024
            saving = 100 - (size_webp_kb / size_orig_kb * 100) if size_orig_kb > 0 else 0

            print(f"[OK] {name}: {size_orig_kb:.1f}KB -> {size_webp_kb:.1f}KB WebP ({saving:.1f}% ahorro)")

    except Exception as e:
        print(f"[ERROR] No se pudo procesar {src_path}: {e}", file=sys.stderr)

def main():
    print("===============================================================")
    print(" INICIANDO CONVERSIÓN DE IMÁGENES A WEBP — ISTPET BAR")
    print("===============================================================\n")

    setup_directories()

    valid_extensions = (".jpg", ".jpeg", ".png", ".webp")
    sources = [f for f in SOURCE_DIR.iterdir() if f.suffix.lower() in valid_extensions]

    if not sources:
        print(f"No se encontraron imágenes en {SOURCE_DIR}")
        sys.exit(1)

    print(f"Procesando {len(sources)} imágenes gastronómicas reales...\n")
    for src in sorted(sources):
        process_image(src)

    print("\n===============================================================")
    print(" CONVERSIÓN A WEBP COMPLETADA CON ÉXITO")
    print("===============================================================")

if __name__ == "__main__":
    main()
