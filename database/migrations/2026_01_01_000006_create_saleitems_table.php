<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saleitems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saleid')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('productid')->constrained('products')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamp('createdat')->nullable();
            $table->timestamp('updatedat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saleitems');
    }
};
