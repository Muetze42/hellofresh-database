<?php

/** @noinspection JsonEncodingApiUsageInspection */

namespace App\Http\Clients\HelloFresh;

use App\Http\Clients\HelloFresh\Responses\MenusResponse;
use App\Http\Clients\HelloFresh\Responses\RecipeResponse;
use App\Http\Clients\HelloFresh\Responses\RecipesResponse;
use App\Models\Country;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class HelloFreshClient
{
    protected string $cacheKey = 'hellofresh_api_token';

    protected string $tokenSourceUrl = 'https://www.hellofresh.de';

    protected int $secondsPerDay = 86400;

    /**
     * Throw an exception if a server or client error occurred.
     */
    public bool $throw = true;

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function getToken(): string
    {
        $token = Cache::get($this->cacheKey);

        if ($token !== null) {
            return $token;
        }

        $tokenData = $this->fetchToken();

        $ttl = $tokenData['expires_in'] - $this->secondsPerDay;

        if ($ttl > 0) {
            Cache::put($this->cacheKey, $tokenData['token'], $ttl);
        }

        return $tokenData['token'];
    }

    /**
     * Don't throw an exception if a server or client error occurred.
     */
    public function withOutThrow(): self
    {
        $this->throw = false;

        return $this;
    }

    /**
     * Fetch a fresh token from the HelloFresh homepage.
     *
     * @return array{token: string, expires_in: int}
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    protected function fetchToken(): array
    {
        /** @var Response $response */
        $response = Http::proxy()->get($this->tokenSourceUrl);

        return $this->parseTokenFromHtml($response->throw()->body());
    }

    /**
     * Parse the token data from the HTML response.
     *
     * @return array{token: string, expires_in: int}
     *
     * @throws RuntimeException
     */
    protected function parseTokenFromHtml(string $html): array
    {
        $pattern = '/<script\s+id="__NEXT_DATA__"\s+type="application\/json">\s*({.+?})\s*<\/script>/s';

        if (! preg_match($pattern, $html, $matches)) {
            throw new RuntimeException('Could not find __NEXT_DATA__ script tag in HelloFresh HTML response');
        }

        $jsonData = json_decode($matches[1], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Failed to parse __NEXT_DATA__ JSON: ' . json_last_error_msg());
        }

        $serverAuth = $jsonData['props']['pageProps']['ssrPayload']['serverAuth'] ?? null;

        if ($serverAuth === null) {
            throw new RuntimeException('Could not find serverAuth in HelloFresh __NEXT_DATA__ JSON');
        }

        $token = $serverAuth['access_token'] ?? null;
        $expiresIn = $serverAuth['expires_in'] ?? null;

        if ($token === null || $expiresIn === null) {
            throw new RuntimeException('Missing access_token or expires_in in HelloFresh serverAuth data');
        }

        return [
            'token' => $token,
            'expires_in' => (int) $expiresIn,
        ];
    }

    /**
     * Invalidate the cached token.
     */
    public function invalidateToken(): void
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * Create a new HTTP client configured with the HelloFresh API token.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    protected function http(): PendingRequest
    {
        return Http::proxy()->withToken($this->getToken());
    }

    /**
     * Make an authenticated API request with automatic 401 retry.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    protected function request(string $url): Response
    {
        /** @var Response $response */
        $response = $this->http()->get($url);

        if ($response->unauthorized()) {
            $this->invalidateToken();
            /** @var Response $response */
            $response = $this->http()->get($url);
        }

        $response->throw();

        return $response;
    }

    /**
     * Fetch recipes from the HelloFresh API.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function getRecipes(Country $country, string $locale, int $skip = 0, ?int $take = null): RecipesResponse
    {
        $countryCode = Str::upper($country->code);

        if ($take === null) {
            $take = $country->take;
        }

        $url = sprintf(
            '%s/gw/api/recipes?country=%s&locale=%s-%s&take=%d&skip=%d',
            $country->domain,
            $countryCode,
            Str::lower($locale),
            $countryCode,
            $take,
            $skip,
        );

        return new RecipesResponse(
            $this->request($url)->toPsrResponse()
        );
    }

    /**
     * Fetch a single recipe from the HelloFresh API.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function getRecipe(Country $country, string $locale, string $recipeId): RecipeResponse
    {
        $countryCode = Str::upper($country->code);

        $url = sprintf(
            '%s/gw/api/recipes/%s?country=%s&locale=%s-%s',
            $country->domain,
            $recipeId,
            $countryCode,
            Str::lower($locale),
            $countryCode,
        );

        return new RecipeResponse(
            $this->request($url)->toPsrResponse()
        );
    }

    /**
     * Fetch menus from the HelloFresh API.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function getMenus(Country $country, string $locale, ?string $week = null): MenusResponse
    {
        $countryCode = Str::upper($country->code);

        $url = sprintf(
            '%s/gw/api/menus?country=%s&locale=%s-%s&take=200&skip=0',
            $country->domain,
            $countryCode,
            Str::lower($locale),
            $countryCode,
        );

        if ($week !== null) {
            $url .= '&week=' . $week;
        }

        return new MenusResponse(
            $this->request($url)->toPsrResponse()
        );
    }
}
