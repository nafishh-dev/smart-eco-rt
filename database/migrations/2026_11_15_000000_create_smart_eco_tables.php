<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bins', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['organik', 'anorganik', 'jelantah'])->unique();
            $table->string('name');
            $table->string('subtitle');
            $table->string('unit');
            $table->decimal('current_weight', 8, 2)->default(0);
            $table->integer('capacity_percent')->default(0);
            $table->enum('status', ['aman', 'hampir_penuh'])->default('aman');
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('hub_id');
            $table->enum('category', ['organik', 'anorganik', 'jelantah']);
            $table->string('description');
            $table->decimal('amount', 8, 2);
            $table->string('unit');
            $table->integer('points_earned')->default(0);
            $table->string('otp_code', 4)->nullable();
            $table->string('citizen_name')->default('Warga RT');
            $table->enum('status', ['pending', 'claimed', 'expired'])->default('claimed');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['warning', 'info']);
            $table->string('title');
            $table->text('message');
            $table->string('bin_category')->nullable();
            $table->integer('capacity_snapshot')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();
        });

        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('hub_id')->unique();
            $table->string('name');
            $table->string('location');
            $table->string('wifi_ssid');
            $table->integer('battery_percent')->default(94);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('bins');
    }
};
