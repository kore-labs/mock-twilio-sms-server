<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MockMessageStatusReady;
use Carbon\Carbon;

class MessageController extends Controller
{

    public function sendSms(Request $request)
    {
      /* Request:
        {
          "from" => "+12005550000",
          "to" => "+12005550100",
          "body" => "N/A",
          "webhookUrl" => "http://example.com?target=qa-05&api_key=xyz"
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
      MockMessageStatusReady::dispatch($request, $message->toArray());

      return response()->json([ 'status' => 'success',
                                'data' => [
                                  'sid' => $sid,
                                  'status' => 'accepted',
                                  'dateCreated' => [
                                    'date' => Carbon::now(),
                                    'timezone' => "+00:00",
                                    'timezone_type' => 1
                                  ]
                                ]
                              ]);
    }

    public function getMessageBySID(Request $request, $sid)
    {
        $messageStatus =  Message::select('status')->where('sid', $sid)->first();
        return response()->json([
                                    'status' => $messageStatus['status'],
                                ]);
    }




}
