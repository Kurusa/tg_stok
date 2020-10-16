<?php

namespace App\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class ProductList extends BaseCommand
{

    function processCommand($text = false)
    {
        $inline_keyboard_array = [];
        $callback_data = \GuzzleHttp\json_decode($this->update->getCallbackQuery()->getData(), true);

        foreach (Product::where('category_id', $callback_data['id'])->where('subcategory_id', $callback_data['sub_id'])->get() as $product) {
            $inline_keyboard_array[] = [
                [
                    'text' => $product->title,
                    'callback_data' => json_encode([
                        'a' => 'product',
                        'id' => $product->id,
                        'cat_id' => $callback_data['id'],
                        'sub_id' => $callback_data['sub_id']
                    ])
                ]
            ];
        }
        $inline_keyboard_array[] = [
            [
                'text' => $this->text['back'],
                'callback_data' => json_encode([
                    'a' => 'product_back',
                    'id' => $callback_data['id'], // category id
                ])
            ]
        ];

        $keyboard = new InlineKeyboardMarkup($inline_keyboard_array);
        $this->getBot()->editMessageText($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId(), $this->text['select_product'], 'html', false,  $keyboard);
    }

}