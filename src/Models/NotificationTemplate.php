<?php

namespace Eugenefvdm\NotificationSubscriptions\Models;

use Eugenefvdm\NotificationSubscriptions\Enums\RepeatFrequency;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'notification_class',
        'repeat_frequency',
        'repeat_interval',
        'max_repeats',
        'initial_delay',
    ];

    protected $casts = [
        'repeat_frequency' => RepeatFrequency::class,
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(NotificationSubscription::class);
    }

    // A system notification is one where the category is 'system'
    public function isSystem(): bool
    {
        return $this->category === 'system';
    }
    
    // A notification is be repeateable if the repeatable_freqency is set
    public function isRepeatable(): bool
    {        
        return $this->repeat_frequency !== null;
    }

    public function nameWithSpaces(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => preg_replace('/(?<!^)[A-Z]/', ' $0', $this->name)
        );
    }
} 