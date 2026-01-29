<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token');
            $table->enum('type', ['change', 'reset'])->default('reset');
            $table->text('new_password')->nullable(); // Only for 'change' type
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['token', 'type', 'expires_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
};
