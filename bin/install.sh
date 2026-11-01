#!/usr/bin/env bash
# نصب خودکار پارت‌یاب روی یک نصب موجود وردپرس.
#
#   bash bin/install.sh --path=/var/www/html
#   bash bin/install.sh --path=/var/www/html --currency=IRR --no-images
#
# پیش‌نیاز: WP-CLI (https://wp-cli.org) + PHP + دسترسی به پوشهٔ وردپرس.
set -euo pipefail

WP_PATH="${WP_PATH:-}"
PASSTHRU=()

for arg in "$@"; do
	case "$arg" in
		--path=*) WP_PATH="${arg#--path=}" ;;
		*) PASSTHRU+=("$arg") ;;
	esac
done

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

say() { printf '\033[1;36m%s\033[0m\n' "$*"; }
die() { printf '\033[1;31m%s\033[0m\n' "$*" >&2; exit 1; }

command -v wp >/dev/null 2>&1 || die "WP-CLI نصب نیست. راهنما: https://wp-cli.org"

WP=(wp)
[[ -n "$WP_PATH" ]] && WP+=("--path=$WP_PATH")

"${WP[@]}" core is-installed >/dev/null 2>&1 || die "این مسیر یک نصب وردپرس نیست: ${WP_PATH:-$(pwd)}"

# ۱) ساخت زیپ‌ها اگر نیستند
if [[ ! -f "$ROOT/dist/partyab.zip" || ! -f "$ROOT/dist/partyab-fitment.zip" ]]; then
	say "ساخت بسته‌های نصب…"
	bash "$ROOT/bin/build-zips.sh"
fi

# ۲) نصب ووکامرس در صورت نیاز
if ! "${WP[@]}" plugin is-installed woocommerce >/dev/null 2>&1; then
	say "نصب ووکامرس…"
	"${WP[@]}" plugin install woocommerce --activate --quiet || say "نصب ووکامرس انجام نشد؛ دستی نصب کنید."
fi
"${WP[@]}" plugin is-active woocommerce >/dev/null 2>&1 || "${WP[@]}" plugin activate woocommerce --quiet || true

# ۳) نصب قالب و افزونه از بسته‌های همین مخزن
if ! "${WP[@]}" theme is-installed partyab >/dev/null 2>&1; then
	say "نصب قالب پارت‌یاب…"
	"${WP[@]}" theme install "$ROOT/dist/partyab.zip" --quiet
fi

if ! "${WP[@]}" plugin is-installed partyab-fitment >/dev/null 2>&1; then
	say "نصب افزونهٔ سازگاری…"
	"${WP[@]}" plugin install "$ROOT/dist/partyab-fitment.zip" --quiet
fi

say "فعال‌سازی قالب و افزونه…"
"${WP[@]}" theme activate partyab --quiet
"${WP[@]}" plugin activate partyab-fitment --quiet

# ۴) اجرای نصب خودکار
say "اجرای نصب خودکار (دادهٔ نمونه، صفحه‌ها، منوها، تنظیمات ووکامرس)…"
"${WP[@]}" eval-file "$ROOT/bin/install.php" -- "${PASSTHRU[@]+"${PASSTHRU[@]}"}"

say "تمام شد. آدرس سایت را در پنجرهٔ ناشناس باز کنید."
