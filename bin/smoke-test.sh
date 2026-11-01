#!/usr/bin/env bash
# تست سلامت نصب پارت‌یاب روی یک سایت وردپرس موجود.
#
#   bash bin/smoke-test.sh --path=/var/www/html
#
# معادل: wp partyab verify
set -euo pipefail

WP_PATH=""
for arg in "$@"; do
	case "$arg" in
		--path=*) WP_PATH="${arg#--path=}" ;;
	esac
done

command -v wp >/dev/null 2>&1 || { echo "WP-CLI نصب نیست. راهنما: https://wp-cli.org" >&2; exit 1; }

WP=(wp)
[[ -n "$WP_PATH" ]] && WP+=("--path=$WP_PATH")

"${WP[@]}" core is-installed >/dev/null 2>&1 || { echo "این مسیر یک نصب وردپرس نیست: ${WP_PATH:-$(pwd)}" >&2; exit 1; }

if ! "${WP[@]}" plugin is-active partyab-fitment >/dev/null 2>&1; then
	echo "افزونهٔ partyab-fitment فعال نیست. اول: wp plugin activate partyab-fitment" >&2
	exit 1
fi

exec "${WP[@]}" partyab verify
