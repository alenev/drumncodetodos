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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user')->nullable();
            $table->integer('id_parent_todo')->default(0);
            $table->integer('id_status')->default(1);
            $table->integer('priority')->default(1);
            $table->string('title')->nullable();
            $table->longText('description')->nullable();  
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
