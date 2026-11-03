# Audit

> ممیزی ضدتقلب با Hash Chain — ثبت غیرقابل‌انکار تغییرات حساس.

| فیلد | مقدار |
|------|------|
| Engine ID | `tavaan.engine.audit` |
| DOC-ID | `DOC-058` |
| Namespace | `Tavaan\Engine\Audit` |
| اولویت | P1 (فاز M4) |
| Facade | `Audit` (accessor: `audit`) |
| مخزن | https://github.com/andrbi106/Tavaan-Engine-Audit |
| مستندات | https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/audit.md

## استفاده

```php
use Tavaan\Engine\Audit\AuditManager;
use Tavaan\Engine\Audit\AuditEvent;

$audit = new AuditManager(auditKey: 'secret-key');

// ثبت رویداد
$audit->record(new AuditEvent(
    name: 'updated',
    auditableType: 'User',
    auditableId: 1,
    userId: 42,
    oldValues: ['name' => 'Ali'],
    newValues: ['name' => 'Sara'],
    ip: '192.168.1.1',
    userAgent: 'Mozilla/5.0',
));

// جستجو
$logs = $audit->forAuditable('User', 1);   // تمام رکوردهای User:1
$logs = $audit->forUser(42);               // تمام رکوردهای user_id=42
$all = $audit->all();

// تأیید زنجیره هش (tamper-evident)
$valid = $audit->verifyChain();   // true اگر هیچ رکوردی دستکاری نشده باشد
```

## مستندات

[docs/03-engines/audit.md](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/audit.md)

---
*Generated on 2026-11-03.*
