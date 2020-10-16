<?php

namespace App\Commands;

use App\Services\Status\UserStatusService;

class Cancel extends BaseCommand
{

    function processCommand()
    {
        switch ($this->user->status) {
            case UserStatusService::EQUIPMENT_TYPE:
                $this->triggerCommand(MainMenu::class);
                break;
        }
    }

}