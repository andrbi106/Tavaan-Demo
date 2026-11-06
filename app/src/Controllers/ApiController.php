<?php
declare(strict_types=1);
namespace App\Controllers;

class ApiController
{
    public function status(): array
    {
        return ['framework' => 'Tavaan', 'version' => '0.0.1', 'status' => 'running'];
    }

    public function phpInfo(): void
    {
        phpinfo();
    }
}
