<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->uuid('olaya_request_id')->nullable()->unique();
            $table->string('olaya_status', 16)->nullable()->index();
            $table->unsignedInteger('olaya_attempts')->default(0);
            $table->timestamp('olaya_next_attempt_at')->nullable()->index();
            $table->timestamp('olaya_delivered_at')->nullable();
            $table->unsignedBigInteger('olaya_lead_id')->nullable();
            $table->string('olaya_last_error', 40)->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropUnique(['olaya_request_id']);
            $table->dropIndex(['olaya_status']);
            $table->dropIndex(['olaya_next_attempt_at']);
            $table->dropColumn(['olaya_request_id', 'olaya_status', 'olaya_attempts', 'olaya_next_attempt_at', 'olaya_delivered_at', 'olaya_lead_id', 'olaya_last_error']);
        });
    }
};
