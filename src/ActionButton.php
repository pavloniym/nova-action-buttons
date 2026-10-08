<?php

declare(strict_types=1);

namespace Pavloniym\ActionButtons;

use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Field;
use Pavloniym\ActionButtons\Support\IconMeta;

class ActionButton extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'action-button';

    /**
     * Indicates if the element should be shown on the update view.
     *
     * @var bool
     */
    public $showOnUpdate = false;

    /**
     * Indicates if the element should be shown on the creation view.
     *
     * @var bool
     */
    public $showOnCreation = false;

    /**
     * Action to run and the resource it runs on.
     * The action must also be registered in the resource's actions() method.
     */
    public function action(Action $action, mixed $resourceId): static
    {
        return $this->withMeta([
            'action' => $action,
            'resourceId' => $resourceId,
        ]);
    }

    /**
     * Text inside button.
     */
    public function text(string $text): static
    {
        return $this->withMeta(['text' => $text]);
    }

    /**
     * Heroicons v2 icon inside the button, e.g. icon('bolt') or icon('bolt', 'solid').
     * Heroicons v1 names (e.g. "lightning-bolt") are mapped to v2 by Nova; raw SVG markup is rendered as iconHtml().
     */
    public function icon(string $icon, IconType|string $type = IconType::Outline): static
    {
        return $this->withMeta(IconMeta::make($icon, $type));
    }

    /**
     * Icon url inside the button.
     */
    public function iconUrl(string $iconUrl): static
    {
        return $this->withMeta(['iconUrl' => $iconUrl]);
    }

    /**
     * Icon html (e.g. inline SVG) inside the button.
     */
    public function iconHtml(string $iconHtml): static
    {
        return $this->withMeta(['iconHtml' => $iconHtml]);
    }

    /**
     * Inline css styles, merged over the default look.
     *
     * @param array<string, string> $styles
     */
    public function styles(array $styles = []): static
    {
        return $this->withMeta(['styles' => $styles]);
    }

    /**
     * Css classes that replace the default look of the button.
     *
     * @param array<int, string> $classes
     */
    public function classes(array $classes = []): static
    {
        return $this->withMeta(['classes' => array_values($classes)]);
    }

    /**
     * Look like Nova's icon buttons in a table row (view / edit / delete).
     * This only changes the style: the button stays in the field's column.
     */
    public function asToolbarButton(): static
    {
        return $this->withMeta(['asToolbarButton' => true]);
    }

    /**
     * Show a tooltip on hover; defaults to the action name.
     */
    public function tooltip(?string $tooltip = null): static
    {
        return $this->withMeta(['hasTooltip' => true, 'tooltip' => $tooltip]);
    }
}
