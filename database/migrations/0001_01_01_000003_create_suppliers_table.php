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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('name');
            $table->string('primary_contact')->nullable();
            $table->string('location')->nullable();
            $table->text('material_certifications')->nullable();
            $table->date('last_audit_date')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();

            // Helps import determinism: a supplier should be unique by name.
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};

