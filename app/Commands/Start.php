<?php

namespace App\Commands;

use App\Commands\RegisterWorker\UserName;
use App\Models\AdminList;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class Start extends BaseCommand
{

    function processCommand($text = false)
    {
        if (!$this->user->is_active) {
            $this->getBot()->sendMessage($this->user->chat_id, $this->text['pre_active_message']);
        }
    }

}