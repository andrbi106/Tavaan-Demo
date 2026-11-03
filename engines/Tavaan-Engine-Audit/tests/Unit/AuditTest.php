<?php

declare(strict_types=1);

namespace Tavaan\Engine\Audit\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Audit\AuditEvent;
use Tavaan\Engine\Audit\AuditLog;
use Tavaan\Engine\Audit\AuditManager;
use Tavaan\Engine\Audit\AuditServiceProvider;

final class AuditTest extends TestCase
{
    public function test_audit_event_changed_fields(): void
    {
        $e = new AuditEvent('updated', 'User', 1, oldValues: ['name' => 'Ali', 'age' => 25], newValues: ['name' => 'Ali', 'age' => 26]);
        $changed = $e->changedFields();
        $this->assertContains('age', $changed);
        $this->assertNotContains('name', $changed);
    }

    public function test_audit_event_added_field(): void
    {
        $e = new AuditEvent('updated', 'User', 1, oldValues: ['a' => 1], newValues: ['a' => 1, 'b' => 2]);
        $this->assertContains('b', $e->changedFields());
    }

    public function test_audit_event_removed_field(): void
    {
        $e = new AuditEvent('updated', 'User', 1, oldValues: ['a' => 1, 'b' => 2], newValues: ['a' => 1]);
        $this->assertContains('b', $e->changedFields());
    }

    public function test_audit_manager_record(): void
    {
        $m = new AuditManager('secret-key');
        $e = new AuditEvent('created', 'User', 1, userId: 42, newValues: ['name' => 'Ali']);
        $log = $m->record($e);
        $this->assertInstanceOf(AuditLog::class, $log);
        $this->assertSame('created', $log->event);
        $this->assertSame('User', $log->auditableType);
        $this->assertSame(1, $log->auditableId);
        $this->assertSame(42, $log->userId);
        $this->assertNotEmpty($log->auditId);
        $this->assertNotEmpty($log->hash);
        $this->assertNotSame($log->prevHash, $log->hash);
    }

    public function test_audit_hash_chain(): void
    {
        $m = new AuditManager('secret-key');
        $m->record(new AuditEvent('created', 'User', 1, newValues: ['name' => 'Ali']));
        $m->record(new AuditEvent('updated', 'User', 1, oldValues: ['name' => 'Ali'], newValues: ['name' => 'Sara']));
        $logs = $m->all();
        $this->assertCount(2, $logs);
        // Second log's prevHash should be first log's hash
        $this->assertSame($logs[0]->hash, $logs[1]->prevHash);
    }

    public function test_audit_verify_chain(): void
    {
        $m = new AuditManager('secret-key');
        $m->record(new AuditEvent('created', 'User', 1));
        $m->record(new AuditEvent('updated', 'User', 1));
        $this->assertTrue($m->verifyChain());
    }

    public function test_audit_for_auditable(): void
    {
        $m = new AuditManager('key');
        $m->record(new AuditEvent('created', 'User', 1));
        $m->record(new AuditEvent('created', 'User', 2));
        $m->record(new AuditEvent('updated', 'User', 1));
        $this->assertCount(2, $m->forAuditable('User', 1));
        $this->assertCount(1, $m->forAuditable('User', 2));
    }

    public function test_audit_for_user(): void
    {
        $m = new AuditManager('key');
        $m->record(new AuditEvent('created', 'User', 1, userId: 42));
        $m->record(new AuditEvent('created', 'User', 2, userId: 99));
        $m->record(new AuditEvent('updated', 'User', 1, userId: 42));
        $this->assertCount(2, $m->forUser(42));
        $this->assertCount(1, $m->forUser(99));
    }

    public function test_audit_count(): void
    {
        $m = new AuditManager('key');
        $this->assertSame(0, $m->count());
        $m->record(new AuditEvent('created', 'User', 1));
        $this->assertSame(1, $m->count());
    }

    public function test_audit_log_properties(): void
    {
        $m = new AuditManager('key');
        $e = new AuditEvent(
            name: 'login',
            auditableType: 'User',
            auditableId: 1,
            userId: 42,
            tenantId: 5,
            oldValues: [],
            newValues: ['ip' => '127.0.0.1'],
            ip: '192.168.1.1',
            userAgent: 'Mozilla/5.0',
            traceId: 'abc123',
        );
        $log = $m->record($e);
        $this->assertSame('login', $log->event);
        $this->assertSame(5, $log->tenantId);
        $this->assertSame('192.168.1.1', $log->ip);
        $this->assertSame('Mozilla/5.0', $log->userAgent);
        $this->assertSame('abc123', $log->traceId);
        $this->assertSame(['ip' => '127.0.0.1'], $log->newValues);
        $this->assertSame([], $log->oldValues);
    }

    public function test_service_provider(): void
    {
        $provider = new AuditServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(AuditManager::class, $provider->getManager());
        $this->assertContains(AuditManager::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/audit.php';
        $this->assertArrayHasKey('key', $config);
        $this->assertArrayHasKey('table', $config);
    }
}
