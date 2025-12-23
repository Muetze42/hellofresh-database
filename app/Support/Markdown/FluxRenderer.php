<?php

namespace App\Support\Markdown;

use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Node\Block\ThematicBreak;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;
use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableRow;
use League\CommonMark\Extension\Table\TableSection;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;

class FluxRenderer
{
    /**
     * Render a CommonMark document to Flux UI HTML.
     */
    public function render(Document $document): string
    {
        return $this->renderNode($document);
    }

    /**
     * Render a node and its children.
     */
    protected function renderNode(Node $node): string
    {
        return $this->renderBlockNode($node)
            ?? $this->renderInlineNode($node)
            ?? $this->renderChildren($node);
    }

    /**
     * Render block-level nodes.
     */
    protected function renderBlockNode(Node $node): ?string
    {
        return match (true) {
            $node instanceof Document => $this->renderChildren($node),
            $node instanceof Heading => $this->renderHeading($node),
            $node instanceof Paragraph => $this->renderParagraph($node),
            $node instanceof ListBlock => $this->renderList($node),
            $node instanceof ListItem => $this->renderListItem($node),
            $node instanceof ThematicBreak => $this->renderThematicBreak(),
            $node instanceof FencedCode => $this->renderCodeBlock($node),
            $node instanceof IndentedCode => $this->renderIndentedCode($node),
            $node instanceof BlockQuote => $this->renderBlockQuote($node),
            $node instanceof Table => $this->renderTable($node),
            $node instanceof TableSection => $this->renderTableSection($node),
            $node instanceof TableRow => $this->renderTableRow($node),
            $node instanceof TableCell => $this->renderTableCell($node),
            $node instanceof HtmlBlock => $this->renderHtmlBlock($node),
            default => null,
        };
    }

    /**
     * Render inline nodes.
     */
    protected function renderInlineNode(Node $node): ?string
    {
        return match (true) {
            $node instanceof Text => $this->renderText($node),
            $node instanceof Strong => $this->renderStrong($node),
            $node instanceof Emphasis => $this->renderEmphasis($node),
            $node instanceof Link => $this->renderLink($node),
            $node instanceof Code => $this->renderInlineCode($node),
            $node instanceof Newline => $this->renderNewline($node),
            $node instanceof HtmlInline => $this->renderHtmlInline($node),
            default => null,
        };
    }

    /**
     * Render all children of a node.
     */
    protected function renderChildren(Node $node): string
    {
        $output = '';

        foreach ($node->children() as $child) {
            $output .= $this->renderNode($child);
        }

        return $output;
    }

    /**
     * Render a heading element.
     */
    protected function renderHeading(Heading $heading): string
    {
        $level = $heading->getLevel();
        $content = $this->renderChildren($heading);

        $size = match ($level) {
            1 => 'xl',
            2 => 'lg',
            default => 'base',
        };

        return sprintf(
            '<flux:heading size="%s" level="%d">%s</flux:heading>' . "\n",
            $size,
            $level,
            $content
        );
    }

    /**
     * Render a paragraph element.
     */
    protected function renderParagraph(Paragraph $paragraph): string
    {
        $content = $this->renderChildren($paragraph);

        return sprintf('<flux:text>%s</flux:text>' . "\n", $content);
    }

    /**
     * Render a text node.
     */
    protected function renderText(Text $text): string
    {
        return e($text->getLiteral());
    }

    /**
     * Render strong/bold text.
     */
    protected function renderStrong(Strong $strong): string
    {
        return sprintf('<strong>%s</strong>', $this->renderChildren($strong));
    }

    /**
     * Render emphasized/italic text.
     */
    protected function renderEmphasis(Emphasis $emphasis): string
    {
        return sprintf('<em>%s</em>', $this->renderChildren($emphasis));
    }

    /**
     * Render a link.
     */
    protected function renderLink(Link $link): string
    {
        $url = $link->getUrl();
        $content = $this->renderChildren($link);
        $external = $this->isExternalUrl($url);
        $externalAttr = $external ? ' external' : '';

        return sprintf('<flux:link href="%s"%s>%s</flux:link>', e($url), $externalAttr, $content);
    }

