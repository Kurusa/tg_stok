<?php

use App\Services\Status\UserStatusService;

return [
    UserStatusService::ORDER_START => \App\Commands\Contragent::class,
    UserStatusService::MAILING => \App\Commands\Mailing::class,
];