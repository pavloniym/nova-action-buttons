<?php

namespace Pavloniym\ActionButtons;

use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Field;

class ActionButton extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'action-button';


    /**
     * @param Action $action
     * @param mixed $resourceId
     * @return $this
     */
    public function action(Action $action, mixed $resourceId): self
    {
        return $this->withMeta([
            'action' => $action,
            'resourceId' => $resourceId,
        ]);
    }


    /**
     * Text inside button.
     *
     * @param string $text
     * @return $this
     */
    public function text(string $text): self
    {
        return $this->withMeta(compact('text'));
    }


    /**
     * Icon inside the button.
     *
     * @param string $icon
     * @return $this
     */
    public function icon(string $icon): self
    {
        return $this->withMeta(compact('icon'));
    }


    /**
     * Apply styles to button
     *
     * @param array $styles
     * @return $this
     */
    public function styles(array $styles = []): self
    {
        return $this->withMeta(compact('styles'));
    }

    /**
     * Apply classes to button
     *
     * @param array $classes
     * @return $this
     */
    public function classes(array $classes = []): self
    {
        return $this->withMeta(compact('classes'));
    }


    /**
     * Apply toolbar button styles
     *
     * @return $this
     */
    public function asToolbarButton(): self
    {
        return $this->withMeta(['asToolbarButton' => true]);
    }
}
