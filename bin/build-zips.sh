#!/usr/bin/env bash
# ساخت بستهٔ نصب وردپرس: قالب و افزونه
# استفاده: bash bin/build-zips.sh
set -euo pipefail

root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
out="$root/dist"
mkdir -p "$out"
rm -f "$out/partyab.zip" "$out/partyab-fitment.zip"

# قالب: ریشهٔ زیپ باید پوشهٔ partyab باشد
cd "$root/theme"
zip -rq "$out/partyab.zip" partyab -x '*.DS_Store' -x '*/.*'

# افزونه: ریشهٔ زیپ باید پوشهٔ partyab-fitment باشد
cd "$root/plugin"
zip -rq "$out/partyab-fitment.zip" partyab-fitment -x '*.DS_Store' -x '*/.*'

echo "ساخته شد:"
ls -lh "$out"/*.zip
