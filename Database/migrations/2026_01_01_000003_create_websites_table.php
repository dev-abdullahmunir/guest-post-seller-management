<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table: websites (Vendor Inventory of Available Guest Post Websites)
     */
    public function up(): void
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->string('website_name');
            $table->string('website_url');
            $table->string('niche');
            $table->integer('domain_authority')->nullable();
            $table->integer('domain_rating')->nullable();
            $table->bigInteger('traffic')->nullable();
            $table->decimal('vendor_price', 10, 2);
            $table->integer('turnaround_days')->nullable();
            $table->enum('status', ['active', 'inactive', 'paused'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vendor_id')->references('id')->on('sellers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
