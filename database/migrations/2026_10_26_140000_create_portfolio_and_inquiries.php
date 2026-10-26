<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', fn (Blueprint $table) => $table->boolean('is_admin')->default(false));
        Schema::create('projects', function (Blueprint $table) {
            $table->id(); $table->string('title'); $table->string('slug')->unique();
            $table->string('category'); $table->text('summary');
            $table->text('goal')->nullable(); $table->text('problem')->nullable();
            $table->text('scope')->nullable(); $table->text('solution')->nullable(); $table->text('result')->nullable();
            $table->string('relationship')->default('unverified');
            $table->string('delivery_status')->default('in_development');
            $table->string('cover')->nullable(); $table->string('cover_alt')->nullable();
            $table->string('seo_title')->nullable(); $table->text('seo_description')->nullable();
            $table->boolean('approved_for_publication')->default(false);
            $table->boolean('is_published')->default(false); $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id(); $table->string('name',100); $table->string('email');
            $table->string('company',150)->nullable(); $table->string('phone',40)->nullable();
            $table->text('message'); $table->boolean('is_read')->default(false); $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('inquiries'); Schema::dropIfExists('projects');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_admin'));
    }
};
