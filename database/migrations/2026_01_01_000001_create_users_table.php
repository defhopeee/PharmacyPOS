<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['owner', 'attendant'])->default('attendant');
            $table->string('phone')->nullable();
            $table->boolean('active')->default(true);
            $table->string('remembertoken', 100)->nullable();
            $table->timestamp('createdat')->nullable();
            $table->timestamp('updatedat')->nullable();
            $table->softDeletes('deletedat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
