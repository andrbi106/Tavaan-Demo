# ساختار manifest.json و JSON Schemaها

## manifest.json (فهرست ماشین‌خوان)

```json
{
  "$schema": "https://docs.tavaan.dev/schemas/docs-manifest.v1.json",
  "framework": {
    "name": "Tavaan",
    "name_fa": "تَوان",
    "version": "1.0.0",
    "php_min": "8.2.0",
    "dependency_policy": "zero-external",
    "architectures": ["layered", "hmvc", "modular-monolith", "event-driven", "hexagonal"]
  },
  "generated_note": "فهرست ماشین‌خوان همهٔ مستندات",
  "totals": {"documents": N, "engines": N, "schemas": 4, "adrs": N},
  "entry_points": {
    "index": "docs/README.md",
    "readme": "README.md",
    "quickstart": "docs/00-overview/quickstart.md",
    "engine_registry": "docs/03-engines/README.md",
    "adr_index": "docs/adr/README.md"
  },
  "reading_paths": {
    "engine_builder": ["DOC-010", "DOC-011", ...],
    "architect": ["DOC-001", "DOC-002", ...],
    "app_builder": [...],
    "qa_reviewer": [...]
  },
  "engines": [
    {"engine_id": "tavaan.engine.<name>", "slug": "<name>", "namespace": "...", "priority": "P0", "phase": "M1", "doc": "docs/03-engines/<name>.md"}
  ],
  "catalog": [
    {"section": "...", "section_label": "...", "doc_id": "DOC-XXX", "title": "...", "path": "...", "machine_readable": false, "sha256": "<16 hex>"}
  ]
}
```

## ۴ JSON Schema

### module.v1.json (اعتبارسنجی module.json)
اجباری: `id` (regex `^[a-z0-9-]+\.[a-z0-9-]+$`), `name`, `version` (SemVer), `autoload.namespace-map`, `providers` (minItems 1)
اختیاری: `title_fa`, `type`, `orm_style`, `requires`, `conflicts`, `priority`, `routes`, `views`, `lang`, `migrations`, `config`, `permissions`, `roles`, `database`, `health`, `extractable`, `install`

### project.v1.json (اعتبارسنجی tavaan.json)
اجباری: `name` (regex `^[a-z0-9-]+$`), `version`, `tavaan` (SemVer constraint), `php` (default `>=8.2.0`)
اختیاری: `environment`, `architecture`, `paths`, `engines`, `modules`, `kits`, `providers`, `scripts`, `build`, `security`

### integration-event.v1.json (اعتبارسنجی رویداد بین‌سرویسی)
اجباری: `event` (dotted pattern), `version` (≥1), `producer`, `payload_schema`, `delivery` (at-most-once/at-least-once/exactly-once)
اختیاری: `consumers`, `compatibility`, `ordering`, `retention_days`, `envelope` (CloudEvents-style)

### docs-manifest.v1.json (اعتبارسنجی خودِ manifest.json)
اجباری: `framework`, `totals`, `entry_points`, `engines`, `catalog`

## تولید manifest

```bash
python3 tools/scripts/build-manifest.py
```
این اسکریپت:
1. همهٔ فایل‌های `.md` را پیمایش می‌کند
2. DOC-ID را با regex استخراج می‌کند
3. sha256[:16] محاسبه می‌کند
4. بلوک `machine-readable:` را بررسی می‌کند
5. برای موتورها: slug, namespace, priority, phase را از YAML استخراج می‌کند
6. manifest.json کامل تولید می‌کند
