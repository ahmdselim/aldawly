<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{

    protected $messaging;

    public function __construct()
    {
        $serviceAccountPath = storage_path('ishaar-ab0a4-firebase-adminsdk-61cul-babd03e587.json');

        $factory = (new Factory)
            ->withServiceAccount($serviceAccountPath);

        $this->messaging = $factory->createMessaging();
    }

    /**
     * @throws MessagingException
     * @throws FirebaseException
     */
    public function sendNotification($token, $title, $body): JsonResponse
    {
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body));

        $this->messaging->send($message);



        return response()->json([
            'message' => 'Notification sent successfully'
        ]);
    }

}
