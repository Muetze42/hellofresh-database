<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Portal;

use App\Livewire\Portal\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ProfileTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_mounts_with_user_data(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'country_code' => 'DE',
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->assertSet('name', 'John Doe')
            ->assertSet('email', 'john@example.com')
            ->assertSet('country_code', 'DE');
    }

    #[Test]
    public function it_mounts_with_null_country_code(): void
    {
        $user = User::factory()->create([
            'country_code' => null,
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->assertSet('country_code', null);
    }

    #[Test]
    public function it_updates_profile_successfully(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@gmail.com',
            'country_code' => null,
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('name', 'New Name')
            ->set('email', 'old@gmail.com')
            ->set('country_code', 'DE')
            ->call('updateProfile')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('DE', $user->country_code);
    }

    #[Test]
    public function it_validates_name_is_required(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('name', '')
            ->call('updateProfile')
            ->assertHasErrors(['name' => 'required']);
    }

    #[Test]
    public function it_validates_name_min_length(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('name', 'A')
            ->call('updateProfile')
            ->assertHasErrors(['name' => 'min']);
    }

    #[Test]
    public function it_validates_name_max_length(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('name', str_repeat('a', 256))
            ->call('updateProfile')
            ->assertHasErrors(['name' => 'max']);
    }

    #[Test]
    public function it_validates_name_is_unique(): void
    {
        User::factory()->create(['name' => 'Taken Name']);
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('name', 'Taken Name')
            ->call('updateProfile')
            ->assertHasErrors(['name' => 'unique']);
    }

    #[Test]
    public function it_allows_keeping_same_name(): void
    {
        $user = User::factory()->create(['name' => 'John Doe']);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('name', 'John Doe')
            ->call('updateProfile')
            ->assertHasNoErrors('name');
    }

    #[Test]
    public function it_validates_email_is_required(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('email', '')
            ->call('updateProfile')
            ->assertHasErrors(['email' => 'required']);
    }

    #[Test]
    public function it_validates_email_format(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('email', 'invalid-email')
            ->call('updateProfile')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function it_validates_email_is_unique(): void
    {
        User::factory()->create(['email' => 'taken@gmail.com']);
        $user = User::factory()->create(['email' => 'user@gmail.com']);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('email', 'taken@gmail.com')
            ->call('updateProfile')
            ->assertHasErrors(['email' => 'unique']);
    }

    #[Test]
    public function it_validates_country_code_size(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('country_code', 'DEU')
            ->call('updateProfile')
            ->assertHasErrors(['country_code' => 'size']);
    }

    #[Test]
    public function it_allows_null_country_code(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('country_code', null)
            ->call('updateProfile')
            ->assertHasNoErrors('country_code');
    }

    #[Test]
    public function it_redirects_when_email_changed_and_was_verified(): void
    {
        $user = User::factory()->create([
            'email' => 'old@gmail.com',
            'email_verified_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('email', 'new@gmail.com')
            ->call('updateProfile')
            ->assertRedirect(route('portal.profile'));
    }

    #[Test]
    public function it_clears_email_verification_when_email_changed(): void
    {
        $user = User::factory()->create([
            'email' => 'old@gmail.com',
            'email_verified_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('email', 'new@gmail.com')
            ->call('updateProfile');

        $user->refresh();
        $this->assertSame('new@gmail.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    #[Test]
    public function it_redirects_when_email_changed_but_was_not_verified(): void
    {
        $user = User::factory()->create([
            'email' => 'old@gmail.com',
            'email_verified_at' => null,
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('email', 'new@gmail.com')
            ->call('updateProfile')
            ->assertRedirect(route('portal.profile'));
    }

    #[Test]
    public function it_validates_current_password_is_required_for_password_update(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('current_password', '')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('updatePassword')
            ->assertHasErrors(['current_password' => 'required']);
    }

    #[Test]
    public function it_validates_current_password_matches(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('current_password', 'WrongPassword!')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);
    }

    #[Test]
    public function it_validates_new_password_is_required(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('current_password', 'OldPassword123!')
            ->set('password', '')
            ->call('updatePassword')
            ->assertHasErrors(['password' => 'required']);
    }

    #[Test]
    public function it_validates_password_confirmation(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('current_password', 'OldPassword123!')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'DifferentPassword!')
            ->call('updatePassword')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    #[Test]
    public function it_updates_password_successfully(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('current_password', 'OldPassword123!')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('updatePassword')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));
    }

    #[Test]
    public function it_resets_password_fields_after_update(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('current_password', 'OldPassword123!')
            ->set('password', 'NewPassword123!')
            ->set('password_confirmation', 'NewPassword123!')
            ->call('updatePassword')
            ->assertSet('current_password', '')
            ->assertSet('password', '')
            ->assertSet('password_confirmation', '');
    }

    #[Test]
    public function it_renders_the_profile_view(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->assertViewIs('livewire.settings');
    }

    #[Test]
    public function it_returns_early_when_no_user_for_update_profile(): void
    {
        $component = new Profile();
        $component->updateProfile();

        $this->assertTrue(true);
    }

    #[Test]
    public function it_returns_early_when_no_user_for_update_password(): void
    {
        $component = new Profile();
        $component->updatePassword();

        $this->assertTrue(true);
    }
}
