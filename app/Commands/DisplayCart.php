<?php

namespace App\Commands;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class DisplayCart extends BaseCommand
{

    function processCommand()
    {
        $order = Order::where('user_id', $this->user->id)->where('status', 'NEW')->first();
        $order_products = OrderProduct::where('order_id', $order->id)->get();

        $message = $this->text['order'] . '№' . $order->id . ' от ' . date('m.d.Y', strtotime($order->created_at)) . "\n";

        $sum = 0;
        foreach ($order_products as $order_product) {
            $product = Product::where('id', $order_product->product_id)->first();
            $sum += $order_product->amount * $product->price;
        }
        $message .= '<code>' . $this->text['total'] . $sum . '</code> ₽';

        $this->getBot()->editMessageText($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId(), $message, 'html');

        foreach ($order_products as $key => $order_product) {
            $product = Product::where('id', $order_product->product_id)->first();

            $message = $key + 1 . '. ' . $product->title . "\n";
            $message .= '<code>' . $order_product->amount . '</code> шт. x <code>' . $product->price . '.00</code> ₽ = <code>' . $order_product->amount * $product->price . '.00</code> ₽' . "\n" . "\n";

            $inline_keyboard_array = [];
            $inline_keyboard_array[] = [
                [
                    'text' => $this->text['delete'],
                    'callback_data' => json_encode([
                        'a' => 'delete',
                        'id' => $order_product->id
                    ])
                ]
            ];
            if ($key + 1 == $order_products->count()) {
                $inline_keyboard_array[] = [
                    [
                        'text' => $this->text['finish_order'],
                        'callback_data' => json_encode([
                            'a' => 'finish_order',
                            'id' => $order->id
                        ])
                    ]
                ];
                $inline_keyboard_array[] = [
                    [
                        'text' => $this->text['back'],
                        'callback_data' => json_encode([
                            'a' => 'cart_back',
                        ])
                    ]
                ];
            }
            $keyboard = new InlineKeyboardMarkup($inline_keyboard_array);
            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $message, $keyboard);
        }
    }

}