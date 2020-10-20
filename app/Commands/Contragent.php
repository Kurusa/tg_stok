<?php

namespace App\Commands;

use App\Services\Status\UserStatusService;

class Contragent extends BaseCommand
{

    function processCommand()
    {
        if ($this->user->status == UserStatusService::ORDER_START) {
            $this->getBot()->sendMessage($this->user->chat_id, $this->text['application_considered']);
            $this->triggerCommand(NotifyAdmin::class);
        } else {
            $this->user->status = UserStatusService::ORDER_START;
            $this->user->save();

            $this->getBot()->deleteMessage($this->user->chat_id, $this->update->getCallbackQuery()->getMessage()->getMessageId());
            $this->getBot()->sendMessage($this->user->chat_id, $this->text['enter_counterparty']);
        }
    }

}