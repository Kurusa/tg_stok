<?php

namespace App\Commands;

use App\Models\Category;
use App\Models\SubCategory;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class SubCategoryList extends BaseCommand
{

    function processCommand($text = false)
    {
        $inline_keyboard_array = [];
        $callback_data = \GuzzleHttp\json_decode($this->update->getCallbackQuery()->getData(), true);

        foreach (SubCategory::where('category_id', $callback_data['id'])->get() as $subcategory) {
            $inline_keyboard_array[] = [
                [
                    'text' => $subcategory->title,
                    'callback_data' => json_encode([
                        'a' => 'subcat',
                        'sub_id' => $subcategory->id,
                        'id' => $callback_data['id']
                    ])
                ]
            ];
        }
        $inline_keyboard_array[] = [
            [
                'text' => $this->text['back'],
                'callback_data' => json_encode([
                    'a' => 'subcat_back'
                ])
            ]
        ];

        $keyboard = new InlineKeyboardMarkup($inline_keyboard_array);
        $this->getBot()->editMessageText($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId(), $this->text['select_subcategory'], 'html', false,  $keyboard);
    }

}