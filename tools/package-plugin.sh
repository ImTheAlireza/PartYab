#!/usr/bin/env bash
# ساخت بسته قابل آپلود یک افزونه از پوشه plugins/
# استفاده: bash tools/package-plugin.sh partyab-compat
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SLUG="${1:-}"

if [ -z "$SLUG" ]; then
  echo "استفاده: bash tools/package-plugin.sh <plugin-slug>" >&2
  echo "افزونه‌های موجود:" >&2
  ls -1 "$ROOT/plugins" >&2
  exit 1
fi

SRC="$ROOT/plugins/$SLUG"

if [ ! -d "$SRC" ]; then
  echo "خطا: افزونه $SLUG پیدا نشد." >&2
  exit 1
fi

mkdir -p "$ROOT/dist"
OUT="$ROOT/dist/$SLUG.zip"
rm -f "$OUT"

cd "$ROOT/plugins"
zip -qr "$OUT" "$SLUG" -x "*/.DS_Store" -x "*/node_modules/*" -x "*/.git/*"

echo "ساخته شد: $OUT"
unzip -l "$OUT" | tail -3
