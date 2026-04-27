<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('api_request_logs')) {
            return;
        }

        Schema::create('api_request_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_client_id')->nullable();
            $table->string('method', 10);
            $table->string('endpoint', 500);
            $table->string('ip_address', 45);
            $table->string('user_agent', 500)->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedInteger('response_time_ms')->nullable(); // milliseconds
            $table->json('query_params')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('api_client_id');
            $table->index('created_at');
            $table->index(['api_client_id', 'created_at']);

            $table->foreign('api_client_id')
                ->references('id')
                ->on('api_clients')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_request_logs');
    }
};
