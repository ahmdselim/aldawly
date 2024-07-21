<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Validation\ValidationException;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Exception\Messaging\NotFound;

class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * @throws MessagingException
     * @throws ValidationException
     * @throws FirebaseException
     */
    public function sendPushNotification(Request $request): JsonResponse
    {
        $this->validate($request, [
            'token' => 'required',
            'title' => 'required',
            'body' => 'required',
        ]);

        try {
            $this->firebaseService->sendNotification(
                auth()->user()->fcm_token,
                $request->input('title'),
                $request->input('body')
            );
//          set sound
        } catch (NotFound $e) {
            // Handle the case where the FCM token was not found
            // For example, remove the token from your database or log this incident
            return response()->json(['error' => 'Token not found or invalid'], 404);
        } catch (MessagingException $e) {
            // Handle other messaging exceptions
            return response()->json(['error' => 'Failed to send notification'], 500);
        }

        return response()->json(['status' => 'Notification sent successfully']);
    }
    public function setToken(Request $request): JsonResponse
    {
        $token = $request->input('fcm_token');
        $request->user()->update([
            'fcm_token' => $token
        ]); //Get the currrently logged in user and set their token
        return response()->json([
            'message' => 'Successfully Updated FCM Token'
        ]);
    }
}
