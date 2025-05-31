<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->default('default');
            $table->string('notification_class')->unique();
            $table->string('repeat_frequency')->nullable(); // E.g. daily, weekly, etc. See Enum
            $table->integer('repeat_interval')->nullable(); // E.g. 1, 2, 3, etc.
            $table->integer('max_repeats')->nullable();
            $table->timestamp('initial_delay')->nullable(); // Carbon instance e.g. now()->addDays(4)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
}; 