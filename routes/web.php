<?php

use Eugenefvdm\NotificationSubscriptions\Http\Controllers\UnsubscribeController;
use Illuminate\Support\Facades\Route;

Route::get('/notifications/unsubscribe/{uuid}', UnsubscribeController::class)
    ->name('notifications.unsubscribe');