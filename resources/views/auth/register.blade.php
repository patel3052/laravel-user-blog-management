<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name') }}"
                autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                value="{{ old('email') }}" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Mobile -->
        <div class="mt-4">
            <x-input-label for="mobile" :value="__('Mobile')" />
            <x-text-input id="mobile" class="block mt-1 w-full" type="number" name="mobile"
                value="{{ old('mobile') }}" />
            <x-input-error :messages="$errors->get('mobile')" class="mt-2" />
        </div>

        <!-- DOB -->
        <div class="mt-4">
            <x-input-label for="dob" :value="__('Date of Birth')" />
            <x-text-input id="dob" class="block mt-1 w-full" type="date" name="dob"
                value="{{ old('dob') }}" />
            <x-input-error :messages="$errors->get('dob')" class="mt-2" />
        </div>

        <!-- Gender -->
        <div class="mt-4">
            <x-input-label :value="__('Gender')" />
            <select name="gender" class="block mt-1 w-full">
                <option value="">--Select--</option>
                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>

        <!-- Profile Image -->
        <div class="mt-4">
            <x-input-label for="profile_image" :value="__('Profile Image')" />
            <x-text-input id="profile_image" class="block mt-1 w-full" type="file" name="profile_image" />
            <x-input-error :messages="$errors->get('profile_image')" class="mt-2" />
        </div>

        <!-- Status -->
        <div class="mt-4">
            <x-input-label :value="__('Status')" />
            <select name="status" class="block mt-1 w-full">
                <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>


        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
