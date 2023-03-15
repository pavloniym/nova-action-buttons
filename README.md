# Nova Action Buttons

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pavloniym/nova-action-buttons?style=flat-square)](https://packagist.org/packages/pavloniym/nova-action-buttons)
![Licence](https://img.shields.io/github/license/pavloniym/nova-action-buttons?style=flat-square)
[![Total Downloads](https://poser.pugx.org/pavloniym/nova-action-buttons/downloads?format=flat-square)](https://packagist.org/packages/pavloniym/nova-action-buttons)

This [Laravel Nova](https://nova.laravel.com) package allows you to execute an action directly on your resource table
view.


## Requirements

- `php: >=8.0`
- `laravel/nova: ^4.1`

## Installation

Install the package in a Laravel Nova project via Composer:

```bash
composer require pavloniym/nova-action-buttons
```

## Usage

### Single button
![Nova Action Buttons](https://raw.githubusercontent.com/pavloniym/nova-action-buttons/main/.github/assets/screenshot1.png)  
You can add single button to execute action from index row

```php
use Pavloniym\ActionButtons\ActionButton;

public function fields(Request $request)
{
    return [
      
        // ... Nova default fields
      
        ActionButton::make('') // Name in resource table column
            ->icon('<svg></svg>') // Svg icon (optional)
            ->title('Refresh') // Title (optional)
            ->styles([]) // Custom css styles (optional)
            ->classes([]) // Custom css classes (optional)
            ->action(new RefreshAction, $this->resource->id) // Provide action instance and resource id
            ->asToolbarButton(), // Display as row toolbar button (optional)
      
        // ... Nova default fields
    ];
}
```

### Collection of buttons
![Nova Action Buttons](https://raw.githubusercontent.com/pavloniym/nova-action-buttons/main/.github/assets/screenshot2.png)  
You can add collection of buttons to index row


```php
use Pavloniym\ActionButtons\ActionButton;

public function fields(Request $request)
{
    return [
      
        // ... Nova default fields
      
        ActionButtons::make()->collection([
            ActionButton::make('')->action(),
            ActionButton::make('')->action(),
            ActionButton::make('')->action(),
        ])
      
        // ... Nova default fields
    ];
}
```

## Caveats
* Currently, in order to use this field, you still have to declare the action in your resource `actions()` method.
* Tested only on `confirm-action-modal` action
* You should provide action instance in `action()` method of button.
* If you have action fields that are depends on resource instance -> you should inject resource in action constructor, because Nova doesn't provide `NovaRequest` instance to `fields` method on index row

```php
class RefreshAction extends Action 
{

    private Torrent $torrent

    /**
     * @param Torrent $torrent
     */
    public function __construct(Torrent $torrent)
    {
        $this->torrent = $torrent;
    }


    
    /**
     * Get the fields available on the action.
     *
     * @param NovaRequest $request
     * @return array|null
     */
    public function fields(NovaRequest $request): ?array
    {
   
        // $request is empty if action is called from index row (or inline)
        // so use instance injected to action constructor
        $torrent = (fn(): ?Torrent => $request?->selectedResources()?->first())();
        $torrent = $torrent ?? $this->torrent;

        if ($torrent) {
            return [
                File::make('File')->creationRules(['required'])
            ];
        }

        return null;
    }

}

```

> This field is available on index and detail (Thanks to [@CosminBd](https://github.com/CosminBd)) views

---
To hide the action either on Index or Detail, you can add the methods in the action declaration as per:
```php
ActionButton::make('My action')
    ->action((new RefreshAction)->onlyOnDetail(), $this->resource?->id)
    ->icon('')
    ->asToolbarButton(),
```
This is available for both action buttons and action button groups, and it works in individual actions which are part of the action group.

---

To run actions without confirmation, you can add the `$withoutConfirmation = true` property to the Laravel Nova action or provide it as a method when you declare the action button
```php
ActionButton::make('My action')
    ->action((new RefreshAction)->withoutConfirmation(), $this->resource?->id)
    ->icon('')
    ->asToolbarButton(),
```


## License

This project is open-sourced software licensed under the [MIT license](LICENSE.md).
