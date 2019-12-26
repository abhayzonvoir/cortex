<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;

class CortexTenantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $abilities = [
            ['name' => 'list', 'title' => 'List tenants', 'entity_type' => 'tenant'],
            ['name' => 'import', 'title' => 'Import tenants', 'entity_type' => 'tenant'],
            ['name' => 'create', 'title' => 'Create tenants', 'entity_type' => 'tenant'],
            ['name' => 'update', 'title' => 'Update tenants', 'entity_type' => 'tenant'],
            ['name' => 'delete', 'title' => 'Delete tenants', 'entity_type' => 'tenant'],
            ['name' => 'audit', 'title' => 'Audit tenants', 'entity_type' => 'tenant'],
        ];

        collect($abilities)->each(function (array $ability) {
            app('cortex.auth.ability')->create($ability);
        });
    }
}
