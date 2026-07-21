@if(session('success') || session('error') || $errors->any())
    @php
        $type = session('success') ? 'success' : 'error';
        if ($errors->any()) {
            $type = 'error';
            $message = $errors->first();
        } else {
            $message = session($type);
        }
    @endphp
    <div id="toast"
        class="fixed top-5 right-5 px-4 py-3 rounded-lg shadow-lg z-50 text-white
        {{ $type === 'success' ? 'bg-green-500' : 'bg-red-500' }} animate-slide-in">
        {{ $message }}
    </div>
    <script src="{{ asset('js/common/toast.js') }}" defer></script>
    <style>
        @keyframes slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .animate-slide-in {
            animation: slide-in 0.4s ease-out;
        }
        .opacity-0 {
            transition: opacity 0.5s ease;
            opacity: 0;
        }
    </style>
@endif