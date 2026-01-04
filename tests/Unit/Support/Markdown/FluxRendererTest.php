<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Markdown;

use App\Support\Markdown\FluxRenderer;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Parser\MarkdownParser;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class FluxRendererTest extends TestCase
{
    private FluxRenderer $fluxRenderer;

    private MarkdownParser $markdownParser;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->fluxRenderer = new FluxRenderer();

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());

        $this->markdownParser = new MarkdownParser($environment);
    }

    protected function parse(string $markdown): Document
    {
        return $this->markdownParser->parse($markdown);
    }

    #[Test]
    public function it_renders_heading_level_1(): void
    {
        $document = $this->parse('# Heading 1');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<flux:heading size="xl" level="1">Heading 1</flux:heading>', $output);
    }

    #[Test]
    public function it_renders_heading_level_2(): void
    {
        $document = $this->parse('## Heading 2');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<flux:heading size="lg" level="2">Heading 2</flux:heading>', $output);
    }

    #[Test]
    public function it_renders_heading_level_3_and_above_with_base_size(): void
    {
        $document = $this->parse('### Heading 3');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<flux:heading size="base" level="3">Heading 3</flux:heading>', $output);
    }

    #[Test]
    public function it_renders_heading_level_4_with_base_size(): void
    {
        $document = $this->parse('#### Heading 4');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<flux:heading size="base" level="4">Heading 4</flux:heading>', $output);
    }

    #[Test]
    public function it_renders_paragraph(): void
    {
        $document = $this->parse('This is a paragraph.');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<flux:text>This is a paragraph.</flux:text>', $output);
    }

    #[Test]
    public function it_renders_strong_text(): void
    {
        $document = $this->parse('This is **bold** text.');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<strong>bold</strong>', $output);
    }

    #[Test]
    public function it_renders_emphasis_text(): void
    {
        $document = $this->parse('This is *italic* text.');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<em>italic</em>', $output);
    }

    #[Test]
    public function it_renders_internal_link(): void
    {
        config(['app.url' => 'https://example.com']);

        $document = $this->parse('[Link](/path/to/page)');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('href="/path/to/page"', $output);
        $this->assertStringContainsString('data-flux-link', $output);
        $this->assertStringContainsString('>Link</a>', $output);
        $this->assertStringNotContainsString('target="_blank"', $output);
    }

    #[Test]
    public function it_renders_external_link_with_target_blank(): void
    {
        config(['app.url' => 'https://example.com']);

        $document = $this->parse('[External](https://other.com/page)');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('href="https://other.com/page"', $output);
        $this->assertStringContainsString('target="_blank"', $output);
        $this->assertStringContainsString('>External</a>', $output);
    }

    #[Test]
    public function it_renders_anchor_link_as_internal(): void
    {
        $document = $this->parse('[Anchor](#section)');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('href="#section"', $output);
        $this->assertStringContainsString('data-flux-link', $output);
        $this->assertStringContainsString('>Anchor</a>', $output);
        $this->assertStringNotContainsString('target="_blank"', $output);
    }

    #[Test]
    public function it_renders_mailto_link_as_internal(): void
    {
        $document = $this->parse('[Email](mailto:test@example.com)');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('href="mailto:test@example.com"', $output);
        $this->assertStringContainsString('data-flux-link', $output);
        $this->assertStringContainsString('>Email</a>', $output);
        $this->assertStringNotContainsString('target="_blank"', $output);
    }

    #[Test]
    public function it_renders_unordered_list(): void
    {
        $document = $this->parse("- Item 1\n- Item 2");
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<ul class="ml-6 list-disc space-y-2">', $output);
        $this->assertStringContainsString('<li class="text-zinc-600 dark:text-zinc-400">Item 1</li>', $output);
        $this->assertStringContainsString('<li class="text-zinc-600 dark:text-zinc-400">Item 2</li>', $output);
    }

    #[Test]
    public function it_renders_ordered_list(): void
    {
        $document = $this->parse("1. First\n2. Second");
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<ol class="ml-6 list-disc space-y-2">', $output);
        $this->assertStringContainsString('<li class="text-zinc-600 dark:text-zinc-400">First</li>', $output);
    }

    #[Test]
    public function it_renders_thematic_break(): void
    {
        $document = $this->parse("Above\n\n---\n\nBelow");
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<flux:separator />', $output);
    }

    #[Test]
    public function it_renders_inline_code(): void
    {
        $document = $this->parse('Use `inline code` here.');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<code class="rounded bg-zinc-100 px-1.5 py-0.5 text-sm dark:bg-zinc-800">inline code</code>', $output);
    }

    #[Test]
    public function it_renders_fenced_code_block(): void
    {
        $document = $this->parse("```php\necho 'Hello';\n```");
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<pre class="overflow-x-auto rounded-lg bg-zinc-100 p-4 dark:bg-zinc-800"><code>', $output);
        $this->assertStringContainsString('echo &#039;Hello&#039;;', $output);
    }

    #[Test]
    public function it_renders_indented_code_block(): void
    {
        $document = $this->parse('    indented code');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<pre class="overflow-x-auto rounded-lg bg-zinc-100 p-4 dark:bg-zinc-800"><code>', $output);
        $this->assertStringContainsString('indented code', $output);
    }

    #[Test]
    public function it_renders_blockquote(): void
    {
        $document = $this->parse('> This is a quote');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<flux:callout>', $output);
        $this->assertStringContainsString('This is a quote', $output);
        $this->assertStringContainsString('</flux:callout>', $output);
    }

    #[Test]
    public function it_renders_table(): void
    {
        $markdown = "| Header 1 | Header 2 |\n| --- | --- |\n| Cell 1 | Cell 2 |";
        $document = $this->parse($markdown);
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<flux:table>', $output);
        $this->assertStringContainsString('<flux:table.columns>', $output);
        $this->assertStringContainsString('<flux:table.column>Header 1</flux:table.column>', $output);
        $this->assertStringContainsString('<flux:table.rows>', $output);
        $this->assertStringContainsString('<flux:table.cell>Cell 1</flux:table.cell>', $output);
    }

    #[Test]
    public function it_renders_table_header_cells(): void
    {
        $markdown = "| Header |\n| --- |\n| Cell |";
        $document = $this->parse($markdown);
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<flux:table.column>Header</flux:table.column>', $output);
    }

    #[Test]
    public function it_renders_table_body_cells(): void
    {
        $markdown = "| Header |\n| --- |\n| Cell |";
        $document = $this->parse($markdown);
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<flux:table.cell>Cell</flux:table.cell>', $output);
    }

    #[Test]
    public function it_renders_hard_break(): void
    {
        $document = $this->parse("Line 1  \nLine 2");
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<br />', $output);
    }

    #[Test]
    public function it_renders_soft_break_as_space(): void
    {
        $document = $this->parse("Line 1\nLine 2");
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('Line 1 Line 2', $output);
    }

    #[Test]
    public function it_renders_html_block(): void
    {
        $document = $this->parse("<div>Custom HTML</div>\n\n");
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<div>Custom HTML</div>', $output);
    }

    #[Test]
    public function it_renders_inline_html(): void
    {
        $document = $this->parse('Text with <span>inline HTML</span> here.');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<span>inline HTML</span>', $output);
    }

    #[Test]
    public function it_escapes_text_content(): void
    {
        $document = $this->parse('Text with special chars: & < >');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('&amp;', $output);
        $this->assertStringContainsString('&lt;', $output);
        $this->assertStringContainsString('&gt;', $output);
    }

    #[Test]
    public function it_escapes_link_url(): void
    {
        $document = $this->parse('[Link](https://example.com/path?foo=bar&baz=qux)');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('href="https://example.com/path?foo=bar&amp;baz=qux"', $output);
    }

    #[Test]
    public function it_escapes_code_block_content(): void
    {
        $document = $this->parse("```\n<script>alert('xss')</script>\n```");
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('&lt;script&gt;', $output);
    }

    #[Test]
    public function it_handles_empty_document(): void
    {
        $document = $this->parse('');
        $output = $this->fluxRenderer->render($document);

        $this->assertSame('', $output);
    }

    #[Test]
    public function it_handles_nested_formatting(): void
    {
        $document = $this->parse('This is ***bold and italic*** text.');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<strong>', $output);
        $this->assertStringContainsString('<em>', $output);
    }

    #[Test]
    public function it_treats_relative_url_as_internal(): void
    {
        config(['app.url' => 'https://example.com']);

        $document = $this->parse('[Link](relative/path)');
        $output = $this->fluxRenderer->render($document);

        // Relative URLs without leading slash are treated as external by parse_url
        $this->assertStringContainsString('href="relative/path"', $output);
    }

    #[Test]
    public function it_treats_same_domain_link_as_internal(): void
    {
        config(['app.url' => 'https://example.com']);

        $document = $this->parse('[Link](https://example.com/page)');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringNotContainsString('external', $output);
    }

    #[Test]
    public function it_handles_list_item_with_nested_content(): void
    {
        $document = $this->parse('- Item with **bold** and *italic*');
        $output = $this->fluxRenderer->render($document);

        $this->assertStringContainsString('<li class="text-zinc-600 dark:text-zinc-400">Item with <strong>bold</strong> and <em>italic</em></li>', $output);
    }

    #[Test]
    public function it_handles_url_with_no_host(): void
    {
        $document = $this->parse('[Link](javascript:void(0))');
        $output = $this->fluxRenderer->render($document);

        // URLs without a host should not be marked external
        $this->assertStringNotContainsString(' external', $output);
    }
}
