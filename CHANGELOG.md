# Changelog

All notable changes to this project are documented in this file.
The project follows [Semantic Versioning](https://semver.org).

## [2.0.1] - 2026-10-09

No code changes: same files as 2.0.0, released from the cleaned-up `main` history.

## [2.0.0] - 2026-10-09

Laravel Nova 5 support. Nova 4 projects should stay on `^1.1`.
See the [upgrade guide](README.md#upgrading-from-1x-to-20).

### Breaking

- Requires `laravel/nova: ^5.0` and `php: ^8.1`.
- `classes()` replaces the default look of the button instead of being added to it (#17).
- The bundled CSS no longer contains Tailwind utilities; buttons use their own `nab-*` classes.
- `icon()` renders Heroicons v2 names through Nova's `<Icon>` component.
- `ActionButton` / `ActionButtons` methods return `static` instead of `self`.

### Added

- `icon($name, $type)`: optional Heroicons variant `outline` (default), `solid`, `mini`, `micro` (`IconType` enum).
- Support for every Nova 5 action response: `redirect` / `openInNewTab`, `download`, `visit`, `modal` and `emit`
  (`event`), besides `message`, `danger`, `deleted` and streamed downloads.
- Buttons in a collection wrap onto the next line when the column is narrow.
- PHPUnit tests and a GitHub Actions workflow.

### Fixed

- `icon()` rendered an empty button on Nova 5 (#19).
- `Action::redirect()` led to `/resources/[object Object]`; `openInNewTab()` and `download` responses did nothing.
- SVG markup passed to `icon()` (the documented 1.1.1 usage) is rendered again (#16).
- Package CSS overrode Nova's responsive classes and caused a duplicated actions dropdown (#15).
  Thanks to @andypooletrioteca, @mcatalan-trioteca (#20, #24) and @k8n (#21).
- Running an action again without reloading the page sent stale field values (#18), failed with
  `e.fill is not defined` (#10) and left the button loading forever (#13).
- `asToolbarButton()` now looks like Nova's row icons instead of plain text (#22).
- Buttons in a collection on the detail page now reload the resource after the action.
- A single `ActionButton` respects the action's `onlyOnIndex()` / `onlyOnDetail()` visibility, like buttons in a collection.
- Input typed into the action modal is kept when the index reloads while the modal is open.
- Boolean fields of actions without confirmation send their default value correctly.
- `tooltip()` uses an explicit nullable type (PHP 8.4 deprecation).
- Tooltips are rendered with Nova's own tooltip.

### Changed

- Assets are built with `laravel/nova-devtool`; Nova's code is no longer bundled into `dist`
  (thanks to @CosminBd for the Nova 5 groundwork in #23).

## [1.1.5] - 2024-11-01

Last release for Laravel Nova 4.
