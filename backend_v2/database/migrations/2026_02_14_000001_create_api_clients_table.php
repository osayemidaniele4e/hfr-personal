<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('api_clients')) {
            return;
        }

        Schema::create('api_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('organisation')->nullable();
            $table->string('api_key', 64)->unique();
            $table->string('api_key_hash', 128);
            $table->unsignedInteger('rate_limit')->default(60); // requests per minute
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('api_key');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_clients');
    }
};
