<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->enum('product', ['quail_eggs', 'quail_chicks', 'quail_meat', 'live_quail', 'dressed_quail', 'mixed'])->default('quail_eggs');
            $table->string('quantity');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'processing', 'to_ship', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->string('order_number')->unique()->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->date('preferred_date')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
