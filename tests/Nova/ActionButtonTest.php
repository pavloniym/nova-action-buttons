<?php

declare(strict_types=1);

namespace Pavloniym\ActionButtons\Tests\Nova;

use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Field;
use Pavloniym\ActionButtons\ActionButton;
use Pavloniym\ActionButtons\ActionButtons;
use PHPUnit\Framework\TestCase;

/**
 * Runs only where laravel/nova is installed (it is a paid package and is absent in CI).
 */
final class ActionButtonTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(Field::class)) {
            $this->markTestSkipped('laravel/nova is not installed');
        }
    }

    public function test_action_and_resource_id_are_stored_in_meta(): void
    {
        $action = new class extends Action {};

        $button = ActionButton::make('Run')->action($action, 42);

        $this->assertSame($action, $button->meta['action']);
        $this->assertSame(42, $button->meta['resourceId']);
    }

    public function test_icon_meta(): void
    {
        $this->assertSame(['icon' => 'bolt', 'iconType' => 'solid'], ActionButton::make('Run')->icon('bolt', 'solid')->meta);
        $this->assertSame(['iconHtml' => '<svg></svg>'], ActionButton::make('Run')->icon('<svg></svg>')->meta);
    }

    public function test_tooltip_without_text_falls_back_to_action_name_on_the_client(): void
    {
        $this->assertSame(['hasTooltip' => true, 'tooltip' => null], ActionButton::make('Run')->tooltip()->meta);
        $this->assertSame(['hasTooltip' => true, 'tooltip' => 'Hi'], ActionButton::make('Run')->tooltip('Hi')->meta);
    }

    public function test_classes_are_a_list(): void
    {
        $this->assertSame(['classes' => ['a', 'b']], ActionButton::make('Run')->classes(['x' => 'a', 'y' => 'b'])->meta);
    }

    public function test_button_is_hidden_on_forms(): void
    {
        $button = ActionButton::make('Run');

        $this->assertFalse($button->showOnCreation);
        $this->assertFalse($button->showOnUpdate);
    }

    public function test_collection_serializes_buttons(): void
    {
        $field = ActionButtons::make('Actions')->collection([
            ActionButton::make('One')->text('1'),
            ActionButton::make('Two')->asToolbarButton(),
        ]);

        $collection = $field->meta['collection'];

        $this->assertCount(2, $collection);
        $this->assertSame('action-button', $collection[0]['component']);
        $this->assertSame('1', $collection[0]['text']);
        $this->assertTrue($collection[1]['asToolbarButton']);
        $this->assertSame('center', $field->jsonSerialize()['textAlign']);
    }
}
