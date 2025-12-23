<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\DataMaintenance;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Override;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

final class UpdateDisposableEmailDomainsCommandTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    #[Test]
    public function it_downloads_and_stores_disposable_email_domains(): void
    {
        $domains = ['tempmail.com', 'throwaway.email', 'fakeinbox.com'];
        $jsonResponse = json_encode($domains);

        Http::fake([
            'cdn.jsdelivr.net/*' => Http::response($jsonResponse, 200),
        ]);

        $this->artisan('data-maintenance:update-disposable-email-domains')
            ->assertSuccessful();

        Storage::assertExists('disposable-email-domains.json');
        $this->assertSame($jsonResponse, Storage::get('disposable-email-domains.json'));

        Http::assertSent(function (Request $request): bool {
            return str_contains($request->url(), 'disposable-email-domains');
        });
    }

    #[Test]
    public function it_throws_exception_when_response_is_not_valid_json(): void
    {
        Http::fake([
            'cdn.jsdelivr.net/*' => Http::response('not valid json {{{', 200),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid JSON response from source');

        $this->artisan('data-maintenance:update-disposable-email-domains');
    }

    #[Test]
    public function it_throws_exception_when_request_fails(): void
    {
        Http::fake([
            'cdn.jsdelivr.net/*' => Http::response('Not Found', 404),
        ]);

        $this->expectException(RequestException::class);

        $this->artisan('data-maintenance:update-disposable-email-domains');
    }

    #[Test]
    public function it_throws_exception_on_server_error(): void
    {
        Http::fake([
            'cdn.jsdelivr.net/*' => Http::response('Internal Server Error', 500),
        ]);

        $this->expectException(RequestException::class);

        $this->artisan('data-maintenance:update-disposable-email-domains');
    }
}
