<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subsidies', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->text('title')->nullable();
            $table->string('provider')->nullable();
            $table->string('recipient_ico')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('program')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->unsignedSmallInteger('year')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subsidies');
    }
};
