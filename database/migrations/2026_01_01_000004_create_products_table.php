<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('categoryid')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('supplierid')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('barcode')->nullable()->unique();
            $table->string('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('cost', 12, 2)->default(0);
            $table->integer('quantity')->default(0);
            $table->integer('reorder')->default(10);
            $table->date('expiry')->nullable();
            $table->timestamp('createdat')->nullable();
            $table->timestamp('updatedat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
