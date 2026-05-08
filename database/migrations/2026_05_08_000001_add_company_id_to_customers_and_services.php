<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'company_id')) {
                $table->foreignId('company_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('companies')
                    ->cascadeOnDelete();
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'company_id')) {
                $table->foreignId('company_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('companies')
                    ->cascadeOnDelete();
            }
        });

        $defaultCompanyId = Company::query()->value('id');

        if ($defaultCompanyId) {
            DB::table('customers')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
            DB::table('services')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->unique(['company_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'email']);
            $table->unique('email');
        });

        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
        });
    }
};
