<flux:main container class="max-w-xl lg:max-w-3xl">
  <flux:heading size="xl">{{ __('Settings') }}</flux:heading>

  <flux:separator variant="subtle" class="my-8" />

  {{-- Profile Information --}}
  <form wire:submit="updateProfile">
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
      <div class="lg:w-80">
        <flux:heading size="lg">{{ __('Profile Information') }}</flux:heading>
        <flux:subheading>{{ __("Update your account's profile information and email address.") }}</flux:subheading>
      </div>

      <div class="flex-1 space-y-section">
        <flux:input
          wire:model="name"
          :label="__('Name')"
          :placeholder="__('Your name')"
          required
        />

        <flux:input
          wire:model="email"
          type="email"
          :label="__('Email')"
          placeholder="you@example.com"
          required
        />

        <x-country-select wire:model="country_code" />

        <div class="flex justify-end">
          <flux:button type="submit" variant="primary">
            {{ __('Save') }}
          </flux:button>
        </div>
      </div>
    </div>
  </form>

  <flux:separator variant="subtle" class="my-8" />

  {{-- Avatar --}}
  <form wire:submit="updateAvatar">
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
      <div class="lg:w-80">
        <flux:heading size="lg">{{ __('Avatar') }}</flux:heading>
        <flux:subheading>{{ __('Upload a profile picture. Image must be square and between 200x200 and 1000x1000 pixels.') }}</flux:subheading>
      </div>

      <div class="flex-1 space-y-ui">
        <div class="flex items-center gap-section">
          <flux:avatar
            :src="$this->avatarUrl"
            :name="auth()->user()->name"
            size="xl"
            circle
          />
          @if($this->avatarUrl && !$avatar)
            <flux:button wire:click="removeAvatar" type="button" variant="danger" size="sm">
              {{ __('Remove') }}
            </flux:button>
          @endif
        </div>

        <flux:file-upload wire:model="avatar" :label="__('Upload new avatar')">
          <flux:file-upload.dropzone
            :heading="__('Drop image here or click to browse')"
            :text="__('JPG up to 2MB (200x200 - 1000x1000px, square)')"
            inline
            with-progress
          />
        </flux:file-upload>

        @if($avatar)
          <flux:file-item
            :heading="$avatar->getClientOriginalName()"
            :image="$avatar->isPreviewable() ? $avatar->temporaryUrl() : null"
            :size="$avatar->getSize()"
          >
            <x-slot name="actions">
              <flux:file-item.remove wire:click="cancelAvatarUpload" :aria-label="__('Remove file')" />
            </x-slot>
          </flux:file-item>

          <div class="flex justify-end gap-ui">
            <flux:button type="button" wire:click="cancelAvatarUpload">
              {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary">
              {{ __('Save Avatar') }}
            </flux:button>
          </div>
        @endif
      </div>
    </div>
  </form>

  <flux:separator variant="subtle" class="my-8" />

  {{-- Update Password --}}
  <form wire:submit="updatePassword">
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
      <div class="lg:w-80">
        <flux:heading size="lg">{{ __('Update Password') }}</flux:heading>
        <flux:subheading>{{ __('Ensure your account is using a long, random password to stay secure.') }}</flux:subheading>
      </div>

      <div class="flex-1 space-y-section">
        <flux:input
          wire:model="current_password"
          type="password"
          :label="__('Current Password')"
          :placeholder="__('Your current password')"
          required
        />

        <flux:input
          wire:model="password"
          type="password"
          :label="__('New Password')"
          :placeholder="__('Your new password')"
          required
        />

        <flux:input
          wire:model="password_confirmation"
          type="password"
          :label="__('Confirm Password')"
          :placeholder="__('Confirm your new password')"
          required
        />

        <div class="flex justify-end">
          <flux:button type="submit" variant="primary">
            {{ __('Update Password') }}
          </flux:button>
        </div>
      </div>
    </div>
  </form>

  <flux:separator variant="subtle" class="my-8" />

  {{-- Support --}}
  <div class="flex flex-col lg:flex-row gap-4 lg:gap-6 pb-10">
    <div class="lg:w-80">
      <flux:heading size="lg">{{ __('Support') }}</flux:heading>
      <flux:subheading>{{ __('Information for support requests.') }}</flux:subheading>
    </div>

    <div class="flex-1">
      <flux:field>
        <flux:label>{{ __('Your User ID for support requests:') }}</flux:label>
        <flux:input icon="hash" :value="auth()->id()" readonly copyable />
      </flux:field>
    </div>
  </div>
</flux:main>
