<?php

namespace Eugenefvdm\NotificationSubscriptions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
} 