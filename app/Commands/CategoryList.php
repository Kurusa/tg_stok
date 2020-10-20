<?php

namespace App\Commands;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Services\Status\UserStatusService;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class CategoryList extends BaseCommand
{

    function processCommand($text = false)
    {
        if (!$this->user->is_active) {
            $inline_keyboard_array = [];
            $inline_keyboard_array[] = [
                [
                    'text' => $this->text['left_application'],
                    'callback_data' => json_encode([
                        'a' => 'left_order',
                    ])
                ]
            ];
            $keyboard = new InlineKeyboardMarkup($inline_keyboard_array);

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['pre_active_message'], $keyboard);
        } else {
            $this->user->status = UserStatusService::DONE;
            $this->user->save();

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

            $keyboard = new InlineKeyboardMarkup($inline_keyboard_array);
            if ($update) {
                $this->getBot()->editMessageText($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId(), $this->text['select_category'], 'html', false,  $keyboard);
            } else {
                $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['select_category'], $keyboard);
            }
        }
    }

}