<?php

namespace App\Config;

enum ParseState: string
{
    /**
     * Cancelled by client.
     */
    case Cancelled = 'cancelled';

    /**
     * Queued for pickup.
     */
    case Queued = 'queued';

    /**
     * Picked up.
     */
    case RequestedByParser = 'requested';

    /**
     * Reported complete.
     */
    case FinishedByParser = 'finished';
}
