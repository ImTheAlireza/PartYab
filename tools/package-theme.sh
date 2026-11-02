#!/usr/bin/env bash
# ساخت بسته قابل آپلود چایلد تم.
# استفاده: bash tools/package-theme.sh [نام-خروجی]
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SRC="$ROOT/theme/part-yab"
NAME="${1:-part-yab}"
OUT="$ROOT/dist/$NAME.zip"

if [ ! -f "$SRC/style.css" ]; then
  echo "خطا: style.css پیدا نشد در $SRC" >&2
  exit 1
fi

mkdir -p "$ROOT/dist"
rm -f "$OUT"

cd "$(dirname "$SRC")"
# فقط فایل‌های لازم؛ فایل‌های توسعه و سیستم حذف می‌شوند.
zip -qr "$OUT" "part-yab" \
  -x "part-yab/.git/*" \
  -x "part-yab/node_modules/*" \
  -x "part-yab/.DS_Store" \
  -x "*/.DS_Store"

echo "ساخته شد: $OUT"
unzip -l "$OUT" | tail -3
