<x-app-layout>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6" x-data="{ tab: 'office' }">

                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        System Settings
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage office, attendance, AI, and email configuration.
                    </p>
                </div>

                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tabs -->
                <div class="flex flex-wrap gap-3 border-b pb-4 mb-6">
                    <button type="button" @click="tab = 'general'"
                        :class="tab === 'general' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        General
                    </button>

                    <button type="button" @click="tab = 'office'"
                        :class="tab === 'office' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        Office Settings
                    </button>

                    <button type="button" @click="tab = 'attendance'"
                        :class="tab === 'attendance' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        Attendance Rules
                    </button>

                    <button type="button" @click="tab = 'ai'"
                        :class="tab === 'ai' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        AI Settings
                    </button>

                    <button type="button" @click="tab = 'email'"
                        :class="tab === 'email' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        Email Settings
                    </button>

                    <button type="button" @click="tab = 'features'"
                        :class="tab === 'features' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition">
                        Feature Toggles
                    </button>
                </div>

                <form method="POST" action="{{ route('office-settings.update') }}">
                    @csrf
                    @method('PUT')
                    <!-- GENERAL TAB -->
                    <div x-show="tab === 'general'" x-cloak class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Site Name</label>
                                <input type="text" name="site_name"
                                    value="{{ old('site_name', $dynamicSettings['site_name'] ?? '') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Site Tagline</label>
                                <input type="text" name="site_tagline"
                                    value="{{ old('site_tagline', $dynamicSettings['site_tagline'] ?? '') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Footer Text</label>
                                <input type="text" name="footer_text"
                                    value="{{ old('footer_text', $dynamicSettings['footer_text'] ?? '') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- OFFICE TAB -->
                    <div x-show="tab === 'office'" x-cloak class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Office Name</label>
                                <input type="text" name="office_name"
                                    value="{{ old('office_name', $settings->office_name ?? '') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Office Email</label>
                                <input type="email" name="office_email"
                                    value="{{ old('office_email', $settings->office_email ?? '') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Office Phone</label>
                                <input type="text" name="office_phone"
                                    value="{{ old('office_phone', $settings->office_phone ?? '') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Office Address</label>
                                <input type="text" name="office_address"
                                    value="{{ old('office_address', $settings->office_address ?? '') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- ATTENDANCE TAB -->
                    <!-- ATTENDANCE TAB -->
                    <div x-show="tab === 'attendance'" x-cloak class="space-y-6">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Search Office
                                        Location</label>
                                    <div class="flex gap-2 mt-1">
                                        <input type="text" id="office_location_search"
                                            placeholder="Search office location..."
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        <button type="button" id="search_location_btn"
                                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
                                            Search
                                        </button>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pick Office Location on
                                        Map</label>
                                    <div id="office_map" style="height: 400px; border-radius: 12px;"></div>
                                </div>

                                <input type="hidden" id="office_latitude" name="office_latitude" value="{{ old('office_latitude', $settings->office_latitude ?? '') }}">
                                <input type="hidden" id="office_longitude" name="office_longitude" value="{{ old('office_longitude', $settings->office_longitude ?? '') }}">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Allowed Radius
                                        (meters)</label>
                                    <input type="number" name="allowed_radius"
                                        value="{{ old('allowed_radius', $settings->allowed_radius ?? '') }}"
                                        class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Late After Minutes</label>
                                    <input type="number" name="late_after_minutes"
                                        value="{{ old('late_after_minutes', $dynamicSettings['late_after_minutes'] ?? 15) }}"
                                        class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- AI TAB -->
                    <div x-show="tab === 'ai'" x-cloak class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Gemini API Key</label>
                                <input type="text" name="gemini_api_key"
                                    value="{{ old('gemini_api_key', $dynamicSettings['gemini_api_key'] ?? '') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Gemini Model</label>
                                <input type="text" name="gemini_model"
                                    value="{{ old('gemini_model', $dynamicSettings['gemini_model'] ?? 'gemini-2.5-flash-lite') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">AI Status</label>
                                <select name="ai_enabled"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1" {{ old('ai_enabled', ($dynamicSettings['ai_enabled'] ?? true) ? '1' : '0') == '1' ? 'selected' : '' }}>
                                        Enabled
                                    </option>
                                    <option value="0" {{ old('ai_enabled', ($dynamicSettings['ai_enabled'] ?? true) ? '1' : '0') == '0' ? 'selected' : '' }}>
                                        Disabled
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- EMAIL TAB -->
                    <div x-show="tab === 'email'" x-cloak class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">SMTP Host</label>
                                <input type="text" name="smtp_host"
                                    value="{{ old('smtp_host', $dynamicSettings['smtp_host'] ?? '') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">SMTP Port</label>
                                <input type="text" name="smtp_port"
                                    value="{{ old('smtp_port', $dynamicSettings['smtp_port'] ?? '587') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">SMTP Username</label>
                                <input type="text" name="smtp_username"
                                    value="{{ old('smtp_username', $dynamicSettings['smtp_username'] ?? '') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">SMTP Password</label>
                                <input type="text" name="smtp_password"
                                    value="{{ old('smtp_password', $dynamicSettings['smtp_password'] ?? '') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                    <!-- FEATURES TAB -->
                    <div x-show="tab === 'features'" x-cloak class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Attendance Module</label>
                                <select name="attendance_module_enabled"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1" {{ old('attendance_module_enabled', ($dynamicSettings['attendance_module_enabled'] ?? true) ? '1' : '0') == '1' ? 'selected' : '' }}>Enabled</option>
                                    <option value="0" {{ old('attendance_module_enabled', ($dynamicSettings['attendance_module_enabled'] ?? true) ? '1' : '0') == '0' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Leave Module</label>
                                <select name="leave_module_enabled"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1" {{ old('leave_module_enabled', ($dynamicSettings['leave_module_enabled'] ?? true) ? '1' : '0') == '1' ? 'selected' : '' }}>Enabled</option>
                                    <option value="0" {{ old('leave_module_enabled', ($dynamicSettings['leave_module_enabled'] ?? true) ? '1' : '0') == '0' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lead Module</label>
                                <select name="lead_module_enabled"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1" {{ old('lead_module_enabled', ($dynamicSettings['lead_module_enabled'] ?? true) ? '1' : '0') == '1' ? 'selected' : '' }}>Enabled</option>
                                    <option value="0" {{ old('lead_module_enabled', ($dynamicSettings['lead_module_enabled'] ?? true) ? '1' : '0') == '0' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Campaign Module</label>
                                <select name="campaign_module_enabled"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1" {{ old('campaign_module_enabled', ($dynamicSettings['campaign_module_enabled'] ?? true) ? '1' : '0') == '1' ? 'selected' : '' }}>Enabled</option>
                                    <option value="0" {{ old('campaign_module_enabled', ($dynamicSettings['campaign_module_enabled'] ?? true) ? '1' : '0') == '0' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">AI Module</label>
                                <select name="ai_module_enabled"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1" {{ old('ai_module_enabled', ($dynamicSettings['ai_module_enabled'] ?? true) ? '1' : '0') == '1' ? 'selected' : '' }}>Enabled</option>
                                    <option value="0" {{ old('ai_module_enabled', ($dynamicSettings['ai_module_enabled'] ?? true) ? '1' : '0') == '0' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SAVE BUTTON -->
                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            Save Settings
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const latInput = document.getElementById('office_latitude');
            const lngInput = document.getElementById('office_longitude');
            const searchInput = document.getElementById('office_location_search');
            const searchBtn = document.getElementById('search_location_btn');

            let defaultLat = parseFloat(latInput.value) || 24.8607;   // Karachi default
            let defaultLng = parseFloat(lngInput.value) || 67.0011;

            const map = L.map('office_map').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            function updateLatLng(lat, lng) {
                latInput.value = lat.toFixed(7);
                lngInput.value = lng.toFixed(7);
            }

            updateLatLng(defaultLat, defaultLng);

            // marker drag
            marker.on('dragend', function (e) {
                const position = marker.getLatLng();
                updateLatLng(position.lat, position.lng);
            });

            // map click
            map.on('click', function (e) {
                const { lat, lng } = e.latlng;
                marker.setLatLng([lat, lng]);
                updateLatLng(lat, lng);
            });

            // search location
            async function searchLocation() {
                const query = searchInput.value.trim();
                if (!query) return;

                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
                    const data = await response.json();

                    if (data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lng = parseFloat(data[0].lon);

                        map.setView([lat, lng], 16);
                        marker.setLatLng([lat, lng]);
                        updateLatLng(lat, lng);
                    } else {
                        alert('Location not found');
                    }
                } catch (error) {
                    console.error(error);
                    alert('Error searching location');
                }
            }

            searchBtn.addEventListener('click', searchLocation);

            searchInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchLocation();
                }
            });
        });
    </script>

</x-app-layout>