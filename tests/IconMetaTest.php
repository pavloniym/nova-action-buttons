<?php

declare(strict_types=1);

namespace Pavloniym\ActionButtons\Tests;

use InvalidArgumentException;
use Pavloniym\ActionButtons\IconType;
use Pavloniym\ActionButtons\Support\IconMeta;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class IconMetaTest extends TestCase
{
    public function test_name_defaults_to_outline(): void
    {
        $this->assertSame(['icon' => 'bolt', 'iconType' => 'outline'], IconMeta::make('bolt'));
    }

    public function test_heroicons_v1_name_is_passed_to_nova_as_is(): void
    {
        // Nova's <Icon> maps v1 names (lightning-bolt -> bolt) itself.
        $this->assertSame(['icon' => 'lightning-bolt', 'iconType' => 'outline'], IconMeta::make('lightning-bolt'));
    }

    public function test_name_is_trimmed(): void
    {
        $this->assertSame(['icon' => 'bolt', 'iconType' => 'outline'], IconMeta::make('  bolt '));
    }

    #[DataProvider('types')]
    public function test_type_accepts_enum_and_string(IconType|string $type, string $expected): void
    {
        $this->assertSame(['icon' => 'bolt', 'iconType' => $expected], IconMeta::make('bolt', $type));
    }

    public static function types(): array
    {
        return [
            'enum solid' => [IconType::Solid, 'solid'],
            'string mini' => ['mini', 'mini'],
            'string micro' => ['micro', 'micro'],
            'string outline' => ['outline', 'outline'],
        ];
    }

    public function test_svg_markup_becomes_icon_html(): void
    {
        $svg = '<svg viewBox="0 0 24 24"><path d="M0 0"/></svg>';

        $this->assertSame(['iconHtml' => $svg], IconMeta::make("\n  ".$svg));
    }

    public function test_svg_markup_ignores_type(): void
    {
        $this->assertSame(['iconHtml' => '<svg></svg>'], IconMeta::make('<svg></svg>', 'unknown'));
    }

    public function test_empty_name_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        IconMeta::make('   ');
    }

    public function test_unknown_type_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid icon type "filled", expected one of: outline, solid, mini, micro.');

        IconMeta::make('bolt', 'filled');
    }
}
