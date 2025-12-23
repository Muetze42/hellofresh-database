<?php

namespace App\Enums;

enum QueueEnum: string
{
    case Default = 'default';
    case HelloFresh = 'hellofresh';
    case Import = 'import';
    case Long = 'long';
}
