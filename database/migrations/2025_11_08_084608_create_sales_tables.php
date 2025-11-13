<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });


        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('so_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('total', 18, 2);
            $table->string('status')->default('draft');
            $table->timestamps();
        });


        Schema::create('sales_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });


        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->date('date');
            $table->decimal('total', 18, 2);
            $table->string('status')->default('unpaid');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('sales_items');
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('customers');
    }
};
