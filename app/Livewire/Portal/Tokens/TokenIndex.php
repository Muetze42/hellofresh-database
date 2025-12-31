<?php

namespace App\Livewire\Portal\Tokens;

use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.app')]
class TokenIndex extends Component
{
    public string $tokenName = '';

    public int $tokenExpiration = 180;

    public ?string $newToken = null;

    public ?int $tokenToDelete = null;

    /**
     * Get available expiration options.
     *
     * @return array<int, string>
     */
    public function getExpirationOptions(): array
    {
        return [
            7 => '1 week',
            30 => '1 month',
            90 => '3 months',
            180 => '6 months',
        ];
    }

    /**
     * Create a new API token.
     */
    public function createToken(): void
    {
        $this->validate([
            'tokenName' => ['required', 'string', 'min:3', 'max:255'],
            'tokenExpiration' => ['required', 'integer', 'in:7,30,90,180'],
        ]);

        $user = auth()->user();

        if (! $user) {
            return;
        }

        $expiresAt = now()->addDays($this->tokenExpiration);
        $token = $user->createToken($this->tokenName, ['*'], $expiresAt);

        $this->newToken = $token->plainTextToken;
        $this->tokenName = '';
        $this->tokenExpiration = 180;

        $this->modal('create-token')->close();

        Flux::toastSuccess('API token created successfully. Make sure to copy it now!');
    }

    /**
     * Confirm token deletion.
     */
    public function confirmDelete(int $tokenId): void
    {
        $this->tokenToDelete = $tokenId;
    }

    /**
     * Cancel token deletion.
     */
    public function cancelDelete(): void
    {
        $this->tokenToDelete = null;
    }

    /**
     * Delete an API token.
     */
    public function deleteToken(int $tokenId): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $token = $user->tokens()->find($tokenId);

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
            Flux::toastSuccess('API token deleted successfully.');
        }

        $this->tokenToDelete = null;
    }

    /**
     * Clear the newly created token display.
     */
    public function clearNewToken(): void
    {
        $this->newToken = null;
    }

    public function render(): View
    {
        $user = auth()->user();
        $tokens = $user?->tokens()->latest()->get() ?? collect();

        return view('portal::livewire.tokens.index', [
            'tokens' => $tokens,
        ])->title('API Tokens');
    }
}
