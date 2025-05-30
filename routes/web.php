<?php

use Eugenefvdm\NotificationSubscriptions\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/notifications/unsubscribe/{uuid}', [NotificationController::class, 'unsubscribe'])
->name('notifications.unsubscribe');