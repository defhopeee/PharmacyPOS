<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('userid')->constrained('users')->cascadeOnDelete();
            $table->string('customer')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->enum('method', ['cash', 'card', 'mpesa'])->default('cash');
            $table->string('mpesareceipt')->nullable();
            $table->timestamp('createdat')->nullable();
            $table->timestamp('updatedat')->nullable();
            $table->softDeletes('deletedat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
