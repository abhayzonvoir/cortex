<?php

declare(strict_types=1);

return [

    // Tenants Database Tables
    'tables' => [

        'tenants' => 'tenants',
        'tenantables' => 'tenantables',

    ],

    // Tenants Models
    'models' => [
        'tenant' => \Cortex\Tenants\Models\Tenant::class,
    ],

];
