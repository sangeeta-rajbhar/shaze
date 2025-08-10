<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // Primary key: Auto-incrementing BIGINT

            // Personal Info
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();            
            $table->string('phone_number')->unique();     
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('anniversary')->nullable();

            // Communication Preferences
            $table->boolean('allow_promotional_communication')->default(false);
            $table->boolean('allow_transactional_communication')->default(true);
            $table->string('communication_channels')->nullable();

            // Address Info
            $table->string('address_line')->nullable();
            $table->string('address_landmark')->nullable();
            $table->string('country_code', 5)->nullable();

            // Other
            $table->string('invoice_type')->nullable(); 
            $table->string('status')->default(1);

            // Audit Fields
            $table->string('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
}
