{{-- Difficulty Distribution --}}
<flux:card>
  <flux:heading size="lg">Recipe Difficulty Distribution</flux:heading>
  <div class="mt-section space-y-4">
    @php
      $totalRecipes = array_sum(array_column($this->difficultyDistribution, 'count'));
      $difficultyLabels = [1 => 'Easy', 2 => 'Medium', 3 => 'Hard'];
      $difficultyColors = [1 => 'bg-green-500', 2 => 'bg-amber-500', 3 => 'bg-red-500'];
    @endphp
    @foreach($this->difficultyDistribution as $item)
      @php
        $percentage = $totalRecipes > 0 ? ($item['count'] / $totalRecipes) * 100 : 0;
      @endphp
      <div wire:key="difficulty-{{ $item['difficulty'] }}">
        <div class="flex justify-between mb-1">
          <flux:text class="text-sm font-medium">
            {{ $difficultyLabels[$item['difficulty']] ?? 'Level ' . $item['difficulty'] }}
          </flux:text>
          <flux:text class="text-sm text-zinc-500">
            {{ number_format($item['count']) }} ({{ number_format($percentage, 1) }}%)
          </flux:text>
        </div>
        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
          <div
            class="{{ $difficultyColors[$item['difficulty']] ?? 'bg-zinc-500' }} h-2 rounded-full transition-all"
            style="width: {{ $percentage }}%"
          ></div>
        </div>
      </div>
    @endforeach
  </div>
</flux:card>
