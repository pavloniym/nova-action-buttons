<?php

declare(strict_types=1);

namespace Pavloniym\ActionButtons\Support;

use InvalidArgumentException;
use Pavloniym\ActionButtons\IconType;

/**
 * Builds the field meta for ActionButton::icon().
 */
final class IconMeta
{
    /**
     * @return array{icon: string, iconType: string}|array{iconHtml: string}
     */
    public static function make(string $icon, IconType|string $type = IconType::Outline): array
    {
        $icon = trim($icon);

        // Up to v1.1.1 icon() took raw SVG markup; keep accepting it.
        if (str_starts_with($icon, '<')) {
            return ['iconHtml' => $icon];
        }

        if ($icon === '') {
            throw new InvalidArgumentException('Icon name must not be empty.');
        }

        $iconType = $type instanceof IconType ? $type : IconType::tryFrom($type);

        if ($iconType === null) {
            throw new InvalidArgumentException(sprintf(
                'Invalid icon type "%s", expected one of: %s.',
                $type,
                implode(', ', array_column(IconType::cases(), 'value')),
            ));
        }

        return ['icon' => $icon, 'iconType' => $iconType->value];
    }
}
