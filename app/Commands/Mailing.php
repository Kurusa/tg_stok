<?php

namespace App\Commands;

use App\Models\User;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardRemove;

class Mailing extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status == UserStatusService::MAILING) {
            $text = $this->update->getMessage()->getText();
            if ($text == 'отменить') {
                $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, 'Отменено', new ReplyKeyboardRemove());
                $this->user->status = UserStatusService::DONE;
                $this->user->save();
            } else {
                foreach (User::all() as $user) {
                    $this->getBot()->sendMessage($user->chat_id, $text, 'html');
                }
                $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, 'Рассылка закончена', new ReplyKeyboardRemove());
                $this->user->status = UserStatusService::DONE;
                $this->user->save();
            }
        } else {
            $this->user->status = UserStatusService::MAILING;
            $this->user->save();

            $admin_list = explode(',', env('ADMIN_CHAT_ID_LIST'));
            if (in_array($this->user->chat_id, $admin_list)) {
                $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, 'Введите текст для рассылки', new ReplyKeyboardMarkup([
                    [
                        'отменить'
                    ]
                ], false, true));
            }
        }
    }

}