<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Category;
use App\Models\User;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

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
        $this->getBot()->sendMessageWithKeyboard($processed_user->chat_id, 'Ваша заявка обработана, приступайте к заказам', new ReplyKeyboardMarkup([
            [
                'Каталог',
                $this->text['cart']
            ]
        ], false, true));
        $this->getBot()->sendMessageWithKeyboard($processed_user->chat_id, $this->text['select_category'], $keyboard);
    }

}