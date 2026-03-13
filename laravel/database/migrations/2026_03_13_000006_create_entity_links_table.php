<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained()->cascadeOnDelete();
            $table->string('linked_type')->index();
            $table->unsignedBigInteger('linked_id');
            $table->string('role');
            $table->text('evidence')->nullable();
            $table->timestamps();

            $table->unique(['entity_id', 'linked_type', 'linked_id', 'role']);
            $table->index(['linked_type', 'linked_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_links');
    }
};
