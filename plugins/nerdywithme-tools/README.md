# NerdyWithMe Tools

`NerdyWithMe Tools` is the companion plugin for the `NerdyWithMe` WordPress theme. It provides the interactive tools layer for the site, including calculator shortcodes, tool-page routing, active-tool metadata, and configurable ad slot rendering used by the theme.

## Current Scope

The plugin currently includes:

- a WordPress admin screen under `NWM Tools`
- configurable ad slots for theme placements
- a tools hub shortcode for a multi-tool page experience
- multiple built-in trading calculators
- tool-specific URLs under the `/tools/` path
- helper functions the theme can call safely when the plugin is active

## Calculators

The current calculator set includes:

- Risk Calculator
- Position Size Calculator
- Pip / Point Value Calculator
- Profit Target Calculator
- Compound Growth Calculator

## Ad Slot Support

The admin configuration currently supports these slot keys:

- `homepage_after_hero`
- `archive_header`
- `sidebar`
- `single_inline`
- `footer`

Slots can be configured with different display modes and presentation settings, including:

- raw markup
- managed promo content
- managed image ads
- sticky behavior for supported placements
- responsive width and alignment settings
- mobile visibility controls

## Shortcodes

The plugin currently exposes:

- `[nwm_tools_hub]`
- `[nwm_risk_calculator]`
- `[nwm_ad_slot slot="sidebar"]`

## Public Integration Helpers

The theme integration currently relies on:

- `nerdywithme_tools_render_ad_slot($slot)`
- `nerdywithme_tools_get_active_tool_data()`

These helpers allow the theme to render plugin-driven content without hard-coupling template execution to the plugin internals.

## Routing Behavior

The plugin registers a public query var and rewrite rule so tool views can be accessed with URLs like:

- `/tools/risk-calculator/`
- `/tools/position-size/`
- `/tools/profit-target/`

The active tool is resolved from the `nwm_tool` query var and used to:

- switch the visible tool pane
- add tool-specific body classes
- set tool-aware page titles
- output tool-aware meta description and canonical tags

## Admin Features

The `NWM Tools` admin page currently supports:

- enabling or disabling calculators
- controlling tool order
- editing tool labels and slugs
- editing tool descriptions, summaries, and meta titles
- managing ad content and slot presentation settings

## File Overview

- `nerdywithme-tools.php`
  Plugin bootstrap and singleton entry point.
- `includes/class-nerdywithme-tools-plugin.php`
  Routing, frontend assets, metadata, and body class integration.
- `includes/class-nerdywithme-tools-shortcodes.php`
  Shortcodes, tool registry, calculator rendering, and public helper functions.
- `includes/class-nerdywithme-tools-admin.php`
  Admin menu, settings registration, sanitization, and configuration UI.
- `assets/`
  Frontend and admin CSS/JS assets.

## Notes

- The plugin is intended to work alongside the `NerdyWithMe` theme, but its shortcodes and admin tooling are packaged independently.
- If tool routes do not work immediately after activation, save permalinks once in WordPress under `Settings > Permalinks`.
- Keep this README aligned with the shipped calculator set and slot behavior as the plugin evolves.
