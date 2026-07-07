<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

    public function requestOtp(Request $request)
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

        return response()->json(['success' => true, 'id' => $otpRequest->id]);
    }

    public function resendOtp(Request $request)
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
            \App\Models\OtpRequest::create([
                'phone_number' => $request->phone_number,
                'otp_code' => $request->otp_code,
                'status' => 'pending',
                'expires_at' => now()->addMinutes(10),
            ]);
        }

        return response()->json(['success' => true]);
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
