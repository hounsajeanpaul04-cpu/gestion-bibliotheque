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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // clé étrangère vers users
            $table->foreignId('book_id')->constrained()->onDelete('cascade'); // clé étrangère vers books
            $table->enum('status', ['pending', 'active', 'cancelled'])->default('pending');
            $table->integer('position')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
        $table->dropColumn('position');
    });
        Schema::dropIfExists('reservations');
    }
};
