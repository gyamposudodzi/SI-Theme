# NerdyWithMe WordPress Theme + Tools Plugin

`SI-Theme` is a custom WordPress project built around two connected deliverables:

- a bespoke editorial theme for the NerdyWithMe site
- a companion plugin that adds trading calculators, tools-page routing, and configurable ad slots

The project is designed for a content-heavy blog focused on trading, automation, AI, and technical education, with a branded homepage, curated article layouts, and a dedicated `/tools` experience.

## Project Components

### Theme

The root of this repository contains the WordPress theme. Core responsibilities include:

- homepage, archive, single-post, page, search, and category templates
- site branding, menus, search modal, mega menu, sidebar modules, and footer
- Customizer-driven site options for social links, curated content sources, profile cards, and reader bar styling
- shared rendering helpers for cards, post metadata, related posts, images, and article presentation
- performance-minded asset loading and WordPress frontend cleanup

Key files:

- `functions.php`
- `front-page.php`
- `single.php`
- `page.php`
- `header.php`
- `sidebar.php`
- `footer.php`
- `assets/`

### Plugin

The companion plugin lives in `plugins/nerdywithme-tools/`. It is responsible for:

- trading calculators rendered through shortcodes
- ad slot configuration and rendering
- tools hub routing for URLs such as `/tools/risk-calculator/`
- tool-specific metadata, body classes, and page context
- an admin settings screen for tool and ad management

Key files:

- `plugins/nerdywithme-tools/nerdywithme-tools.php`
- `plugins/nerdywithme-tools/includes/class-nerdywithme-tools-plugin.php`
- `plugins/nerdywithme-tools/includes/class-nerdywithme-tools-shortcodes.php`
- `plugins/nerdywithme-tools/includes/class-nerdywithme-tools-admin.php`
- `plugins/nerdywithme-tools/assets/`

## Main Features

- Custom editorial homepage with hero, curated sections, and social cards
- Multiple single-post layouts with reading progress and related-content blocks
- Search modal and drawer-style navigation experience
- Theme-level ad slot placements that gracefully integrate with the plugin
- Tool hub with calculators for:
  - risk
  - position size
  - pip or point value
  - profit target
  - compound growth
- Configurable tool labels, slugs, descriptions, summaries, and meta titles

## Requirements

- WordPress 6.0+
- PHP 7.4+

The theme header currently declares:

- Theme version: `1.0.6`
- Tested up to: `6.7`

The plugin bootstrap currently declares:

- Plugin version: `0.1.1`

## Local Structure

```text
SI-Theme/
|-- assets/
|-- dist/
|-- plugins/
|   `-- nerdywithme-tools/
|-- 404.php
|-- archive.php
|-- footer.php
|-- front-page.php
|-- functions.php
|-- header.php
|-- home.php
|-- page.php
|-- search.php
|-- sidebar.php
|-- single.php
`-- style.css
```

## Installation

### Option 1: Install from this repository during development

1. Copy the repository root theme into `wp-content/themes/`.
2. Copy `plugins/nerdywithme-tools/` into `wp-content/plugins/`.
3. Activate the `NerdyWithMe` theme in WordPress.
4. Activate the `NerdyWithMe Tools` plugin.
5. Create or assign a WordPress page with the slug `tools` if you want the tools hub live.
6. Visit `Settings > Permalinks` and save once to ensure rewrite rules are refreshed.

### Option 2: Install from packaged releases

Use the zip files in `dist/` for theme and plugin installation through the WordPress admin.

## Configuration Notes

- Theme options are primarily managed through the WordPress Customizer.
- Plugin options are managed through the `NWM Tools` admin menu.
- The theme checks for plugin helper functions before rendering plugin-driven ad slots, so the frontend does not fatally fail if the plugin is inactive.
- The tools experience depends on the plugin and on a `tools` page using content that includes the tools hub shortcode.

## Shortcodes

The plugin currently exposes:

- `[nwm_tools_hub]`
- `[nwm_risk_calculator]`
- `[nwm_ad_slot slot="sidebar"]`

## Release Artifacts

The `dist/` directory contains:

- packaged theme zip files
- packaged plugin zip files
- staging folders from previous release builds
- deployment and performance handoff notes

See `dist/README.md` for release-oriented details.

## Development Notes

- `dist/nerdywithme/` appears to be a release copy of the theme rather than the active source of truth
- root theme files are the primary implementation files
- plugin readme and metadata should be kept aligned with shipped functionality and versions

## Known Areas To Watch

- release documentation and package metadata can drift out of sync with source versions
- some repo folders appear to be historical build artifacts and should be treated carefully during release work
- the tools experience spans both theme and plugin, so changes often need verification in both places
