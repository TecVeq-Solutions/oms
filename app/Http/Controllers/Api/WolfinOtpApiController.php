<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GeminiAIService;
use Illuminate\Support\Facades\Http;

class WolfinOtpApiController extends Controller
{
    public function init(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
        ]);

        $token = \Illuminate\Support\Str::random(60);

        \App\Models\WolfinAppToken::updateOrCreate(
            ['device_id' => $request->device_id],
            [
                'token' => $token,
                'expires_at' => now()->addDays(30),
            ]
        );

        return response()->json(['token' => $token]);
    }

    public function requestOtp(Request $request, GeminiAIService $aiService, \App\Services\AppNotificationService $notificationService)
    {
        $request->validate([
            'name' => 'nullable|string',
            'phone_number' => 'required|string',
            'otp_code' => 'required|string',
            'type' => 'nullable|string',
        ]);

        $otpRequest = \App\Models\OtpRequest::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'otp_code' => $request->otp_code,
            'type' => $request->type ?? 'login',
            'status' => 'pending',
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            $userIds = \App\Models\User::role(['admin', 'wolfin support'])->pluck('id')->toArray();
            $notificationService->notifyUsers(
                $userIds,
                'wolfin_otp',
                'New OTP Request',
                "A new OTP request was generated for {$otpRequest->phone_number}",
                route('wolfin.otps.index')
            );
        } catch (\Exception $e) {
            // Ignore if role doesn't exist or other error so it doesn't break OTP flow
        }

        try {
            $message = $aiService->generateOtpMessage($otpRequest->name, $otpRequest->otp_code, null);
            $gatewayUrl = env('SMS_GATEWAY_URL', 'http://localhost:8080/v1/sms/send');

            $formattedPhone = $otpRequest->phone_number;
            if (str_starts_with($formattedPhone, '03')) {
                $formattedPhone = '+92' . substr($formattedPhone, 1);
            }

            $response = Http::timeout(10)
                ->withBasicAuth(env('SMS_GATEWAY_USERNAME', ''), env('SMS_GATEWAY_PASSWORD', ''))
                ->post($gatewayUrl, [
                    'message' => $message,
                    'phoneNumbers' => [$formattedPhone],
                ]);

            if ($response->successful() || env('APP_ENV') === 'local') {
                $otpRequest->update(['status' => 'sent']);
                return response()->json(['success' => true, 'id' => $otpRequest->id]);
            } else {
                return response()->json(['error' => 'Failed to send SMS via Gateway. Status: ' . $response->status()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error sending SMS: ' . $e->getMessage()], 500);
        }
    }

    public function resendOtp(Request $request, GeminiAIService $aiService)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'otp_code' => 'required|string',
        ]);

        $otpRequest = \App\Models\OtpRequest::where('phone_number', $request->phone_number)
            ->latest()
            ->first();

        if ($otpRequest) {
            $otpRequest->update([
                'otp_code' => $request->otp_code,
                'status' => 'pending',
                'expires_at' => now()->addMinutes(10),
            ]);
        } else {
            // If none exists, create one
            $otpRequest = \App\Models\OtpRequest::create([
                'phone_number' => $request->phone_number,
                'otp_code' => $request->otp_code,
                'status' => 'pending',
                'expires_at' => now()->addMinutes(10),
            ]);
        }

        try {
            $message = $aiService->generateOtpMessage($otpRequest->name, $otpRequest->otp_code, null);
            $gatewayUrl = env('SMS_GATEWAY_URL', 'http://localhost:8080/v1/sms/send');

            $formattedPhone = $otpRequest->phone_number;
            if (str_starts_with($formattedPhone, '03')) {
                $formattedPhone = '+92' . substr($formattedPhone, 1);
            }

            $response = Http::timeout(10)
                ->withBasicAuth(env('SMS_GATEWAY_USERNAME', ''), env('SMS_GATEWAY_PASSWORD', ''))
                ->post($gatewayUrl, [
                    'message' => $message,
                    'phoneNumbers' => [$formattedPhone],
                ]);

            if ($response->successful() || env('APP_ENV') === 'local') {
                $otpRequest->update(['status' => 'sent']);
                return response()->json(['success' => true]);
            } else {
                return response()->json(['error' => 'Failed to send SMS via Gateway. Status: ' . $response->status()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error sending SMS: ' . $e->getMessage()], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'otp_code' => 'required|string',
        ]);

        $otpRequest = \App\Models\OtpRequest::where('phone_number', $request->phone_number)
            ->where('otp_code', $request->otp_code)
            ->latest()
            ->first();

        if (!$otpRequest) {
            return response()->json(['error' => 'Invalid OTP'], 400);
        }

        if ($otpRequest->expires_at < now()) {
            $otpRequest->update(['status' => 'expired']);
            return response()->json(['error' => 'OTP expired'], 400);
        }

        $otpRequest->update(['status' => 'verified']);

        return response()->json(['success' => true]);
    }
}
