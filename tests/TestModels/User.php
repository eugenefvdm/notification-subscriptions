<?php

namespace Eugenefvdm\NotificationSubscriptions\Tests\TestModels;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Eugenefvdm\NotificationSubscriptions\Traits\HasNotificationSubscriptions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Notifiable, HasFactory;
    use HasNotificationSubscriptions;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
} 