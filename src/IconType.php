<?php

declare(strict_types=1);

namespace Pavloniym\ActionButtons;

/**
 * Heroicons v2 variants supported by Nova's <Icon> component.
 */
enum IconType: string
{
    case Outline = 'outline';
    case Solid = 'solid';
    case Mini = 'mini';
    case Micro = 'micro';
}
