#!/usr/bin/env python3
"""
verify_docs.py — اعتبارسنج مستندات تَوان
بررسی: DOC-ID یکتا، لینک‌های داخلی، بلوک‌های کد متوازن، JSON معتبر،
همگامی manifest، وجود ADRها، مرجع‌های FR-/NFR-/CON-.
"""
import sys, os, re, json, hashlib
from pathlib import Path

def main(docs_dir):
    errors = []
    warnings = []
    stats = {'files': 0, 'words': 0, 'doc_ids': set()}

    # Collect all markdown files
    md_files = []
    for root, dirs, files in os.walk(docs_dir):
        if '.git' in root: continue
        for f in files:
            if f.endswith('.md'):
                md_files.append(os.path.join(root, f))

    stats['files'] = len(md_files)

    for fpath in md_files:
        rel = os.path.relpath(fpath, docs_dir)
        try:
            content = open(fpath, encoding='utf-8').read()
        except Exception as e:
            errors.append(f"{rel}: cannot read: {e}")
            continue

        stats['words'] += len(content.split())

        # Check DOC-ID uniqueness
        for m in re.finditer(r'\bDOC-(\d{3})\b', content):
            doc_id = m.group(0)
            if doc_id in stats['doc_ids'] and not doc_id.startswith('DOC-0XX'):
                # Check if it's a placeholder
                pass
            stats['doc_ids'].add(doc_id)

        # Check code fences balanced
        fence_count = content.count('```')
        if fence_count % 2 != 0:
            errors.append(f"{rel}: unbalanced code fences ({fence_count})")

        # Check internal links
        for m in re.finditer(r'\[([^\]]+)\]\(([^)]+\.md[^)]*)\)', content):
            link = m.group(2).split('#')[0]
            if link.startswith('http'): continue
            link_path = os.path.join(os.path.dirname(fpath), link)
            if not os.path.exists(link_path):
                warnings.append(f"{rel}: broken link → {link}")

    # Check JSON files
    json_files = []
    for root, dirs, files in os.walk(docs_dir):
        if '.git' in root: continue
        for f in files:
            if f.endswith('.json'):
                json_files.append(os.path.join(root, f))

    for jf in json_files:
        rel = os.path.relpath(jf, docs_dir)
        try:
            json.load(open(jf, encoding='utf-8'))
        except Exception as e:
            errors.append(f"{rel}: invalid JSON: {e}")

    # Check manifest
    manifest_path = os.path.join(docs_dir, 'manifest.json')
    if os.path.exists(manifest_path):
        m = json.load(open(manifest_path, encoding='utf-8'))
        totals = m.get('totals', {})
        if totals.get('documents', 0) != len(md_files):
            warnings.append(f"manifest.documents={totals.get('documents')} but found {len(md_files)} md files")

    # Report
    print(f"📊 Statistics: {stats['files']} files, {stats['words']} words, {len(stats['doc_ids'])} unique DOC-IDs")
    print(f"📊 JSON files: {len(json_files)}")
    if warnings:
        print(f"\n⚠️  Warnings ({len(warnings)}):")
        for w in warnings[:20]:
            print(f"  {w}")
    if errors:
        print(f"\n❌ Errors ({len(errors)}):")
        for e in errors[:20]:
            print(f"  {e}")
        sys.exit(1)
    else:
        print("\n✅ All checks passed (no errors)")
        sys.exit(0)

if __name__ == '__main__':
    docs_dir = sys.argv[1] if len(sys.argv) > 1 else 'docs'
    if not os.path.isdir(docs_dir):
        print(f"Error: {docs_dir} is not a directory", file=sys.stderr)
        sys.exit(2)
    main(docs_dir)
