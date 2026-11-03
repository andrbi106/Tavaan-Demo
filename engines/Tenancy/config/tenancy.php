<?php

declare(strict_types=1);

return [
    /*| استراتژی: shared | per_db | per_schema */
    'strategy' => \getenv('TVN_TENANCY_STRATEGY') !== false ? \getenv('TVN_TENANCY_STRATEGY') : 'shared',

    /*| دامنه‌ی پایه برای subdomain resolution */
    'base_domain' => \getenv('TVN_TENANCY_BASE_DOMAIN') !== false ? \getenv('TVN_TENANCY_BASE_DOMAIN') : 'example.com',

    /*| tenant‌های پیش‌فرض (در startup) */
    'default_tenants' => [],

    /*| Resolver chain: order matters */
    'resolvers' => ['domain', 'header', 'path'],

    /*| تنظیمات resolvers */
    'resolver_config' => [
        'header_name' => 'X-Tenant-ID',
        'path_segment' => 1,
    ],

    /*| تنظیمات quota پیش‌فرض */
    'default_quota' => [
        'orders_per_day' => 1000,
        'storage_mb' => 1024,
    ],
];
