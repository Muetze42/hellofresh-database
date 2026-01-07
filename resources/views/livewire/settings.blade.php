<flux:main container class="space-y-section">
  <flux:heading size="xl">{{ __('Settings') }}</flux:heading>

  {{-- Profile Information --}}
  <flux:card>
    <flux:heading size="lg">{{ __('Profile Information') }}</flux:heading>
    <flux:text class="mt-ui">{{ __("Update your account's profile information and email address.") }}</flux:text>

    <form wire:submit="updateProfile" class="mt-section space-y-section">
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
    </form>
  </flux:card>

  {{-- Avatar --}}
  <flux:card>
    <flux:heading size="lg">{{ __('Avatar') }}</flux:heading>
    <flux:text class="mt-ui">{{ __('Upload a profile picture. Image must be square and between 200x200 and 1000x1000 pixels.') }}</flux:text>

    <div class="mt-section flex items-center gap-section">
      <flux:avatar
        :src="$this->avatarUrl"
        :name="auth()->user()->name"
        size="xl"
        circle
      />
      @if($this->avatarUrl && !$avatar)
        <flux:button wire:click="removeAvatar" variant="danger" size="sm">
          {{ __('Remove') }}
        </flux:button>
      @endif
    </div>

    <form wire:submit="updateAvatar" class="mt-section">
      <flux:file-upload wire:model="avatar" :label="__('Upload new avatar')">
        <flux:file-upload.dropzone
          :heading="__('Drop image here or click to browse')"
          :text="__('JPG up to 2MB (200x200 - 1000x1000px, square)')"
          inline
          with-progress
        />
      </flux:file-upload>

      @if($avatar)
        <div class="mt-ui flex flex-col gap-ui">
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
        </div>
      @endif
    </form>
  </flux:card>

  {{-- Support --}}
  <flux:card>
    <flux:heading size="lg">{{ __('Support') }}</flux:heading>
    <flux:field class="mt-ui">
      <flux:label>
        {{ __('Your User ID for support requests:') }}
      </flux:label>
      <flux:input icon="hash" :value="auth()->id()" readonly copyable />
    </flux:field>
  </flux:card>

  {{-- Update Password --}}
  <flux:card>
    <flux:heading size="lg">{{ __('Update Password') }}</flux:heading>
    <flux:text class="mt-ui">{{ __('Ensure your account is using a long, random password to stay secure.') }}</flux:text>

    <form wire:submit="updatePassword" class="mt-section space-y-section">
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
    </form>
  </flux:card>
</flux:main>
