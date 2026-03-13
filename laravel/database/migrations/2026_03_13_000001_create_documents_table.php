<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->text('source_url')->unique();
            $table->text('title');
            $table->string('section')->index();
            $table->date('published_date')->nullable()->index();
            $table->date('valid_until')->nullable();
            $table->string('department')->nullable();
            $table->longText('fulltext')->nullable();
            $table->foreignId('duplicate_of')->nullable()->constrained('documents')->nullOnDelete();
            $table->timestamp('collected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
