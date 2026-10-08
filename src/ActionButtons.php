<?php

declare(strict_types=1);

namespace Pavloniym\ActionButtons;

use Laravel\Nova\Fields\Field;

class ActionButtons extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'action-buttons';

    /**
     * The text alignment for the field's text in tables.
     *
     * @var string
     */
    public $textAlign = 'center';

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
     * Buttons to render side by side.
     *
     * @param array<int, ActionButton> $collection
     */
    public function collection(array $collection): static
    {
        return $this->withMeta([
            'collection' => collect($collection)
                ->map(fn (ActionButton $actionButton) => $actionButton->jsonSerialize())
                ->values()
                ->all(),
        ]);
    }
}
