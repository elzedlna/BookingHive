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
        Schema::table('hotels', function (Blueprint $table) {
        $table->string('category_id')->after('name'); // Add category
        $table->string('city')->after('address');  // Add city
        $table->string('state')->after('city');    // Add state
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
        $table->dropColumn(['category_id', 'city', 'state']);
        });
    }
};
