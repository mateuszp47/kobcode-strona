<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $schema = Schema::connection('installflow');
        $schema->create('employees', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('role'); $table->string('email');
            $table->string('phone'); $table->string('availability'); $table->timestamps();
        });
        $schema->create('clients', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('type'); $table->string('email')->nullable();
            $table->string('phone')->nullable(); $table->string('city'); $table->string('address')->nullable();
            $table->text('notes')->nullable(); $table->timestamps();
        });
        $schema->create('contacts', function (Blueprint $table) {
            $table->id(); $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('channel'); $table->text('note'); $table->dateTime('contacted_at');
        });
        $schema->create('offers', function (Blueprint $table) {
            $table->id(); $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->string('title'); $table->string('status'); $table->date('valid_until'); $table->text('notes')->nullable();
            $table->unsignedBigInteger('net_cents'); $table->unsignedBigInteger('vat_cents');
            $table->unsignedBigInteger('gross_cents'); $table->timestamps();
        });
        $schema->create('offer_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->string('description'); $table->unsignedInteger('quantity_hundredths');
            $table->unsignedBigInteger('unit_cents'); $table->unsignedBigInteger('net_cents');
        });
        $schema->create('orders', function (Blueprint $table) {
            $table->id(); $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('offer_id')->nullable()->unique()->constrained('offers')->restrictOnDelete();
            $table->string('title'); $table->string('status'); $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('completed_at')->nullable(); $table->unsignedBigInteger('gross_cents');
            $table->text('notes')->nullable(); $table->timestamps();
        });
        $schema->create('order_events', function (Blueprint $table) {
            $table->id(); $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->text('message'); $table->dateTime('created_at');
        });
    }

    public function down(): void
    {
        foreach (['order_events', 'orders', 'offer_items', 'offers', 'contacts', 'clients', 'employees'] as $table) {
            Schema::connection('installflow')->dropIfExists($table);
        }
    }
};
