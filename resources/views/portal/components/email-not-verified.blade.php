@if(auth()->check() && !auth()->user()->hasVerifiedEmail())
  <flux:callout icon="triangle-alert" color="amber">
    <flux:callout.heading>Email Not Verified</flux:callout.heading>
    <flux:callout.text>
      Please verify your email address to access all API features.
      <flux:link :href="route('portal.verification.notice')" wire:navigate>Resend verification email</flux:link>
    </flux:callout.text>
  </flux:callout>
@endif
