<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_template_id')->constrained('notification_templates');
            $table->foreignId('user_id')->constrained('users');
            $table->string('notification_class')->nullable();
            
            // Polymorphic relationship for model-specific notifications
            $table->nullableMorphs('notifiable');
            
            $table->uuid('uuid');
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('count')->default(0);
            $table->timestamp('scheduled_at')->nullable(); // Next scheduled send time
            $table->timestamps();
            
            // Unique constraint for user and template combination (per notifiable if present)
            $table->unique(['user_id', 'notification_template_id', 'notifiable_type', 'notifiable_id'], 'unique_notification_subscription');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_subscriptions');
    }
}; 