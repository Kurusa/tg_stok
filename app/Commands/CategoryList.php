<?php

namespace App\Commands;

use App\Models\Category;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class CategoryList extends BaseCommand
{

    function processCommand($text = false)
    {
        if (!$this->user->is_active) {
            $this->getBot()->sendMessage($this->user->chat_id, $this->text['pre_active_message']);
        } else {
            $update = false;
            if ($this->update->getCallbackQuery()) {
                $callback_data = \GuzzleHttp\json_decode($this->update->getCallbackQuery()->getData(), true);
                if ($callback_data['a'] == 'subcat_back') {
                    $update = true;
                }
            }

            $inline_keyboard_array = [];
            foreach (Category::all() as $category) {
                $inline_keyboard_array[] = [
                    [
                        'text' => $category->title,
                        'callback_data' => json_encode([
                            'a' => 'category',
                            'id' => $category->id
                        ])
                    ]
                ];
            }

            $keyboard = new InlineKeyboardMarkup($inline_keyboard_array);

            if ($update) {
                $this->getBot()->editMessageText($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId(), $this->text['select_category'], 'html', false,  $keyboard);
            } else {
                $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['select_category'], $keyboard);
            }
        }
    }

}