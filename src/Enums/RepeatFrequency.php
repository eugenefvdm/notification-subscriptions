<?php

namespace Eugenefvdm\NotificationSubscriptions\Enums;

enum RepeatFrequency: string
{
    case Hourly = 'hourly';
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Yearly = 'yearly';
} 