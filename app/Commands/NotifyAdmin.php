<?php

namespace App\Commands;

use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class NotifyAdmin extends BaseCommand
{

    function processCommand()
    {
        $admin_list = explode(',', env('ADMIN_CHAT_ID_LIST'));

        $user_url = '<a href="tg://user?id=' . $this->user->chat_id . '">' . $this->user->first_name . '</a>';
        $message_text = 'Ура, у нас новый клиент! 
' . $user_url . ' хочет присоединиться к нашему боту и заказывать электронику';
        $message_keyboard = new InlineKeyboardMarkup([
            [[
                'text' => 'Принять',
                'callback_data' => json_encode([
                    'a' => 'accept_user',
                    'id' => $this->user->id
                ])
            ]]
        ]);
        foreach ($admin_list as $admin) {
            $this->getBot()->sendMessageWithKeyboard($admin, $message_text, $message_keyboard);
        }
    }

}