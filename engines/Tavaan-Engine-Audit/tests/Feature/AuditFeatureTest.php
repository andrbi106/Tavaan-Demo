<?php

declare(strict_types=1);

namespace Tavaan\Engine\Audit\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Audit\AuditManager;
use Tavaan\Engine\Audit\AuditEvent;
use Tavaan\Engine\Audit\AuditServiceProvider;

final class AuditFeatureTest extends TestCase
{
    public function test_end_to_end_audit_chain(): void
    {
        $provider = new AuditServiceProvider(['key' => 'test-key']);
        $provider->register();
        $audit = $provider->getManager();

        $audit->record(new AuditEvent('created', 'User', 1, userId: 1, newValues: ['name' => 'Ali', 'email' => 'a@b.com']));
        $audit->record(new AuditEvent('updated', 'User', 1, userId: 2, oldValues: ['name' => 'Ali'], newValues: ['name' => 'Sara']));
        $audit->record(new AuditEvent('deleted', 'User', 1, userId: 3, oldValues: ['name' => 'Sara']));

        $this->assertSame(3, $audit->count());
        $this->assertCount(3, $audit->forAuditable('User', 1));
        $this->assertTrue($audit->verifyChain());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/audit.php';
        $this->assertSame('tvn_audit_logs', $config['table']);
    }

    public function test_lang_fa_loads(): void
    {
        $m = require __DIR__ . '/../../lang/fa/audit.php';
        $this->assertArrayHasKey('error_record', $m);
    }
}
