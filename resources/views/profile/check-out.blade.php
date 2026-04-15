<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Mark Check-Out</h2>
                <p class="text-sm text-gray-500 mb-6">
                    Capture your live selfie and current location to mark check-out.
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

                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Employee</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $employee->full_name }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Today Check-In</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $todayAttendance->check_in }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Assigned Shift</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">
                            {{ optional($employee->shift)->name ?? 'No Shift Assigned' }}
                        </p>
                    </div>
                </div>

                <form id="checkoutForm" method="POST" action="{{ route('profile.checkout.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                    <input type="hidden" id="capture_source" name="capture_source" value="camera">
                    <input type="hidden" id="face_verified" name="face_verified" value="0">
                    <input type="hidden" id="face_validation_note" name="face_validation_note">

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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Live Camera Preview</label>

                            <div class="rounded-xl overflow-hidden border bg-black">
                                <video id="cameraPreview" autoplay playsinline class="w-full h-80 object-cover hidden"></video>
                                <div id="cameraPlaceholder" class="w-full h-80 flex items-center justify-center text-sm text-gray-300">
                                    Camera preview will appear here
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-3">
                                <button type="button" id="openCameraBtn" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg">Open Camera</button>
                                <button type="button" id="capturePhotoBtn" class="px-5 py-2.5 bg-green-600 text-white rounded-lg hidden">Capture Photo</button>
                                <button type="button" id="retakePhotoBtn" class="px-5 py-2.5 bg-yellow-500 text-white rounded-lg hidden">Retake</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Captured Selfie</label>

                            <div class="rounded-xl overflow-hidden border bg-white">
                                <img id="capturedPreview" class="w-full h-80 object-cover hidden">
                                <div id="capturedPlaceholder" class="w-full h-80 flex items-center justify-center text-sm text-gray-400">
                                    No selfie captured yet
                                </div>
                            </div>

                            <canvas id="photoCanvas" class="hidden"></canvas>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" id="submitCheckOutBtn" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg" disabled>
                            Mark Check Out
                        </button>

                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const FACE_API_MODEL_URL = "{{ asset('face-api-models') }}";

    const form = document.getElementById('checkoutForm');
    const openCameraBtn = document.getElementById('openCameraBtn');
    const capturePhotoBtn = document.getElementById('capturePhotoBtn');
    const retakePhotoBtn = document.getElementById('retakePhotoBtn');
    const submitBtn = document.getElementById('submitCheckOutBtn');

    const cameraPreview = document.getElementById('cameraPreview');
    const cameraPlaceholder = document.getElementById('cameraPlaceholder');
    const capturedPreview = document.getElementById('capturedPreview');
    const capturedPlaceholder = document.getElementById('capturedPlaceholder');
    const photoCanvas = document.getElementById('photoCanvas');
    const photoInput = document.getElementById('photo');

    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const faceMsg = document.getElementById('faceValidationMessage');
    const faceVerifiedInput = document.getElementById('face_verified');

    let stream = null;
    let photoCaptured = false;
    let modelsLoaded = false;
    let faceVerified = false;

    function setMsg(msg, ok=false){
        faceMsg.className = 'rounded-lg px-4 py-3 text-sm border';
        faceMsg.classList.add(ok?'bg-green-50':'bg-red-50');
        faceMsg.classList.add(ok?'text-green-800':'text-red-800');
        faceMsg.textContent = msg;
        faceMsg.classList.remove('hidden');
    }

    async function loadModels(){
        if(modelsLoaded) return;
        await faceapi.nets.tinyFaceDetector.loadFromUri(FACE_API_MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(FACE_API_MODEL_URL);
        modelsLoaded = true;
    }

    async function validateFace(){
        faceVerified=false;
        submitBtn.disabled=true;

        await loadModels();

        const det = await faceapi.detectAllFaces(capturedPreview,new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks();

        if(!det.length){ setMsg('No face detected'); return; }
        if(det.length>1){ setMsg('Multiple faces detected'); return; }

        faceVerified=true;
        faceVerifiedInput.value='1';
        setMsg('Face verified',true);
        submitBtn.disabled=false;
    }

    openCameraBtn.onclick=async()=>{
        stream = await navigator.mediaDevices.getUserMedia({video:{facingMode:'user'}});
        cameraPreview.srcObject=stream;
        cameraPreview.classList.remove('hidden');
        cameraPlaceholder.classList.add('hidden');
        openCameraBtn.classList.add('hidden');
        capturePhotoBtn.classList.remove('hidden');
    };

    capturePhotoBtn.onclick=()=>{
        photoCanvas.width=cameraPreview.videoWidth;
        photoCanvas.height=cameraPreview.videoHeight;
        photoCanvas.getContext('2d').drawImage(cameraPreview,0,0);

        photoCanvas.toBlob(blob=>{
            const file=new File([blob],'selfie.jpg',{type:'image/jpeg'});
            const dt=new DataTransfer();
            dt.items.add(file);
            photoInput.files=dt.files;

            capturedPreview.onload=validateFace;
            capturedPreview.src=URL.createObjectURL(blob);
            capturedPreview.classList.remove('hidden');
            capturedPlaceholder.classList.add('hidden');

            stream.getTracks().forEach(t=>t.stop());

            capturePhotoBtn.classList.add('hidden');
            retakePhotoBtn.classList.remove('hidden');
            photoCaptured=true;
        });
    };

    retakePhotoBtn.onclick=()=>{
        location.reload();
    };

    form.onsubmit=async(e)=>{
        e.preventDefault();

        if(!photoCaptured){ alert('Capture selfie first'); return; }
        if(!faceVerified){ alert('Face required'); return; }

        navigator.geolocation.getCurrentPosition(pos=>{
            latitudeInput.value=pos.coords.latitude;
            longitudeInput.value=pos.coords.longitude;
            form.submit();
        });
    };
});
</script>
</x-app-layout>