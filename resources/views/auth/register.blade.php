<x-guest-layout>
    <div class="relative min-h-screen flex items-center justify-center overflow-hidden">
        {{-- VIDEO --}}
        <video id="vid1" autoplay muted playsinline
            class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000" style="z-index:1"
            onended="switchVideo(this)">
            <source src="{{ asset('videos/schoolasset.mp4') }}" type="video/mp4">
        </video>
        <video id="vid2" muted playsinline
            class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000"
            style="z-index:0">
            <source src="{{ asset('videos/schoolasset.mp4') }}" type="video/mp4">
        </video>

        {{-- OVERLAY --}}
        <div class="absolute inset-0 bg-black/40" style="z-index:2"></div>

        <!-- CARD -->
        <div class="relative w-full max-w-md rounded-2xl p-8 border border-white-500/30 shadow-2xl"
            style="z-index:3; background: rgba(255, 255, 255, 0.3); backdrop-filter: blur(16px); ">

            <!-- LOGO -->


            <!-- TITLE -->
            <h2 class="text-center text-2xl font-semibold text-gray-800">
                Create Account
            </h2>

            <p class="text-center text-sm text-gray-500 mt-1 mb-6">
                Already have an account?
                <a href="{{ route('login') }}" class="text-orange-500 hover:underline font-medium">
                    Sign in
                </a>
            </p>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- NAME -->
                <div>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Full Name"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                        required>

                    <x-input-error :messages="$errors->get('name')" class="text-sm text-red-500 mt-1" />
                </div>

                <!-- NOMOR INDUK -->
                <input type="text" name="nomor_induk" placeholder="Nomor Induk"
                    class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400">

                <!-- EMAIL -->
                <div>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email address"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                        required>

                    <x-input-error :messages="$errors->get('email')" class="text-sm text-red-500 mt-1" />
                </div>

                <!-- PHONE -->
                <input type="text" name="no_hp" placeholder="Phone number"
                    class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400">

                <!-- PASSWORD -->
                <div>
                    <input type="password" name="password" placeholder="Password"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                        required>

                    <x-input-error :messages="$errors->get('password')" class="text-sm text-red-500 mt-1" />
                </div>

                <!-- CONFIRM PASSWORD -->
                <div>
                    <input type="password" name="password_confirmation" placeholder="Confirm Password"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                        required>

                    <x-input-error :messages="$errors->get('password_confirmation')"
                        class="text-sm text-red-500 mt-1" />
                </div>

                <!-- BUTTON -->
                <button type="submit"
                    class="w-full py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold shadow-md transition">

                    Register

                </button>

            </form>

        </div>
    </div>
</x-guest-layout>