<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_progresses', function (Blueprint $table) {
            $table->id();
            $table->uuid('run_id')->unique();
            $table->string('job');
            $table->string('status')->default('running'); // running, completed, failed
            $table->unsignedInteger('current')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_progresses');
    }
};
