<x-guest-layout>

    <div class="mb-8">
        <span class="mc-eyebrow">Mulai sekarang</span>

        <h1 class="text-2xl sm:text-3xl font-semibold" style="color: var(--mc-ink);">
            Buat akun MindCraft
        </h1>

        <p class="mt-2 text-sm mc-muted">
            Mulai mengatur waktu dan aktivitasmu dengan lebih terarah.
        </p>
    </div>


    <form method="POST" action="{{ route('register') }}" class="space-y-5">

        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="mc-label">
                Nama
            </label>

            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                class="mc-input"
                placeholder="Nama kamu"
            >

            <x-input-error
                :messages="$errors->get('name')"
                class="mt-2"
            />
        </div>


        <!-- Email -->
        <div>
            <label for="email" class="mc-label">
                Email
            </label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                class="mc-input"
                placeholder="nama@email.com"
            >

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>


        <!-- Password -->
        <div>
            <label for="password" class="mc-label">
                Password
            </label>

            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                class="mc-input"
                placeholder="Minimal 8 karakter"
            >

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>


        <!-- Confirm -->
        <div>
            <label for="password_confirmation" class="mc-label">
                Konfirmasi Password
            </label>

            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="mc-input"
                placeholder="Ulangi password"
            >
        </div>


        <button type="submit" class="mc-btn">
            Buat akun
        </button>

    </form>


    <div class="mt-8 pt-6 text-center border-t" style="border-color: var(--mc-line);">
        <p class="text-sm mc-muted">
            Sudah punya akun?

            <a href="{{ route('login') }}" class="mc-link-soft">
                Masuk
            </a>
        </p>
    </div>

</x-guest-layout>