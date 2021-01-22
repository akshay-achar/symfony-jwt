<?php

namespace App\Constants;

class StatusConstants
{
    const BACKLOG = 1;
    const IN_PROGRESS = 2;
    const COMPLETED = 3;
    const DELETED = 4;

    public static $statusArray = [
        self::BACKLOG => self::BACKLOG, self::IN_PROGRESS => self::IN_PROGRESS, self::COMPLETED => self::COMPLETED, self::DELETED => self::DELETED
    ];
}
