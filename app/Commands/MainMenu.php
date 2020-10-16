<?php

namespace App\Commands;

use App\Models\AdminList;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class MainMenu extends BaseCommand
{

    function processCommand($text = false)
    {
        $this->user->status = UserStatusService::DONE;
        $this->user->save();
        $buttons = [];

        $possible_admin_record = AdminList::where('chat_id', $this->user->chat_id)->first();
        if ($possible_admin_record) {
            $buttons[] = [
                'добавить оборудование'
            ];
        } else {
            $buttons[] = [
                'мое оборудование'
            ];
        }

        $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, 'Главное меню', new ReplyKeyboardMarkup($buttons, false, true));
    }

}