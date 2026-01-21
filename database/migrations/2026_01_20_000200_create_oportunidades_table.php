<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('oportunidades', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('source')->nullable();
            $table->string('stage')->nullable();
            $table->enum('status', ['open', 'won', 'lost'])->default('open');
            $table->decimal('value', 10, 2)->nullable();
            $table->unsignedTinyInteger('probability')->default(0); // 0-100
            $table->timestamp('next_action_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedSmallInteger('score')->default(0);
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oportunidades');
    }
};
