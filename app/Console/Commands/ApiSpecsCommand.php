<?php

namespace App\Console\Commands;

use App\Livewire\Portal\Docs\AbstractEndpointDoc;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Finder\Finder;

#[AsCommand(name: 'api:specs')]
class ApiSpecsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:specs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate OpenAPI and Postman collection specs from API documentation';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $endpoints = $this->collectEndpoints();

        $this->generateOpenApiSpec($endpoints);
        $this->generatePostmanCollection($endpoints);

        $this->components->info('API specs generated successfully.');
    }

    /**
     * Collect endpoint data from all Doc classes.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function collectEndpoints(): array
    {
        $endpoints = [];
        $docClasses = $this->getDocClasses();

        foreach ($docClasses as $docClass) {
            $instance = resolve($docClass);
            $reflection = new ReflectionClass($instance);

            $endpoints[] = [
                'title' => $this->callProtectedMethod($instance, $reflection, 'title'),
                'description' => $this->callProtectedMethod($instance, $reflection, 'description'),
                'endpoints' => $this->callProtectedMethod($instance, $reflection, 'endpoints'),
                'queryParams' => $this->callProtectedMethod($instance, $reflection, 'queryParams'),
                'responseFields' => $this->callProtectedMethod($instance, $reflection, 'responseFields'),
            ];
        }

        return $endpoints;
    }

    /**
     * Get all Doc classes extending AbstractEndpointDoc.
     *
     * @return array<int, class-string>
     */
    protected function getDocClasses(): array
    {
        $path = app_path('Livewire/Portal/Docs');
        $namespace = 'App\\Livewire\\Portal\\Docs';

        $finder = Finder::create()
            ->files()
            ->name('*Doc.php')
            ->notName('AbstractEndpointDoc.php')
            ->notName('GetStartedDoc.php')
            ->in($path);

        $classes = [];

        foreach ($finder as $file) {
            $className = $namespace . '\\' . $file->getBasename('.php');

            if (class_exists($className) && is_subclass_of($className, AbstractEndpointDoc::class)) {
                $classes[] = $className;
            }
        }

        return $classes;
    }

    /**
     * Call a protected method on the Doc instance.
     *
     * @param  ReflectionClass<object>  $reflection
     */
    protected function callProtectedMethod(object $instance, ReflectionClass $reflection, string $methodName): mixed
    {
        if (! $reflection->hasMethod($methodName)) {
            return null;
        }

        $method = $reflection->getMethod($methodName);

        return $method->invoke($instance);
    }

    /**
     * Generate OpenAPI 3.0 specification.
     *
     * @param  array<int, array<string, mixed>>  $endpoints
     */
    protected function generateOpenApiSpec(array $endpoints): void
    {
        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => config('app.name') . ' API',
                'description' => 'API for accessing HelloFresh recipe data',
                'version' => config('api.version'),
                'contact' => [
                    'name' => 'Norman Huth',
                    'url' => config('app.url'),
                ],
            ],
            'servers' => [
                [
                    'url' => 'https://' . config('api.domain_name') . '/{locale}-{country}',
                    'variables' => [
                        'locale' => [
                            'default' => 'en',
                            'description' => 'Language code (e.g., en, de, fr)',
                        ],
                        'country' => [
                            'default' => 'US',
                            'description' => 'Country code (e.g., US, DE, GB)',
                        ],
                    ],
                ],
            ],
            'security' => [
                ['bearerAuth' => []],
            ],
            'paths' => [],
            'components' => [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'API Token',
                    ],
                ],
            ],
        ];

        foreach ($endpoints as $endpoint) {
            foreach ($endpoint['endpoints'] as $endpointItem) {
                $path = $this->convertPathToOpenApi($endpointItem['path']);
                $method = strtolower((string) $endpointItem['method']);

                $operation = [
                    'summary' => $endpoint['title'],
                    'description' => $endpoint['description'],
                    'tags' => [$this->extractTag($endpoint['title'])],
                    'responses' => [
                        '200' => [
                            'description' => 'Successful response',
                            'content' => [
                                'application/json' => [
                                    'schema' => $this->buildResponseSchema($endpoint['responseFields']),
                                ],
                            ],
                        ],
                        '401' => ['description' => 'Unauthorized'],
                        '404' => ['description' => 'Not found'],
                        '429' => ['description' => 'Too many requests'],
                    ],
                ];

                if (! empty($endpoint['queryParams'])) {
                    $operation['parameters'] = $this->buildQueryParameters($endpoint['queryParams']);
                }

                $pathParams = $this->extractPathParameters($endpointItem['path']);
                if ($pathParams !== []) {
                    $operation['parameters'] = array_merge(
                        $operation['parameters'] ?? [],
                        $pathParams
                    );
                }

                $spec['paths'][$path][$method] = $operation;
            }
        }

        Storage::disk('local')->put(
            'api-docs/openapi/openapi.json',
            (string) json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->components->twoColumnDetail('OpenAPI spec', Storage::disk('local')->path('api-docs/openapi/openapi.json'));
    }

    /**
     * Generate Postman collection.
     *
     * @param  array<int, array<string, mixed>>  $endpoints
     */
    protected function generatePostmanCollection(array $endpoints): void
    {
        $collection = [
            'info' => [
                'name' => config('app.name') . ' API',
                'description' => 'API for accessing HelloFresh recipe data',
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'auth' => [
                'type' => 'bearer',
                'bearer' => [
                    [
                        'key' => 'token',
                        'value' => '{{API_TOKEN}}',
                        'type' => 'string',
                    ],
                ],
            ],
            'variable' => [
                [
                    'key' => 'BASE_URL',
                    'value' => 'https://' . config('api.domain_name'),
                ],
                [
                    'key' => 'LOCALE',
                    'value' => 'en',
                ],
                [
                    'key' => 'COUNTRY',
                    'value' => 'US',
                ],
                [
                    'key' => 'API_TOKEN',
                    'value' => 'YOUR_API_TOKEN',
                ],
            ],
            'item' => [],
        ];

        foreach ($endpoints as $endpoint) {
            foreach ($endpoint['endpoints'] as $endpointItem) {
                $item = [
                    'name' => $endpoint['title'],
                    'request' => [
                        'method' => $endpointItem['method'],
                        'header' => [
                            [
                                'key' => 'Accept',
                                'value' => 'application/json',
                            ],
                        ],
                        'url' => [
                            'raw' => '{{BASE_URL}}/{{LOCALE}}-{{COUNTRY}}' . $this->convertPathToPostman($endpointItem['path']),
                            'host' => ['{{BASE_URL}}'],
                            'path' => $this->buildPostmanPath($endpointItem['path']),
                        ],
                        'description' => $endpoint['description'],
                    ],
                ];

                if (! empty($endpoint['queryParams'])) {
                    $item['request']['url']['query'] = $this->buildPostmanQueryParams($endpoint['queryParams']);
                }

                $collection['item'][] = $item;
            }
        }

        Storage::disk('local')->put(
            'api-docs/postman/collection.json',
            (string) json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->components->twoColumnDetail('Postman collection', Storage::disk('local')->path('api-docs/postman/collection.json'));
    }

    /**
     * Convert path from docs format to OpenAPI format.
     */
    protected function convertPathToOpenApi(string $path): string
    {
        // Remove locale-country prefix as it's in server URL
        $path = preg_replace('#^/\{locale\}-\{country\}#', '', $path);

        return $path ?: '/';
    }

    /**
     * Convert path from docs format to Postman format.
     */
    protected function convertPathToPostman(string $path): string
    {
        // Remove locale-country prefix as it's handled separately
        $path = (string) preg_replace('#^/\{locale\}-\{country\}#', '', $path);

        // Convert {param} to :param
        return (string) preg_replace('/\{([^}]+)\}/', ':$1', $path);
    }

    /**
     * Build Postman path array.
     *
     * @return array<int, string>
     */
    protected function buildPostmanPath(string $path): array
    {
        $path = preg_replace('#^/\{locale\}-\{country\}#', '', $path);
        $path = preg_replace('/\{([^}]+)\}/', ':$1', (string) $path) ?? $path;

        $segments = array_filter(explode('/', (string) $path));

        return array_merge(['{{LOCALE}}-{{COUNTRY}}'], array_values($segments));
    }

    /**
     * Build query parameters for OpenAPI.
     *
     * @param  array<int, array<string, string>>  $params
     * @return array<int, array<string, mixed>>
     */
    protected function buildQueryParameters(array $params): array
    {
        return array_map(static fn (array $param): array => [
            'name' => $param['name'],
            'in' => 'query',
            'required' => false,
            'description' => $param['description'],
            'schema' => [
                'type' => match ($param['type']) {
                    'integer' => 'integer',
                    'boolean' => 'boolean',
                    default => 'string',
                },
            ],
        ], $params);
    }

    /**
     * Build Postman query params.
     *
     * @param  array<int, array<string, string>>  $params
     * @return array<int, array<string, mixed>>
     */
    protected function buildPostmanQueryParams(array $params): array
    {
        return array_map(static fn (array $param): array => [
            'key' => $param['name'],
            'value' => '',
            'description' => $param['description'],
            'disabled' => true,
        ], $params);
    }

    /**
     * Extract path parameters from path.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractPathParameters(string $path): array
    {
        $params = [];

        // Remove locale-country as it's in server URL
        $path = preg_replace('#^/\{locale\}-\{country\}#', '', $path);

        preg_match_all('/\{([^}]+)\}/', $path ?? '', $matches);

        foreach ($matches[1] as $param) {
            $params[] = [
                'name' => $param,
                'in' => 'path',
                'required' => true,
                'schema' => [
                    'type' => $param === 'id' || $param === 'year_week' ? 'integer' : 'string',
                ],
            ];
        }

        return $params;
    }

    /**
     * Build response schema from response fields.
     *
     * @param  array<int, array<string, string>>  $fields
     * @return array<string, mixed>
     */
    protected function buildResponseSchema(array $fields): array
    {
        $properties = [];

        foreach ($fields as $field) {
            $properties[$field['name']] = [
                'type' => $this->mapFieldType($field['type']),
                'description' => $field['description'],
            ];

            if (str_contains($field['type'], 'null')) {
                $properties[$field['name']]['nullable'] = true;
            }
        }

        return [
            'type' => 'object',
            'properties' => $properties,
        ];
    }

    /**
     * Map doc field type to OpenAPI type.
     */
    protected function mapFieldType(string $type): string
    {
        $type = str_replace('|null', '', $type);

        return match ($type) {
            'integer', 'int' => 'integer',
            'boolean', 'bool' => 'boolean',
            'array' => 'array',
            'object' => 'object',
            'datetime', 'date' => 'string',
            default => 'string',
        };
    }

    /**
     * Extract tag from endpoint title.
     */
    protected function extractTag(string $title): string
    {
        // "List Recipes" -> "Recipes", "Get Recipe" -> "Recipes", "Allergens API" -> "Allergens"
        $title = str_replace([' API', 'List ', 'Get '], '', $title);

        // Singularize if needed
        if (! str_ends_with($title, 's')) {
            $title .= 's';
        }

        return $title;
    }
}
