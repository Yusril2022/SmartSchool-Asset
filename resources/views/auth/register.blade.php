<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-10">

        <!-- CARD -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border border-gray-200">

            <!-- LOGO -->


            <!-- TITLE -->
            <h2 class="text-center text-2xl font-semibold text-gray-800">
                Create Account
            </h2>

            <p class="text-center text-sm text-gray-500 mt-1 mb-6">
                Already have an account?
                <a href="{{ route('login') }}"
                    class="text-orange-500 hover:underline font-medium">
                    Sign in
                </a>
            </p>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- NAME -->
                <div>
                    <input type="text" name="name"
                        value="{{ old('name') }}"
                        placeholder="Full Name"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                        required>

                    <x-input-error :messages="$errors->get('name')" class="text-sm text-red-500 mt-1" />
                </div>

                <!-- NOMOR INDUK -->
                <input type="text" name="nomor_induk"
                    placeholder="Nomor Induk"
                    class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400">

                <!-- EMAIL -->
                <div>
                    <input type="email" name="email"
                        value="{{ old('email') }}"
                        placeholder="Email address"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                        required>

                    <x-input-error :messages="$errors->get('email')" class="text-sm text-red-500 mt-1" />
                </div>

                <!-- PHONE -->
                <input type="text" name="no_hp"
                    placeholder="Phone number"
                    class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400">

                <!-- PASSWORD -->
                <div>
                    <input type="password" name="password"
                        placeholder="Password"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                        required>

                    <x-input-error :messages="$errors->get('password')" class="text-sm text-red-500 mt-1" />
                </div>

                <!-- CONFIRM PASSWORD -->
                <div>
                    <input type="password" name="password_confirmation"
                        placeholder="Confirm Password"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                        required>

                    <x-input-error :messages="$errors->get('password_confirmation')" class="text-sm text-red-500 mt-1" />
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