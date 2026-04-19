<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">

        <!-- CARD -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border border-gray-200">

           

            <!-- TITLE -->
            <h2 class="text-center text-2xl font-semibold text-gray-800">
                Welcome Back
            </h2>

            <p class="text-center text-sm text-gray-500 mt-1 mb-6">
                Don't have an account?
                <a href="{{ route('register') }}"
                   class="text-orange-500 hover:underline font-medium">
                    Sign up
                </a>
            </p>

            <!-- SESSION -->
            <x-auth-session-status class="mb-4 text-green-500 text-sm" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- EMAIL -->
                <div>
                    <input type="email" name="email"
                        value="{{ old('email') }}"
                        placeholder="Email address"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                        required autofocus>

                    <x-input-error :messages="$errors->get('email')" class="text-sm text-red-500 mt-1" />
                </div>

                <!-- PASSWORD -->
                <div>
                    <input type="password" name="password"
                        placeholder="Password"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                        required>

                    <x-input-error :messages="$errors->get('password')" class="text-sm text-red-500 mt-1" />
                </div>

                <!-- FORGOT -->
                <div class="text-right text-sm">
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-gray-500 hover:text-orange-500">
                        Forgot password?
                    </a>
                    @endif
                </div>

                <!-- BUTTON -->
                <button type="submit"
                    class="w-full py-2.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium transition shadow">

                    Login

                </button>

            </form>

            <!-- DIVIDER -->
            <div class="my-6 text-center text-xs text-gray-400">
                OR
            </div>

            <!-- SOCIAL -->
            <div class="flex gap-3">
                <button class="flex-1 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm transition">
                    
                </button>
                <button class="flex-1 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm transition">
                    G
                </button>
                <button class="flex-1 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm transition">
                    X
                </button>
            </div>

        </div>
    </div>
</x-guest-layout>