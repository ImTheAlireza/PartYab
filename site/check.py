#!/usr/bin/env python3
"""درگاه کیفیت صفحات استاتیک: ساختار HTML، پیوندها، آیکونها و تگهای تک H1."""
from __future__ import annotations

import pathlib
import re
from html.parser import HTMLParser

ROOT = pathlib.Path(__file__).resolve().parent
VOID = {"area", "base", "br", "col", "embed", "hr", "img", "input", "link",
        "meta", "param", "source", "track", "wbr", "use", "path", "circle",
        "rect", "ellipse", "polyline", "polygon", "stop", "line", "feTurbulence",
        "feGaussianBlur", "symbol"}
SELF_CLOSING_OK = {"svg", "use", "path", "circle", "rect", "ellipse", "polyline",
                   "polygon", "stop", "line", "feTurbulence", "feGaussianBlur"}


class Checker(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.stack: list[str] = []
        self.errors: list[str] = []
        self.h1 = 0
        self.links: list[str] = []
        self.icons: list[str] = []
        self.assets: list[str] = []

    def handle_starttag(self, tag, attrs):
        a = dict(attrs)
        if tag not in VOID and tag not in SELF_CLOSING_OK:
            self.stack.append(tag)
        if tag == "h1":
            self.h1 += 1
        for key in ("href", "src"):
            if key in a:
                self.links.append(a[key])
        if a.get("href", "").startswith("#i-"):
            self.icons.append(a["href"][1:])

    def handle_startendtag(self, tag, attrs):
        a = dict(attrs)
        if tag == "h1":
            self.h1 += 1
        for key in ("href", "src"):
            if key in a:
                self.links.append(a[key])
        if a.get("href", "").startswith("#i-"):
            self.icons.append(a["href"][1:])

    def handle_endtag(self, tag):
        if tag in VOID or tag in SELF_CLOSING_OK:
            return
        if not self.stack:
            self.errors.append(f"closing </{tag}> without opener")
            return
        if self.stack[-1] == tag:
            self.stack.pop()
            return
        if tag in self.stack:
            while self.stack and self.stack[-1] != tag:
                self.errors.append(f"unclosed <{self.stack.pop()}> before </{tag}>")
            self.stack.pop()
        else:
            self.errors.append(f"stray </{tag}>")


def main() -> None:
    sprite = (ROOT / "assets/partials/sprite.html").read_text(encoding="utf-8")
    symbol_ids = set(re.findall(r'<symbol id="(i-[\w-]+)"', sprite))
    failures = 0

    for page in sorted(ROOT.glob("*.html")):
        if page.name == "styleguide.html":
            continue
        src = page.read_text(encoding="utf-8")
        c = Checker()
        c.feed(src)
        problems = list(c.errors)
        if c.stack:
            problems.append("unclosed at EOF: " + ", ".join(c.stack[-5:]))
        if c.h1 != 1:
            problems.append(f"h1 count = {c.h1}")
        for ref in c.links:
            if ref.startswith(("#", "tel:", "mailto:", "data:", "http")):
                continue
            target = ref.split("#")[0].split("?")[0]
            if target and not (ROOT / target).exists():
                problems.append(f"missing file: {ref}")
        missing_icons = set(c.icons) - symbol_ids
        if missing_icons:
            problems.append("missing icons: " + ", ".join(sorted(missing_icons)))
        for asset in ("assets/css/partyab.css", "assets/css/pages.css",
                      "assets/js/partyab.js", "assets/js/pages.js"):
            if asset not in src:
                problems.append(f"missing asset: {asset}")
        if "<title>" not in src or "undefined" in src or "None</" in src:
            problems.append("bad head or template leak")

        if problems:
            failures += 1
            print(f"✗ {page.name}")
            for p in problems[:8]:
                print("   -", p)
        else:
            print(f"✓ {page.name:18} icons={len(set(c.icons)):2} links={len(c.links):3}")

    print("\n" + ("همه صفحات سالم هستند ✓" if not failures else f"{failures} صفحه مشکل دارند ✗"))
    raise SystemExit(1 if failures else 0)


if __name__ == "__main__":
    main()
