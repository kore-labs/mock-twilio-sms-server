<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MockMessageStatusReady;

class MessageController extends Controller
{

    public function sendSms(Request $request)
    {
      /* Request:
        {
          "from" => "+12005550000",
          "to" => "+12005550100",
          "body" => "N/A",
          "qa-server" => "05",
          "api-key" => 'xyz'
        }
      */
      $request = $request->all();
      $sid = md5($request['to'].hrtime(true)).'-'.hrtime(true);
      $message = Message::create([
                        "sid" => $sid,
                        "phone" => $request['to'],
                        "status" => Message::getStatus($request['to'])
                      ]);

      // Trigger Web Hook Event
      MockMessageStatusReady::dispatch($message);

    }

    public function getMessageBySID(Request $request)
    {
        // TODO
    }




}
