<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\User;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class AcceptUser extends BaseCommand
{

    function processCommand()
    {
        $callback_data = \GuzzleHttp\json_decode($this->update->getCallbackQuery()->getData(), true);
        User::where('id', $callback_data['id'])->update([
            'is_active' => 1
        ]);
        $processed_user = User::where('id', $callback_data['id'])->first();

        $this->getBot()->editMessageReplyMarkup($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId(), new InlineKeyboardMarkup([]));
        $this->getBot()->sendMessage($processed_user->chat_id, 'complered');
    }

}