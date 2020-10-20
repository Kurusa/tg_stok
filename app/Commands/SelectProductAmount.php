<?php

namespace App\Commands;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class SelectProductAmount extends BaseCommand
{

    function processCommand()
    {
        $callback_data = \GuzzleHttp\json_decode($this->update->getCallbackQuery()->getData(), true);
        $inline_keyboard_array = [];

        $amount = Product::where('id', $callback_data['id'])->first()->amount;
        for ($i = 1; $i <= ($amount > 50 ? 50 : $amount) ; $i++) {
            if ($i % 2) {
                $inline_keyboard_array[] = [
                    [
                        'text' => $i,
                        'callback_data' => json_encode([
                            'a' => 'prod_am',
                            'p_id' => $callback_data['id'],
                            'am' => $i
                        ])
                    ], [
                        'text' => $i+1,
                        'callback_data' => json_encode([
                            'a' => 'prod_am',
                            'p_id' => $callback_data['id'],
                            'am' => $i+1
                        ])
                    ],
                ];
            }
        }

        $order = Order::where('user_id', $this->user->id)->where('status', 'NEW')->first();
        $order_products = OrderProduct::where('order_id', $order->id)->get();
        if ($order_products->count()) {
            $inline_keyboard_array[] = [
                [
                    'text' => $this->text['cart'],
                    'callback_data' => json_encode([
                        'a' => 'cart',
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
        $this->getBot()->editMessageText($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId(), $this->text['select_amount'], 'html', false, $keyboard);
    }

}