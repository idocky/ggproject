<?php

namespace App\Enums;

enum MatchStatus: string
{
    case Planned = 'planned';
    case Ongoing = 'ongoing';
    case Finished = 'finished';
    case Cancelled = 'cancelled';
}
