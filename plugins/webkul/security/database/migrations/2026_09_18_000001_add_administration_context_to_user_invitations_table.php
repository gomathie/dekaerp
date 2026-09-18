<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_invitations', function (Blueprint $table): void {
            $table->foreignId('inviter_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('default_company_id')
                ->nullable()
                ->after('email')
                ->constrained('companies')
                ->nullOnDelete();
            $table->json('company_ids')->nullable()->after('default_company_id');
            $table->json('role_ids')->nullable()->after('company_ids');
        });
    }

    public function down(): void
    {
        Schema::table('user_invitations', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('inviter_id');
            $table->dropConstrainedForeignId('default_company_id');
            $table->dropColumn(['company_ids', 'role_ids']);
        });
    }
};
