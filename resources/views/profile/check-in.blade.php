<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Mark My Attendance</h2>
                <p class="text-sm text-gray-500 mb-6">
                    Capture your live selfie and current location to mark check-in.
                </p>

                @if(session('error'))
                    <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">
                        {{ session('success') }}
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

                @if($todayAttendance)
                    <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                        You have already marked attendance for today.
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('profile.attendance') }}"
                            class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            View My Attendance
                        </a>
                    </div>
                @else
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500">Employee</p>
                            <p class="text-base font-semibold text-gray-800 mt-1">{{ $employee->full_name }}</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500">Date</p>
                            <p class="text-base font-semibold text-gray-800 mt-1">{{ now()->format('Y-m-d') }}</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500">Assigned Shift</p>
                            <p class="text-base font-semibold text-gray-800 mt-1">
                                {{ optional($employee->shift)->name ?? 'No Shift Assigned' }}
                            </p>
                        </div>
                    </div>

                    <form id="checkinForm" method="POST" action="{{ route('profile.checkin.store') }}"
                        enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                        <input type="hidden" id="capture_source" name="capture_source" value="camera">

                        <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" class="hidden"
                            required>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Live Camera Preview
                                </label>

                                <div class="rounded-xl overflow-hidden border bg-black">
                                    <video id="cameraPreview" autoplay playsinline
                                        class="w-full h-80 object-cover hidden"></video>

                                    <div id="cameraPlaceholder"
                                        class="w-full h-80 flex items-center justify-center text-sm text-gray-300">
                                        Camera preview will appear here
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-3">
                                    <button type="button" id="openCameraBtn"
                                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                        Open Camera
                                    </button>

                                    <button type="button" id="capturePhotoBtn"
                                        class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition hidden">
                                        Capture Photo
                                    </button>

                                    <button type="button" id="retakePhotoBtn"
                                        class="px-5 py-2.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition hidden">
                                        Retake
                                    </button>
                                </div>

                                <p class="text-xs text-gray-500 mt-2">
                                    Only live camera capture is allowed for attendance check-in.
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Captured Selfie
                                </label>

                                <div class="rounded-xl overflow-hidden border bg-white">
                                    <img id="capturedPreview" src="" alt="Captured selfie preview"
                                        class="w-full h-80 object-cover hidden">

                                    <div id="capturedPlaceholder"
                                        class="w-full h-80 flex items-center justify-center text-sm text-gray-400">
                                        No selfie captured yet
                                    </div>
                                </div>

                                <canvas id="photoCanvas" class="hidden"></canvas>

                                <div class="mt-4 rounded-lg bg-blue-50 text-blue-800 px-4 py-3 text-sm">
                                    Please allow camera and location access to mark your attendance.
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" id="submitCheckInBtn"
                                class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                Mark Check In
                            </button>

                            <a href="{{ route('dashboard') }}"
                                class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                Cancel
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('checkinForm');
            if (!form) return;

            const openCameraBtn = document.getElementById('openCameraBtn');
            const capturePhotoBtn = document.getElementById('capturePhotoBtn');
            const retakePhotoBtn = document.getElementById('retakePhotoBtn');
            const submitCheckInBtn = document.getElementById('submitCheckInBtn');

            const cameraPreview = document.getElementById('cameraPreview');
            const cameraPlaceholder = document.getElementById('cameraPlaceholder');
            const capturedPreview = document.getElementById('capturedPreview');
            const capturedPlaceholder = document.getElementById('capturedPlaceholder');
            const photoCanvas = document.getElementById('photoCanvas');
            const photoInput = document.getElementById('photo');

            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');

            let stream = null;
            let photoCaptured = false;
            let isSubmitting = false;
            let cachedLocation = null;

            function stopCamera() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
            }

            function getLocation(forceRefresh = false) {
                return new Promise((resolve, reject) => {
                    if (!navigator.geolocation) {
                        reject(new Error('Geolocation is not supported by this browser.'));
                        return;
                    }

                    if (!forceRefresh && cachedLocation) {
                        latitudeInput.value = cachedLocation.latitude;
                        longitudeInput.value = cachedLocation.longitude;
                        resolve(cachedLocation);
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            cachedLocation = {
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude,
                            };

                            latitudeInput.value = cachedLocation.latitude;
                            longitudeInput.value = cachedLocation.longitude;

                            console.log('Location success:', position.coords);
                            resolve(cachedLocation);
                        },
                        function (error) {
                            console.log('Geolocation error code:', error.code);
                            console.log('Geolocation error message:', error.message);

                            if (error.code === 1) {
                                reject(new Error('Location permission denied by browser or system.'));
                            } else if (error.code === 2) {
                                reject(new Error('Location information is unavailable on this device.'));
                            } else if (error.code === 3) {
                                reject(new Error('Location request timed out. Please try again.'));
                            } else {
                                reject(new Error('Unable to get current location.'));
                            }
                        },
                        {
                            enableHighAccuracy: false,
                            timeout: 20000,
                            maximumAge: 60000
                        }
                    );
                });
            }

            async function openCamera() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user',
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        },
                        audio: false
                    });

                    cameraPreview.srcObject = stream;
                    cameraPreview.classList.remove('hidden');
                    cameraPlaceholder.classList.add('hidden');

                    openCameraBtn.classList.add('hidden');
                    capturePhotoBtn.classList.remove('hidden');
                    retakePhotoBtn.classList.add('hidden');
                } catch (error) {
                    alert('Camera access denied or unavailable.');
                }
            }

            function capturePhoto() {
                if (!cameraPreview.videoWidth || !cameraPreview.videoHeight) {
                    alert('Camera is not ready yet.');
                    return;
                }

                photoCanvas.width = cameraPreview.videoWidth;
                photoCanvas.height = cameraPreview.videoHeight;

                const context = photoCanvas.getContext('2d');
                context.drawImage(cameraPreview, 0, 0, photoCanvas.width, photoCanvas.height);

                photoCanvas.toBlob(function (blob) {
                    if (!blob) {
                        alert('Photo capture failed.');
                        return;
                    }

                    const file = new File([blob], 'camera-selfie.jpg', {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });

                    const dt = new DataTransfer();
                    dt.items.add(file);
                    photoInput.files = dt.files;

                    capturedPreview.src = URL.createObjectURL(blob);
                    capturedPreview.classList.remove('hidden');
                    capturedPlaceholder.classList.add('hidden');

                    photoCaptured = true;

                    stopCamera();
                    cameraPreview.classList.add('hidden');
                    cameraPlaceholder.classList.remove('hidden');
                    capturePhotoBtn.classList.add('hidden');
                    retakePhotoBtn.classList.remove('hidden');
                }, 'image/jpeg', 0.92);
            }

            async function retakePhoto() {
                photoCaptured = false;

                const dt = new DataTransfer();
                photoInput.files = dt.files;

                capturedPreview.src = '';
                capturedPreview.classList.add('hidden');
                capturedPlaceholder.classList.remove('hidden');

                await openCamera();
            }

            openCameraBtn.addEventListener('click', openCamera);
            capturePhotoBtn.addEventListener('click', capturePhoto);
            retakePhotoBtn.addEventListener('click', retakePhoto);

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                if (isSubmitting) {
                    return;
                }

                if (!photoCaptured || !photoInput.files.length) {
                    alert('Please capture your live selfie before check-in.');
                    return;
                }

                try {
                    await getLocation();
                } catch (error) {
                    alert(error.message);
                    return;
                }

                isSubmitting = true;
                submitCheckInBtn.disabled = true;
                submitCheckInBtn.innerText = 'Submitting...';

                form.submit();
            });

            window.addEventListener('beforeunload', stopCamera);
        });
    </script>
</x-app-layout>