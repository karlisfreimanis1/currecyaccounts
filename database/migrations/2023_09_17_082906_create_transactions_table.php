<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('accountIdFrom')->unsigned();
            $table->integer('accountIdTo')->unsigned();
            $table->decimal('valueFrom', 32,6);
            $table->decimal('valueTo', 32,6)->default(null)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamp('timeCreated')->useCurrent();
            $table->timestamp('timeProcessed')->default(null)->nullable();
            $table->index(['accountIdFrom']);
            $table->index(['accountIdTo']);
            $table->index(['timeProcessed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
