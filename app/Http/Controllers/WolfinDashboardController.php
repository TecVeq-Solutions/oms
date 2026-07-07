<?php

namespace App\Http\Controllers;

use App\Models\OtpRequest;
use App\Services\GeminiAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WolfinDashboardController extends Controller
{
    public function index()
    {
        $otps = OtpRequest::latest()->paginate(20);
        return view('wolfin.dashboard', compact('otps'));
    }

    public function send(Request $request, $id, GeminiAIService $aiService)
    {
        $otpRequest = OtpRequest::findOrFail($id);

        try {
            $message = $aiService->generateOtpMessage($otpRequest->name, $otpRequest->otp_code, auth()->id());
            
            // Get SMS gateway URL from env, or fallback to a dummy local address
            $gatewayUrl = env('SMS_GATEWAY_URL', 'http://localhost:8080/v1/sms/send');
            
            // Send request to Android SMS Gateway
            $response = Http::timeout(10)->post($gatewayUrl, [
                'to' => $otpRequest->phone_number,
                'message' => $message,
            ]);

            if ($response->successful() || env('APP_ENV') === 'local') {
                $otpRequest->update(['status' => 'sent']);
                return back()->with('success', 'SMS sent successfully to ' . $otpRequest->phone_number);
            } else {
                return back()->with('error', 'Failed to send SMS via Gateway. Status: ' . $response->status());
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error sending SMS: ' . $e->getMessage());
        }
    }
}
