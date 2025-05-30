<?php

namespace Flow\Notification\Enums;

enum NotificationTypeEnum: string
{
    case SUCCESS = 'success';
    case DANGER = 'danger';
    case WARNING = 'warning';
    case INFO = 'info';
}
