<flux:modal name="auth-modal" class="max-w-md space-y-section">
  @if ($mode === 'login')
    <div wire:key="login-form" class="flex flex-col gap-section">
      <div>
        <flux:heading size="lg">{{ __('Login') }}</flux:heading>
        <flux:text class="mt-ui">{{ __('Sign in to access your favorites and saved lists.') }}</flux:text>
      </div>

      <form wire:submit="login" class="space-y-section">
        <flux:field>
          <flux:label>{{ __('Email') }}</flux:label>
          <flux:input wire:model="email" name="login_email" type="email" placeholder="{{ __('your@email.com') }}" />
          <flux:error name="email" />
        </flux:field>

        <flux:field>
          <flux:label>{{ __('Password') }}</flux:label>
          <flux:input wire:model="password" name="login_password" type="password" />
          <flux:error name="password" />
        </flux:field>

        <flux:field>
          <flux:checkbox wire:model="remember" label="{{ __('Remember me') }}" />
        </flux:field>

        <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
          <span wire:loading.remove wire:target="login">{{ __('Login') }}</span>
          <span wire:loading wire:target="login" class="flex items-center gap-ui">
            <flux:icon.loading class="size-4" />
            {{ __('Login') }}
          </span>
        </flux:button>
      </form>

      <div class="flex flex-col items-center gap-ui">
        <flux:button wire:click="switchToForgotPassword" variant="ghost" size="sm">
          {{ __('Forgot password?') }}
        </flux:button>

        <div class="text-center">
          <flux:text>{{ __("Don't have an account?") }}</flux:text>
          <flux:button wire:click="switchToRegister" variant="ghost" size="sm">
            {{ __('Create Account') }}
          </flux:button>
        </div>
      </div>
    </div>
  @elseif ($mode === 'register')
    <div wire:key="register-form" class="flex flex-col gap-section">
      <div>
        <flux:heading size="lg">{{ __('Create Account') }}</flux:heading>
        <flux:text class="mt-ui">{{ __('Register to save your favorites and shopping lists.') }}</flux:text>
      </div>

      <form wire:submit="register" class="space-y-section">
        <flux:field>
          <flux:label>{{ __('Name') }}</flux:label>
          <flux:input wire:model="name" name="name" type="text" placeholder="{{ __('Your name') }}" />
          <flux:error name="name" />
        </flux:field>

        <flux:field>
          <flux:label>{{ __('Email') }}</flux:label>
          <flux:input wire:model="email" name="register_email" type="email" placeholder="{{ __('your@email.com') }}" />
          <flux:error name="email" />
        </flux:field>

        <x-country-select wire:model="country_code" />

        <flux:field>
          <flux:label>{{ __('Password') }}</flux:label>
          <flux:input wire:model="password" name="register_password" type="password" />
          <flux:error name="password" />
        </flux:field>

        <flux:field>
          <flux:label>{{ __('Confirm Password') }}</flux:label>
          <flux:input wire:model="password_confirmation" name="password_confirmation" type="password" />
          <flux:error name="password_confirmation" />
        </flux:field>

        <flux:field>
          <div class="flex items-start gap-ui">
            <flux:checkbox wire:model="acceptPrivacy" id="accept-privacy" />
            <flux:label for="accept-privacy" class="text-sm">
              {{ __('I accept the') }}
              <flux:link :href="localized_route('localized.privacy-policy')" target="_blank">{{ __('Privacy Policy') }}</flux:link>
              {{ __('and') }}
              <flux:link :href="localized_route('localized.terms-of-use')" target="_blank">{{ __('Terms of Use') }}</flux:link>
            </flux:label>
          </div>
          <flux:error name="acceptPrivacy" />
        </flux:field>

        <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
          <span wire:loading.remove wire:target="register">{{ __('Register') }}</span>
          <span wire:loading wire:target="register" class="flex items-center gap-ui">
            <flux:icon.loading class="size-4" />
            {{ __('Register') }}
          </span>
        </flux:button>
      </form>

      <flux:separator />

      <div class="text-center">
        <flux:text>{{ __('Already have an account?') }}</flux:text>
        <flux:button wire:click="switchToLogin" variant="ghost" size="sm">
          {{ __('Login') }}
        </flux:button>
      </div>
    </div>
  @elseif ($mode === 'forgot-password')
    <div wire:key="forgot-password-form" class="flex flex-col gap-section">
      <div>
        <flux:heading size="lg">{{ __('Reset Password') }}</flux:heading>
        <flux:text class="mt-ui">{{ __('Enter your email address and we will send you a link to reset your password.') }}</flux:text>
      </div>

      @if ($resetLinkSent)
        <flux:callout variant="success" icon="check-circle">
          {{ __('We have emailed your password reset link.') }}
        </flux:callout>
      @else
        <form wire:submit="sendResetLink" class="space-y-section">
          <flux:field>
            <flux:label>{{ __('Email') }}</flux:label>
            <flux:input wire:model="email" name="reset_email" type="email" placeholder="{{ __('your@email.com') }}" />
            <flux:error name="email" />
          </flux:field>

          <flux:button type="submit" variant="primary" class="w-full" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="sendResetLink">{{ __('Send Reset Link') }}</span>
            <span wire:loading wire:target="sendResetLink" class="flex items-center gap-ui">
              <flux:icon.loading class="size-4" />
              {{ __('Send Reset Link') }}
            </span>
          </flux:button>
        </form>
      @endif

      <flux:separator />

      <div class="text-center">
        <flux:button wire:click="switchToLogin" variant="ghost" size="sm">
          {{ __('Back to Login') }}
        </flux:button>
      </div>
    </div>
  @endif
</flux:modal>
