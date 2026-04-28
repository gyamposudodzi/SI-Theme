# NerdyWithMe Release Packages

This directory contains packaged release artifacts, staging folders, and deployment notes for the `NerdyWithMe` WordPress theme and the `NerdyWithMe Tools` companion plugin.

## Current Release Files

Latest package files currently present in this folder:

- Theme: `nerdywithme-theme-1.0.9.zip`
- Theme flat package: `nerdywithme-theme-1.0.9-flat.zip`
- Theme dev package: `nerdywithme-theme-1.0.9-dev-2026-04-28.zip`
- Plugin: `nerdywithme-tools-0.1.2.zip`
- Plugin dev package: `nerdywithme-tools-0.1.2-dev-2026-04-27.zip`
- Plugin alias: `nerdywithme-tools-latest.zip`

Legacy release files are also kept here for previous iterations.

## Install In WordPress

1. In WordPress admin, go to `Appearance > Themes > Add New > Upload Theme`.
2. Upload `nerdywithme-theme-1.0.9.zip` and activate it.
3. Go to `Plugins > Add New > Upload Plugin`.
4. Upload `nerdywithme-tools-0.1.2.zip` and activate it.
5. Save permalinks once under `Settings > Permalinks` if the tools routes do not appear immediately.

## What Is In This Folder

- `nerdywithme/`
  Release-ready copy of the theme files.
- `_theme_stage_*`
  Historical staging folders used while assembling theme release packages.
- `_plugin_stage*`
  Historical staging folders used while assembling plugin release packages.
- `HTACCESS-PERFORMANCE-SNIPPET.txt`
  Optional Apache or LiteSpeed performance snippet for production use.
- `PERFORMANCE-CHECKLIST.md`
  Release validation checklist for speed and frontend performance.

## Release Notes

- The theme is intended to work with the tools plugin for calculator pages and ad slot rendering.
- The plugin includes admin controls for ad slots, calculator visibility, tool labels, tool slugs, and related metadata.
- The source-of-truth development files live at the repository root and under `plugins/nerdywithme-tools/`. Files inside `dist/` should be treated as release artifacts unless intentionally updating a packaged copy.

## Performance Handoff

### Code Work Already Finished

- Conditional script loading is in place for search, sliders, single-post features, and tools.
- Theme image sizes were aligned with intended layout usage.
- Non-hero imagery is lazy loaded where appropriate.
- Font requests were reduced and fallback stacks were cleaned up.
- Layout-shift protections were added for sliders and ad slots.

### What To Do On The Live Site

1. Enable page caching through your host or preferred caching plugin.
2. Compress images and serve `WebP` or equivalent optimized formats where possible.
3. Enable object cache or server-level caching if your host supports it.
4. Turn on CDN delivery if available.
5. Keep only the plugins you truly need active.
6. If the host uses Apache or LiteSpeed, review and apply `HTACCESS-PERFORMANCE-SNIPPET.txt` where appropriate.

### Pages To Test In PageSpeed Or Lighthouse

- Homepage
- Single post page
- Tools page
- Category page or search results page

### Metrics To Record

- Performance score
- LCP
- INP
- CLS
- TBT
- Speed Index

### Recommended Testing Order

1. Test mobile first.
2. Fix the biggest real issue reported on the homepage.
3. Re-test the homepage.
4. Repeat for single post, tools, and category or search pages.
5. Compare mobile and desktop separately instead of mixing results.

### Important Reminder

- Run the final speed pass only after real content, real images, and real ad creatives are in place.
- If a score drops after launch, check image weight, ad payloads, and third-party plugins before changing theme code.
