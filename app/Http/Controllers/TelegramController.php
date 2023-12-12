<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\TelegramNotification;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramUpdates;

class TelegramController extends Controller
{
    public function send(Request $request)
    {
        $message = json_encode($request->query('message')) ?? 'Test from my bot';

        try {
            $chatId = $this->getChatId();

            Notification::send($chatId, new TelegramNotification(json_decode($message)));
        } catch (\Throwable $th) {
            throw $th;
            return response()->json([
                'msg' => 'Error: '.$th->getMessage(),
            ]);
        }

        return response()->json([
                'msg' => 'Message sent successfully!',
            ]);
    }

    protected function getChatId()
    {
        // Response is an array of updates.
        $updates = TelegramUpdates::create()->latest()
                    ->options([
                        'timeout' => 0,
                    ])
                    ->get();

        if($updates['ok']) {

            $chatId = $updates['result'][0]['message']['chat']['id'];
            return $chatId;
        }
    }


}
