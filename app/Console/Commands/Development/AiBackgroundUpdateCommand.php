<?php

namespace App\Console\Commands\Development;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Artisan;
use JsonException;
use Laravel\Boost\Boost;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'development:ai-background-update')]
class AiBackgroundUpdateCommand extends Command implements PromptsForMissingInput
{
    /**
     * The files with MCP server data.
     *
     * @var string[]
     */
    protected array $mcpFiles = [
        '.mcp.json',
        '.junie/mcp/mcp.json',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'development:ai-background-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync AI guidelines and MCP server data';

    /**
     * Execute the console command.
     *
     * @throws JsonException
     */
    public function handle(): void
    {
        if (! class_exists(Boost::class) || app()->isProduction()) {
            return;
        }

        $arguments = [
            '--silent' => true,
            '--ansi' => true,
        ];

        if (is_file(base_path('.mcp.json'))) {
            $arguments['--ignore-mcp'] = true;
        }

        // $this->call('boost:install --silent');
        Artisan::call('boost:install', $arguments);

        $this->mcpServers();
    }

    /**
     * Sets up the MCP servers by invoking necessary configuration methods.
     *
     * @throws JsonException
     */
    protected function mcpServers(): void
    {
        $this->context7mcpServer();
    }

    /**
     * Configures the Context7 MCP server by updating relevant configuration files.
     *
     * @throws JsonException
     */
    protected function context7mcpServer(): void
    {
        $key = config('services.context7.key');

        if (! is_string($key) || $key === '') {
            return;
        }

        $files = array_map(base_path(...), $this->mcpFiles);

        foreach ($files as $file) {
            if (! is_file($file)) {
                continue;
            }

            $contents = file_get_contents($file);

            if (! is_string($contents)) {
                continue;
            }

            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

            $data['mcpServers']['context7'] = [
                'command' => 'npx',
                'args' => [
                    '-y',
                    '@upstash/context7-mcp',
                    '--api-key',
                    trim($key),
                ],
            ];

            file_put_contents($file, json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        }
    }
}
