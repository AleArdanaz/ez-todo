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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('list_id')->constrained()->onDelete('cascade');
            $table->boolean('completed')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('remind_at')->nullable();
            $table->string('notes')->nullable();
            $table->string('priority')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
