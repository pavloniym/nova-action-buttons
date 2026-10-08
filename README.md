# Nova Action Buttons

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pavloniym/nova-action-buttons?style=flat-square)](https://packagist.org/packages/pavloniym/nova-action-buttons)
![Licence](https://img.shields.io/github/license/pavloniym/nova-action-buttons?style=flat-square)
[![Total Downloads](https://poser.pugx.org/pavloniym/nova-action-buttons/downloads?format=flat-square)](https://packagist.org/packages/pavloniym/nova-action-buttons)

This [Laravel Nova](https://nova.laravel.com) package lets you run a resource action from a button
right in the resource table row or on the detail page.

## Requirements

| Package version | Laravel Nova | PHP    |
|-----------------|--------------|--------|
| `^2.0`          | `^5.0`       | `^8.1` |
| `^1.1`          | `^4.1`       | `^8.0` |

Still on Nova 4? Stay on `pavloniym/nova-action-buttons:^1.1`.

## Installation

Install the package in a Laravel Nova project via Composer:

```bash
composer require pavloniym/nova-action-buttons
```

## Usage

### Single button
![Nova Action Buttons](https://raw.githubusercontent.com/pavloniym/nova-action-buttons/main/.github/assets/screenshot1.png)

```php
use Pavloniym\ActionButtons\ActionButton;

public function fields(NovaRequest $request): array
{
    return [
        // ... Nova default fields

        ActionButton::make('') // Column name in the resource table
            ->action(new RefreshAction, $this->resource->id) // Action instance and resource id (required)
            ->text('Refresh') // Button text (optional)
            ->icon('arrow-path') // Heroicons v2 name (optional), see below
            ->tooltip('Refresh the torrent') // Tooltip (optional), defaults to the action name
            ->asToolbarButton(), // Look like Nova's row icons (optional)

        // ... Nova default fields
    ];
}

public function actions(NovaRequest $request): array
{
    return [
        new RefreshAction, // The action must be registered on the resource too
    ];
}
```

### Collection of buttons
![Nova Action Buttons](https://raw.githubusercontent.com/pavloniym/nova-action-buttons/main/.github/assets/screenshot2.png)

```php
use Pavloniym\ActionButtons\ActionButton;
use Pavloniym\ActionButtons\ActionButtons;

ActionButtons::make('Actions')->collection([
    ActionButton::make('')->text('Publish')->action(new PublishAction, $this->resource->id),
    ActionButton::make('')->icon('trash')->asToolbarButton()->action(new ArchiveAction, $this->resource->id),
]),
```

Buttons wrap onto the next line when the column is narrow. The collection follows the field's
`textAlign()` on the index (`center` by default).

Both `ActionButton` and `ActionButtons` work on the index and detail views
(thanks to [@CosminBd](https://github.com/CosminBd)) and are hidden on create / update forms.

### Button options

| Method                        | Description                                                                                           |
|-------------------------------|-------------------------------------------------------------------------------------------------------|
| `action(Action $action, $id)` | Action to run and the resource id it runs on. **Required.**                                           |
| `text(string $text)`          | Button text.                                                                                          |
| `icon(string $name, $type)`   | [Heroicons v2](https://heroicons.com) icon. `$type`: `outline` (default), `solid`, `mini`, `micro` or `IconType::*`. |
| `iconHtml(string $html)`      | Raw HTML / inline SVG icon.                                                                           |
| `iconUrl(string $url)`        | Image icon.                                                                                           |
| `tooltip(?string $text)`      | Show a tooltip on hover; without text it shows the action name.                                       |
| `asToolbarButton()`           | Look like Nova's view / edit / delete icons in a table row. **Style only**: the button stays in its column. |
| `classes(array $classes)`     | CSS classes that **replace** the default look of the button.                                          |
| `styles(array $styles)`       | Inline styles merged over the default look, e.g. `['min-width' => '8rem']`.                          |

#### Icons

`icon()` uses Nova's own `<Icon>` component, so it takes [Heroicons v2](https://heroicons.com) names:
`icon('bolt')`, `icon('arrow-down-tray', 'mini')`, `icon('check', IconType::Solid)`.
Heroicons v1 names from 1.x (e.g. `lightning-bolt`, `refresh`, `download`) are mapped to v2 by Nova itself,
and SVG markup passed to `icon()` is rendered as `iconHtml()`.

#### Custom classes

Without `classes()` a button looks like Nova's primary button (or like Nova's row icons with `asToolbarButton()`).
`classes()` replaces that look, keeping only the layout (`inline-flex`, centered content, focus ring).
Only classes present in Nova's own stylesheet (or in your theme) will have an effect.

```php
ActionButton::make('')
    ->text('Danger zone')
    ->classes(['text-red-500', 'font-bold', 'px-2'])
    ->action(new WipeAction, $this->resource->id),
```

### Action responses

Buttons handle every Nova 5 action response the same way as Nova's own action menus:
`Action::message()`, `Action::danger()`, `Action::deleted()`, `Action::redirect()`, `Action::openInNewTab()`,
`Action::visit()`, `Action::downloadURL()`, downloads streamed from `handle()` (e.g. `ExportAsCsv`),
`Action::modal()` and `ActionResponse::emit()`.

After an action runs, the index reloads its rows; the detail page reloads the resource
(or goes back to the index after `Action::deleted()`).

### Visibility and confirmation

Hide an action on the index or on the detail view with Nova's own methods, both for single buttons
and for buttons in a collection:

```php
ActionButton::make('My action')
    ->action((new RefreshAction)->onlyOnDetail(), $this->resource?->id),
```

Run an action without the confirmation modal:

```php
ActionButton::make('My action')
    ->action((new RefreshAction)->withoutConfirmation(), $this->resource?->id),
```

To show the action only as a button and keep it out of Nova's own action menus, hide it in `actions()`.
Nova still finds it there when the button runs it:

```php
public function actions(NovaRequest $request): array
{
    return [
        tap(new RefreshAction, function (RefreshAction $action) {
            $action->showOnIndex = false;  // index toolbar menu
            $action->showOnDetail = false; // detail menu
            $action->showInline = false;   // table row menu
        }),
    ];
}
```

## Caveats

* The action must be registered in the resource's `actions()` method: Nova looks it up there when it runs.
* You must pass the action instance and the resource id to `action()`.
* Pivot actions are not supported.
* If the action's fields depend on the resource, inject the resource into the action constructor:
  Nova doesn't provide the selected resource to `fields()` when the modal is opened from a table row.

```php
class RefreshAction extends Action
{
    public function __construct(private ?Torrent $torrent = null)
    {
    }

    public function fields(NovaRequest $request): array
    {
        $torrent = $request->selectedResources()?->first() ?? $this->torrent;

        return $torrent ? [File::make('File')->rules('required')] : [];
    }
}
```

## Upgrading from 1.x to 2.0

2.0 supports Laravel Nova 5 only. Nova 4 projects should stay on `^1.1`.

* **Requirements**: `laravel/nova: ^5.0`, `php: ^8.1`.
* **Icons**: `icon()` takes Heroicons v2 names (v1 names keep working through Nova's mapping).
  It accepts an optional variant: `icon('bolt', 'solid')`.
* **`classes()` replaces the default look** instead of being added to it. To keep the default button and tweak it,
  use `styles()`.
* **CSS**: the package no longer ships Tailwind utilities (they overrode Nova's responsive classes and caused a
  duplicated actions dropdown). Buttons use `nab-button`, `nab-button--solid`, `nab-button--toolbar` and
  `nab-buttons` classes; custom CSS targeting the old Tailwind classes needs updating.
* **Return types**: `ActionButton` / `ActionButtons` methods return `static` (was `self`); subclasses overriding them
  must update their signatures.
* **Tooltip** is rendered with Nova's own tooltip.
* **Action responses**: `redirect`, `openInNewTab`, `download`, `visit`, `modal` and `emit` responses now work
  with Nova 5's response format.
* **Fixed**: stale field values and `e.fill is not defined` when an action is run again without reloading the page,
  and a button stuck in the loading state after that error.

See [CHANGELOG.md](CHANGELOG.md) for the full list.

## Development

Building the assets needs `laravel/nova` and `laravel/nova-devtool` in the package's `vendor`:

```bash
composer install            # with access to the Laravel Nova repository
npm install
npm run production
vendor/bin/phpunit
```

## License

This project is open-sourced software licensed under the [MIT license](LICENSE.md).
