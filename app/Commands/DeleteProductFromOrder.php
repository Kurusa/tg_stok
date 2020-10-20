<?php

namespace App\Commands;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class DeleteProductFromOrder extends BaseCommand
{

    function processCommand()
    {
        $callback_data = \GuzzleHttp\json_decode($this->update->getCallbackQuery()->getData(), true);

        OrderProduct::where('id', $callback_data['id'])->delete();
        $this->getBot()->deleteMessage($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId());
    }
}