    /**
     * Render a list (ordered or unordered).
     */
    protected function renderList(ListBlock $list): string
    {
        $tag = $list->getListData()->type === ListBlock::TYPE_ORDERED ? 'ol' : 'ul';
        $content = $this->renderChildren($list);

        return sprintf('<%s class="ml-6 list-disc space-y-2">%s</%s>' . "\n", $tag, $content, $tag);
    }

    /**
     * Render a list item.
     */
    protected function renderListItem(ListItem $item): string
    {
        $content = $this->renderListItemContent($item);

        return sprintf('<li class="text-zinc-600 dark:text-zinc-400">%s</li>' . "\n", $content);
    }

    /**
     * Render list item content without wrapping paragraph tags.
     */
    protected function renderListItemContent(ListItem $item): string
    {
        $output = '';

        foreach ($item->children() as $child) {
            if ($child instanceof Paragraph) {
                $output .= $this->renderChildren($child);

                continue;
            }

            $output .= $this->renderNode($child);
        }

        return $output;
    }

    /**
     * Render a thematic break (horizontal rule).
     */
    protected function renderThematicBreak(): string
    {
        return '<flux:separator />' . "\n";
    }

    /**
     * Render inline code.
     */
    protected function renderInlineCode(Code $code): string
    {
        return sprintf(
            '<code class="rounded bg-zinc-100 px-1.5 py-0.5 text-sm dark:bg-zinc-800">%s</code>',
            e($code->getLiteral())
        );
    }

    /**
     * Render a fenced code block.
     */
    protected function renderCodeBlock(FencedCode $code): string
    {
        return sprintf(
            '<pre class="overflow-x-auto rounded-lg bg-zinc-100 p-4 dark:bg-zinc-800"><code>%s</code></pre>' . "\n",
            e($code->getLiteral())
        );
    }

    /**
     * Render an indented code block.
     */
    protected function renderIndentedCode(IndentedCode $code): string
    {
        return sprintf(
            '<pre class="overflow-x-auto rounded-lg bg-zinc-100 p-4 dark:bg-zinc-800"><code>%s</code></pre>' . "\n",
            e($code->getLiteral())
        );
    }

    /**
     * Render a blockquote.
     */
    protected function renderBlockQuote(BlockQuote $quote): string
    {
        $content = $this->renderChildren($quote);

        return sprintf(
            '<flux:callout>%s</flux:callout>' . "\n",
            $content
        );
    }

    /**
     * Render a table.
     */
    protected function renderTable(Table $table): string
    {
        $content = $this->renderChildren($table);

        return sprintf('<flux:table>%s</flux:table>' . "\n", $content);
    }

    /**
     * Render a table section (thead/tbody).
     */
    protected function renderTableSection(TableSection $section): string
    {
        $content = $this->renderChildren($section);

        if ($section->isHead()) {
            return sprintf('<flux:table.columns>%s</flux:table.columns>' . "\n", $content);
        }

        return sprintf('<flux:table.rows>%s</flux:table.rows>' . "\n", $content);
    }

    /**
     * Render a table row.
     */
    protected function renderTableRow(TableRow $row): string
    {
        $content = $this->renderChildren($row);

        return sprintf('<flux:table.row>%s</flux:table.row>' . "\n", $content);
    }

    /**
     * Render a table cell.
     */
    protected function renderTableCell(TableCell $cell): string
    {
        $content = $this->renderChildren($cell);

        if ($cell->getType() === TableCell::TYPE_HEADER) {
            return sprintf('<flux:table.column>%s</flux:table.column>', $content);
        }

        return sprintf('<flux:table.cell>%s</flux:table.cell>', $content);
    }

    /**
     * Render a newline.
     */
    protected function renderNewline(Newline $newline): string
    {
        if ($newline->getType() === Newline::HARDBREAK) {
            return '<br />';
        }

        return ' ';
    }

    /**
     * Render an HTML block.
     */
    protected function renderHtmlBlock(HtmlBlock $block): string
    {
        return $block->getLiteral();
    }

    /**
     * Render inline HTML.
     */
    protected function renderHtmlInline(HtmlInline $inline): string
    {
        return $inline->getLiteral();
    }

    /**
     * Check if a URL is external.
     */
    protected function isExternalUrl(string $url): bool
    {
        if (str_starts_with($url, '#') || str_starts_with($url, '/') || str_starts_with($url, 'mailto:')) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);

        if ($host === null || $host === false) {
            return false;
        }

        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);

        return $host !== $appHost;
    }
}
