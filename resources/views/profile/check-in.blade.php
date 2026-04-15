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
                        <input type="hidden" id="face_verified" name="face_verified" value="{{ old('face_verified', '0') }}">
                        <input type="hidden" id="face_validation_note" name="face_validation_note" value="{{ old('face_validation_note') }}">

                        <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" class="hidden" required>

                        <div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 text-sm">
                            <p class="font-semibold mb-2">Selfie Guidelines</p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Your face must be clearly visible.</li>
                                <li>Only one face should appear in the frame.</li>
                                <li>Do not capture desk, wall, floor, ceiling, or random objects.</li>
                                <li>Do not hide your face with hand, mobile, helmet, or strong shadow.</li>
                                <li>Mask or glasses are allowed only if face is still clearly visible.</li>
                                <li>Attendance will not be submitted if no clear face is detected.</li>
                            </ul>
                        </div>

                        <div id="faceValidationMessage" class="hidden rounded-lg px-4 py-3 text-sm"></div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Live Camera Preview
                                </label>

                                <div class="rounded-xl overflow-hidden border bg-black">
                                    <video id="cameraPreview" autoplay playsinline class="w-full h-80 object-cover hidden"></video>

                                    <div id="cameraPlaceholder" class="w-full h-80 flex items-center justify-center text-sm text-gray-300">
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
                                    <img id="capturedPreview" src="" alt="Captured selfie preview" class="w-full h-80 object-cover hidden">

                                    <div id="capturedPlaceholder" class="w-full h-80 flex items-center justify-center text-sm text-gray-400">
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
                                class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition" disabled>
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

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

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
            const faceValidationMessage = document.getElementById('faceValidationMessage');
            const faceVerifiedInput = document.getElementById('face_verified');
            const faceValidationNoteInput = document.getElementById('face_validation_note');

            let stream = null;
            let photoCaptured = false;
            let isSubmitting = false;
            let cachedLocation = null;
            let modelsLoaded = false;
            let faceVerified = false;

            function setFaceMessage(message, type = 'error') {
                faceValidationMessage.className = 'rounded-lg px-4 py-3 text-sm border';

                if (type === 'success') {
                    faceValidationMessage.classList.add('bg-green-50', 'text-green-800', 'border-green-200');
                } else {
                    faceValidationMessage.classList.add('bg-red-50', 'text-red-800', 'border-red-200');
                }

                faceValidationMessage.textContent = message;
            }

            function resetFaceValidation() {
                faceVerified = false;
                faceVerifiedInput.value = '0';
                faceValidationNoteInput.value = '';
                submitCheckInBtn.disabled = true;
                faceValidationMessage.classList.add('hidden');
                faceValidationMessage.textContent = '';
                faceValidationMessage.className = 'hidden rounded-lg px-4 py-3 text-sm';
            }

            async function loadFaceModels() {
                if (modelsLoaded) return;
                await faceapi.nets.tinyFaceDetector.loadFromUri('/face-api-models');
                await faceapi.nets.faceLandmark68Net.loadFromUri('/face-api-models');
                modelsLoaded = true;
            }

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

                            resolve(cachedLocation);
                        },
                        function (error) {
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
                    resetFaceValidation();

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

            async function validateCapturedFace() {
                faceVerified = false;
                faceVerifiedInput.value = '0';
                faceValidationNoteInput.value = '';
                submitCheckInBtn.disabled = true;

                try {
                    await loadFaceModels();

                    const detections = await faceapi
                        .detectAllFaces(capturedPreview, new faceapi.TinyFaceDetectorOptions())
                        .withFaceLandmarks();

                    if (!detections.length) {
                        setFaceMessage('No face detected. Please capture a clear selfie.');
                        faceValidationNoteInput.value = 'No face detected';
                        faceValidationMessage.classList.remove('hidden');
                        return false;
                    }

                    if (detections.length > 1) {
                        setFaceMessage('Multiple faces detected. Only one face is allowed.');
                        faceValidationNoteInput.value = 'Multiple faces detected';
                        faceValidationMessage.classList.remove('hidden');
                        return false;
                    }

                    const box = detections[0].detection.box;
                    const imgWidth = capturedPreview.naturalWidth || capturedPreview.width;
                    const imgHeight = capturedPreview.naturalHeight || capturedPreview.height;

                    const faceArea = box.width * box.height;
                    const imageArea = imgWidth * imgHeight;
                    const ratio = faceArea / imageArea;

                    if (ratio < 0.08) {
                        setFaceMessage('Face is too small or too far. Please move closer to the camera.');
                        faceValidationNoteInput.value = 'Face too small';
                        faceValidationMessage.classList.remove('hidden');
                        return false;
                    }

                    faceVerified = true;
                    faceVerifiedInput.value = '1';
                    faceValidationNoteInput.value = 'Face verified on client side';
                    setFaceMessage('Face verified. You can now mark attendance.', 'success');
                    faceValidationMessage.classList.remove('hidden');
                    submitCheckInBtn.disabled = false;

                    return true;
                } catch (error) {
                    setFaceMessage('Face validation failed. Please try again.');
                    faceValidationNoteInput.value = 'Face validation JS error';
                    faceValidationMessage.classList.remove('hidden');
                    return false;
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

                    const previewUrl = URL.createObjectURL(blob);
                    capturedPreview.onload = async function () {
                        await validateCapturedFace();
                    };
                    capturedPreview.src = previewUrl;
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

                resetFaceValidation();
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

                if (!faceVerified || faceVerifiedInput.value !== '1') {
                    alert('A clear face is required to mark attendance.');
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