<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Company administrators' passwords, encrypted with the application key,
     * so a Super Admin can read one back (user decision, 2026-11-10, chosen
     * over show-once and reset-only after the risk was explained). Kept out
     * of `users` so the value never travels with a user record: exports, API
     * resources, serialized models and logs never see it.
     */
    public function up(): void
    {
        Schema::create('security_user_password_vault', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->text('encrypted_password');
            $table->timestamp('stored_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_user_password_vault');
    }
};
