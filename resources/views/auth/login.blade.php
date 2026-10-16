<x-guest-layout>

    <div class="mb-8">
        <span class="mc-eyebrow">Welcome back</span>

        <h1 class="text-2xl sm:text-3xl font-semibold" style="color: var(--mc-ink);">
            Masuk ke MindCraft
        </h1>

        <p class="mt-2 text-sm mc-muted">
            Atur waktumu dengan lebih terarah.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status
        class="mb-5"
        :status="session('status')"
    />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="mc-label">
                Email
            </label>

            <x-text-input
                id="email"
                class="mc-input"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="nama@email.com"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="text-sm font-semibold" style="color: var(--mc-ink);">
                    Password
                </label>

                @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="text-sm mc-link-soft"
                    >
                        Lupa password?
                    </a>
                @endif
            </div>

            <x-text-input
                id="password"
                class="mc-input"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>

        <!-- Remember -->
        <div class="flex items-center">
            <input
                id="remember_me"
                type="checkbox"
                class="mc-check"
                name="remember"
            >

            <label
                for="remember_me"
                class="ms-2 text-sm mc-muted"
            >
                Ingat saya
            </label>
        </div>

        <!-- Button -->
        <button type="submit" class="mc-btn">
            Masuk
        </button>
    </form>

    <!-- Register -->
    <div class="mt-8 pt-6 text-center border-t" style="border-color: var(--mc-line);">
        <p class="text-sm mc-muted">
            Belum punya akun?

            <a href="{{ route('register') }}" class="mc-link-soft">
                Buat akun
            </a>
        </p>
    </div>

</x-guest-layout>