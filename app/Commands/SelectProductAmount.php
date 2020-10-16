<?php

namespace App\Commands;

use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class SelectProductAmount extends BaseCommand
{

    function processCommand()
    {
        $inline_keyboard_array = [];
        $callback_data = \GuzzleHttp\json_decode($this->update->getCallbackQuery()->getData(), true);

        for ($i = 0; $i <= 9; $i++) {
            $inline_keyboard_array[] = [
                [
                    'text' => $i,
                    'callback_data' => json_encode([
                        'a' => 'prod_am',
                        'id' => $callback_data['id'],
                    ])
                ]
            ];
        }
        $inline_keyboard_array[] = [
            [
                'text' => $this->text['back'],
                'callback_data' => json_encode([
                    'a' => 'prod_back',
                    'id' => $callback_data['cat_id'],
                    'sub_id' => $callback_data['sub_id']
                ])
            ]
        ];

        $keyboard = new InlineKeyboardMarkup($inline_keyboard_array);
        $this->getBot()->editMessageText($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId(), $this->text['select_amount'], 'html', false,  $keyboard);
    }

}