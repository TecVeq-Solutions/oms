<?php

namespace App\Http\Controllers;

use App\Models\OfficeSetting;
use Illuminate\Http\Request;
use App\Services\SettingService;

class OfficeSettingController extends Controller
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function edit()
    {
        abort_unless(auth()->user()->can('view office settings'), 403);

        $settings = OfficeSetting::first();
        $dynamicSettings = $this->settingService->all();

        return view('office-settings.edit', compact('settings', 'dynamicSettings'));
    }

    public function update(Request $request)
    {
        abort_unless(auth()->user()->can('edit office settings'), 403);

        $request->validate([
            'office_name' => 'required|string|max:255',
            'office_email' => 'nullable|email',
            'office_phone' => 'nullable|string|max:50',
            'office_address' => 'nullable|string|max:500',
            'office_latitude' => 'nullable|numeric',
            'office_longitude' => 'nullable|numeric',
            'allowed_radius' => 'nullable|integer|min:0',

            'gemini_api_key' => 'nullable|string',
            'gemini_model' => 'nullable|string|max:255',
            'ai_enabled' => 'nullable|boolean',

            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|string|max:50',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',

            'site_name' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string|max:500',

            'late_after_minutes' => 'nullable|integer|min:0',
            'half_day_after_minutes' => 'nullable|integer|min:0',
            'require_selfie' => 'nullable|boolean',
            'require_location' => 'nullable|boolean',
            'require_checkout' => 'nullable|boolean',

            'attendance_module_enabled' => 'nullable|boolean',
            'leave_module_enabled' => 'nullable|boolean',
            'lead_module_enabled' => 'nullable|boolean',
            'campaign_module_enabled' => 'nullable|boolean',
            'ai_module_enabled' => 'nullable|boolean',
        ]);

        $officeData = [
            'office_name' => $request->office_name,
            'office_email' => $request->office_email,
            'office_phone' => $request->office_phone,
            'office_address' => $request->office_address,
            'office_latitude' => $request->office_latitude,
            'office_longitude' => $request->office_longitude,
            'allowed_radius_meters' => $request->allowed_radius,
        ];

        $officeSetting = OfficeSetting::first();

        if (!$officeSetting) {
            OfficeSetting::create($officeData);
        } else {
            $officeSetting->update($officeData);
        }

        $this->settingService->setMany([
            [
                'group' => 'ai',
                'key' => 'gemini_api_key',
                'value' => $request->gemini_api_key,
                'type' => 'string',
            ],
            [
                'group' => 'ai',
                'key' => 'gemini_model',
                'value' => $request->gemini_model ?: 'gemini-2.5-flash-lite',
                'type' => 'string',
            ],
            [
                'group' => 'ai',
                'key' => 'ai_enabled',
                'value' => $request->boolean('ai_enabled'),
                'type' => 'boolean',
            ],
            [
                'group' => 'email',
                'key' => 'smtp_host',
                'value' => $request->smtp_host,
                'type' => 'string',
            ],
            [
                'group' => 'email',
                'key' => 'smtp_port',
                'value' => $request->smtp_port,
                'type' => 'string',
            ],
            [
                'group' => 'email',
                'key' => 'smtp_username',
                'value' => $request->smtp_username,
                'type' => 'string',
            ],
            [
                'group' => 'email',
                'key' => 'smtp_password',
                'value' => $request->smtp_password,
                'type' => 'string',
            ],
            [
                'group' => 'general',
                'key' => 'site_name',
                'value' => $request->site_name,
                'type' => 'string',
            ],
            [
                'group' => 'general',
                'key' => 'site_tagline',
                'value' => $request->site_tagline,
                'type' => 'string',
            ],
            [
                'group' => 'general',
                'key' => 'footer_text',
                'value' => $request->footer_text,
                'type' => 'string',
            ],
            [
                'group' => 'attendance',
                'key' => 'late_after_minutes',
                'value' => $request->late_after_minutes ?? 15,
                'type' => 'integer',
            ],
            [
                'group' => 'attendance',
                'key' => 'half_day_after_minutes',
                'value' => $request->half_day_after_minutes ?? 240,
                'type' => 'integer',
            ],
            [
                'group' => 'attendance',
                'key' => 'require_selfie',
                'value' => $request->boolean('require_selfie'),
                'type' => 'boolean',
            ],
            [
                'group' => 'attendance',
                'key' => 'require_location',
                'value' => $request->boolean('require_location'),
                'type' => 'boolean',
            ],
            [
                'group' => 'attendance',
                'key' => 'require_checkout',
                'value' => $request->boolean('require_checkout'),
                'type' => 'boolean',
            ],
            [
                'group' => 'features',
                'key' => 'attendance_module_enabled',
                'value' => $request->boolean('attendance_module_enabled'),
                'type' => 'boolean',
            ],
            [
                'group' => 'features',
                'key' => 'leave_module_enabled',
                'value' => $request->boolean('leave_module_enabled'),
                'type' => 'boolean',
            ],
            [
                'group' => 'features',
                'key' => 'lead_module_enabled',
                'value' => $request->boolean('lead_module_enabled'),
                'type' => 'boolean',
            ],
            [
                'group' => 'features',
                'key' => 'campaign_module_enabled',
                'value' => $request->boolean('campaign_module_enabled'),
                'type' => 'boolean',
            ],
            [
                'group' => 'features',
                'key' => 'ai_module_enabled',
                'value' => $request->boolean('ai_module_enabled'),
                'type' => 'boolean',
            ],
        ]);

        return back()->with('success', 'Settings updated successfully.');
    }
}