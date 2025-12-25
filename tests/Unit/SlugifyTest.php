<?php

declare(strict_types=1);

namespace Tests\Unit;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class SlugifyTest extends TestCase
{
    #[DataProvider('slugifyDataProvider')]
    public function test_slugify_generates_correct_slug(string $locale, string $input, string $expected): void
    {
        app()->setLocale($locale);

        $this->assertSame($expected, slugify($input));
    }

    /**
     * @return Iterator<string, array{locale: string, input: string, expected: string}>
     */
    public static function slugifyDataProvider(): Iterator
    {
        yield 'german with umlauts' => [
            'locale' => 'de',
            'input' => 'Würstchen mit Käse',
            'expected' => 'wuerstchen-mit-kaese',
        ];
        yield 'danish with special characters' => [
            'locale' => 'da',
            'input' => 'Blødkogt æg med rødkål',
            'expected' => 'bloedkogt-aeg-med-roedkaal',
        ];
        yield 'swedish with special characters' => [
            'locale' => 'sv',
            'input' => 'Köttbullar med gräddsås',
            'expected' => 'kottbullar-med-graddsas',
        ];
        yield 'norwegian with special characters' => [
            'locale' => 'nb',
            'input' => 'Blåbær på fjellet',
            'expected' => 'blabaer-pa-fjellet',
        ];
        yield 'dutch with special characters' => [
            'locale' => 'nl',
            'input' => 'Gëorganiseerd café',
            'expected' => 'georganiseerd-cafe',
        ];
        yield 'french with accents' => [
            'locale' => 'fr',
            'input' => 'Crème brûlée à la française',
            'expected' => 'creme-brulee-a-la-francaise',
        ];
        yield 'spanish with special characters' => [
            'locale' => 'es',
            'input' => 'Paella española con mariscos',
            'expected' => 'paella-espanola-con-mariscos',
        ];
        yield 'italian with accents' => [
            'locale' => 'it',
            'input' => 'Caffè con biscotti',
            'expected' => 'caffe-con-biscotti',
        ];
        yield 'english simple' => [
            'locale' => 'en',
            'input' => 'Fish and chips',
            'expected' => 'fish-and-chips',
        ];
        yield 'removes multiple spaces' => [
            'locale' => 'en',
            'input' => 'Hello    World',
            'expected' => 'hello-world',
        ];
        yield 'removes special characters' => [
            'locale' => 'en',
            'input' => 'Hello! World#',
            'expected' => 'hello-world',
        ];
        yield 'converts at symbol' => [
            'locale' => 'en',
            'input' => 'Hello @World',
            'expected' => 'hello-at-world',
        ];
        yield 'handles empty string' => [
            'locale' => 'en',
            'input' => '',
            'expected' => '',
        ];
    }
}
