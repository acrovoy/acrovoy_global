<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form 
    method="post" 
    action="{{ route('profile.update') }}" 
    class="mt-6 space-y-6"
    enctype="multipart/form-data"
>
        @csrf
        @method('patch')



        <div class="flex flex-col items-center space-y-3">

    <div class="relative group w-32 h-32">

   

        {{-- Avatar Image --}}
        <img
        id="avatar-preview"
            src="{{ $user->avatar()?->cdn_url ?? asset('images/default-avatar.png') }}"
            class="w-32 h-32 rounded-full object-cover border border-gray-200 shadow-sm"
        >

        {{-- Overlay --}}
        <label 
            for="avatar"
            class="absolute inset-0 flex items-center justify-center 
                   bg-black bg-opacity-0 group-hover:bg-opacity-40 
                   text-white text-sm font-medium 
                   rounded-full cursor-pointer 
                   transition"
        >
            <span class="opacity-0 group-hover:opacity-100 transition">
                Change
            </span>
        </label>

        <input 
            type="file"
            id="avatar"
            name="avatar"
            accept="image/*"
            class="hidden"
        >
    </div>

    <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
</div>


        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>


        <div>
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input
                id="last_name"
                name="last_name"
                type="text"
                class="mt-1 block w-full"
                :value="old('last_name', $user->last_name)"
                autocomplete="family-name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
        </div>


        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>


        <div>
            <x-input-label for="role" :value="__('Account Type')" />
            
            <select id="role" name="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="buyer" @selected(old('role', $user->role) === 'buyer')>Buyer</option>
                <option value="manufacturer" @selected(old('role', $user->role) === 'manufacturer')>Manufacturer</option>
            </select>

            <x-input-error class="mt-2" :messages="$errors->get('role')" />
        </div>


        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>


    <script>
document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('avatar');
    const preview = document.getElementById('avatar-preview');

    if (!input || !preview) return;

    input.addEventListener('change', function (event) {

        const file = event.target.files[0];

        if (!file) return;

        // Проверка типа
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file.');
            input.value = '';
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
        };

        reader.readAsDataURL(file);
    });
});
</script>


</section>
