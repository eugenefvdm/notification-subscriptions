<?php

namespace Eugenefvdm\NotificationSubscriptions\Models;

use Eugenefvdm\NotificationSubscriptions\Enums\RepeatFrequency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class NotificationSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_template_id',
        'user_id',
        'notification_class',
        'notifiable_type',
        'notifiable_id',
        'subscribed',
        'uuid',
        'unsubscribed_at',
        'sent_at',
        'count',
        'scheduled_at',
    ];

    protected $casts = [
        'subscribed' => 'boolean',
        'unsubscribed_at' => 'datetime',
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (empty($subscription->uuid)) {
                $subscription->uuid = (string) Str::uuid();
            }
        });
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'notification_template_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    // Check if this subscription is active
    public function isActive(): bool
    {
        return !$this->isMaxCountReached() && !$this->unsubscribed_at;
    }

    // Check if the max count has been reached
    public function isMaxCountReached(): bool
    {           
        return $this->template->max_repeats !== null && $this->count >= $this->template->max_repeats;
    }

    // Check if the subscription can be unsubscribed
    public function canBeUnsubscribed(): bool
    {
        return !$this->template || !$this->template->isSystem();
    }

    /**
     * Get the notifiable entity for this subscription
     */
    public function getNotifiable()
    {
        if ($this->notifiable_type && $this->notifiable_id) {
            return $this->notifiable;
        }
        
        return null;
    }

    // Unsubscribe from this notification
    public function unsubscribe(): self
    {
        if ($this->canBeUnsubscribed()) {            
            $this->unsubscribed_at = now();
            $this->save();
        }

        return $this;
    }

    // Generate unsubscribe URL
    public function unsubscribeUrl(): string
    {
        return route('notifications.unsubscribe', $this->uuid);
    }

    // Increment the sent count
    public function incrementCount(): self
    {
        $this->count++;
        $this->sent_at = now();
        
        // If this is a repeatable notification, schedule the next one
        if ($this->template->isRepeatable() && !$this->isMaxCountReached()) {
            $interval = $this->template->repeat_interval ?? 1;
            
            $this->scheduled_at = match($this->template->repeat_frequency) {
                RepeatFrequency::Hourly => now()->addHours($interval),
                RepeatFrequency::Daily => now()->addDays($interval),
                RepeatFrequency::Weekly => now()->addWeeks($interval),
                RepeatFrequency::Monthly => now()->addMonths($interval),
                RepeatFrequency::Yearly => now()->addYears($interval),
                default => null,
            };
        }
        
        $this->save();
        
        return $this;
    }
} 