<flux:footer id="site-footer" class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 print:hidden !py-4 text-sm">
  <div class="flex flex-col sm:flex-row items-center justify-between gap-ui">
    <div class="flex items-center gap-ui">
      <flux:link href="https://github.com/Muetze42/hellofresh-database" target="_blank" class="inline-flex items-center gap-1 hover:text-zinc-700 dark:hover:text-zinc-200">
        <flux:icon.github variant="micro" />
        <span>GitHub</span>
      </flux:link>
    </div>
    <div>
      {{ __('Made with') }} <span class="text-red-500">&hearts;</span> {{ __('by') }}
      <flux:link href="https://huth.it" target="_blank" class="font-medium hover:text-zinc-700 dark:hover:text-zinc-200">
        Norman Huth
      </flux:link>
    </div>
  </div>
</flux:footer>
<flux:footer class="not-print:hidden text-center">
  <div>
    <flux:link :href="config('app.url')">{{ config('app.name') }}</flux:link>
  </div>
  <div>
    {{ __('Made with') }} <span class="text-red-500">&hearts;</span> {{ __('by') }}
    <flux:link href="https://huth.it" target="_blank" class="font-medium hover:text-zinc-700 dark:hover:text-zinc-200">
      Norman Huth
    </flux:link>
  </div>
</flux:footer>
