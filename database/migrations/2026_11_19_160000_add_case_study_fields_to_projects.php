<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('technologies')->nullable();
            $table->json('gallery')->nullable();
            $table->json('service_links')->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('projects', fn (Blueprint $table) => $table->dropColumn(['technologies', 'gallery', 'service_links']));
    }
};
