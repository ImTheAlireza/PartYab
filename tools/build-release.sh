#!/usr/bin/env bash
# ساخت همه بسته‌های قابل نصب.
#   bash tools/build-release.sh          # بسته سبک (المنتور از مخزن وردپرس نصب می‌شود)
#   bash tools/build-release.sh --full   # بسته کامل (Elementor و Pro داخل خودش هستند)
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
FULL="no"
[ "${1:-}" = "--full" ] && FULL="yes"

DIST="$ROOT/dist"
REL="$ROOT/releases"
rm -rf "$DIST/quickstart" && mkdir -p "$DIST" "$REL"

echo "▸ بسته‌بندی قالب و افزونه سازگاری"
bash "$ROOT/tools/package-theme.sh" >/dev/null
bash "$ROOT/tools/package-plugin.sh" partyab-compat >/dev/null

echo "▸ ساخت اسکلت نصب‌کننده"
QS="$DIST/quickstart/partyab-quickstart"
mkdir -p "$QS"
cp -r "$ROOT/plugins/partyab-quickstart/." "$QS/"
mkdir -p "$QS/vendor" "$QS/vendor/demo-images"

cp "$DIST/part-yab.zip" "$QS/vendor/"
cp "$DIST/partyab-compat.zip" "$QS/vendor/"
cp "$ROOT/data/compat-sample.csv" "$QS/vendor/"
cp "$ROOT/data/demo-images/"*.jpg "$QS/vendor/demo-images/"
mkdir -p "$QS/vendor/demo-images/cut"
cp "$ROOT/data/demo-images/cut/"*.webp "$QS/vendor/demo-images/cut/"

if [ "$FULL" = "yes" ]; then
  echo "▸ افزودن Elementor و Elementor Pro به بسته (حالت کامل)"
  for z in "$ROOT"/Elementor*.zip "$ROOT"/elementor-pro*.zip; do
    [ -e "$z" ] && cp "$z" "$QS/vendor/" && echo "   + $(basename "$z")"
  done
fi

echo "▸ زیپ کردن نصب‌کننده"
cd "$DIST/quickstart"
NAME="partyab-quickstart"
[ "$FULL" = "yes" ] && NAME="partyab-quickstart-full"
rm -f "$REL/$NAME.zip"
zip -qr "$REL/$NAME.zip" "partyab-quickstart" -x "*/.DS_Store"

# کپی بقیه بسته‌ها برای دانلود مستقیم
cp "$DIST/part-yab.zip" "$REL/"
cp "$DIST/partyab-compat.zip" "$REL/"

echo
echo "▸ آماده در releases/ :"
ls -lh "$REL" | tail -n +2 | awk '{print "   " $9 "  (" $5 ")"}'
