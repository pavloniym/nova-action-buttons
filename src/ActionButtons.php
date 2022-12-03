<?php

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
     * @param array<ActionButton> $collection
     * @return $this
     */
    public function collection(array $collection): self
    {
        return $this->withMeta([
            'collection' => collect($collection)
                ->map(fn(ActionButton $actionButton) => $actionButton->jsonSerialize())
                ->toArray()
        ]);
    }
}
