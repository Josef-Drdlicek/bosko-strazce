<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->text('subject')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('currency', 3)->default('CZK');
            $table->date('date_signed')->nullable()->index();
            $table->string('publisher_ico')->nullable();
            $table->string('publisher_name')->nullable();
            $table->string('counterparty_ico')->nullable();
            $table->string('counterparty_name')->nullable();
            $table->text('source_url')->nullable();
            $table->longText('fulltext')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
