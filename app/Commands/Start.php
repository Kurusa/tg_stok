<?php

namespace App\Commands;

use App\Commands\RegisterWorker\UserName;
use App\Models\AdminList;
use App\Models\Category;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class Start extends BaseCommand
{

    function processCommand($text = false)
    {
        if (!$this->user->is_active) {
            $this->getBot()->sendMessage($this->user->chat_id, $this->text['pre_active_message']);
        } else {
            $inline_keyboard_array = [];
            foreach (Category::all() as $category) {
                $inline_keyboard_array[] = [
                    [
                        'text' => $category->title,
                        'callback_data' => json_encode([
                            'a' => 'accept_user',
                            'id' => $this->user->id
                        ])
                    ]
                ];
            }

            $keyboard = new InlineKeyboardMarkup($inline_keyboard_array);
            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['select_category'], $keyboard);
        }
    }

}