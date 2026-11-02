#!/usr/bin/env python3
"""Build the single-file checklist (index.html) from content/*.json + app/template.html."""
import json, glob, pathlib

root = pathlib.Path(__file__).resolve().parent
phases = []
for f in sorted(glob.glob(str(root / 'content' / '*.json'))):
    with open(f, encoding='utf-8') as fh:
        phases.extend(json.load(fh)['phases'])

# sanity checks
ids = []
for p in phases:
    for s in p['sections']:
        for i in s['items']:
            ids.append(i['id'])
assert len(ids) == len(set(ids)), 'duplicate item ids: %s' % [x for x in ids if ids.count(x) > 1]
for p in phases:
    for s in p['sections']:
        for i in s['items']:
            assert i['pr'] in ('crit', 'imp', 'later'), i

data = json.dumps({'phases': phases}, ensure_ascii=False, separators=(',', ':'))
tpl = (root / 'app' / 'template.html').read_text(encoding='utf-8')
out = tpl.replace('__DATA__', data)
(root / 'index.html').write_text(out, encoding='utf-8')

# ---- markdown (print / Notion / GitHub) ----
PR = {'crit': 'بحرانی', 'imp': 'مهم', 'later': 'بعداً'}
def fa(n):
    return ''.join('۰۱۲۳۴۵۶۷۸۹'[int(c)] if c.isdigit() else c for c in str(n))

md = ['# چک‌لیست ساخت قالب وردپرس فروشگاه لوازم یدکی (المنتور)', '',
      f'مجموع: **{fa(len(ids))} کار** در **{fa(len(phases))} قطعه**.', '']
for p in phases:
    n = sum(len(s['items']) for s in p['sections'])
    md += [f"## قطعه {p['num']} — {p['title']}", '',
           f"> {p['goal']}", '',
           f"- خروجی: {p['output']}",
           f"- زمان تقریبی: {p['time']}",
           f"- پیش‌نیاز: {p['depends']}",
           f"- تعداد کار: {fa(n)}", '']
    for s in p['sections']:
        md += [f"### {s['title']}", '']
        for i in s['items']:
            md.append(f"- [ ] **{i['t']}** ({i['id']} · {PR[i['pr']]} · {i['tag']})  \n      {i['n']}")
        md.append('')
(root / 'CHECKLIST.md').write_text('\n'.join(md), encoding='utf-8')

print('phases:', len(phases), '| items:', len(ids),
      '| critical:', sum(1 for p in phases for s in p['sections'] for i in s['items'] if i['pr'] == 'crit'))
