<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTenantsTableAddStyleAndSocialColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table(config('rinvex.tenants.tables.tenants'), function (Blueprint $table) {
            $table->schemalessAttributes('social')->after('currency');
            $table->string('style')->before('is_active')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table(config('rinvex.tenants.tables.tenants'), function (Blueprint $table) {
            $table->dropColumn('style');
            $table->dropColumn('social');
        });
    }
}
