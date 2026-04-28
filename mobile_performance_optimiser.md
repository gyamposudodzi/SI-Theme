# Mobile Performance Optimiser

This file tracks the mobile-first performance backlog for the NerdyWithMe theme and plugin experience.

Desktop performance is already strong, but mobile remains meaningfully lower. The items below are ordered by likely impact on mobile PageSpeed and real-world responsiveness.

## Priority 1

### 1. Reduce heavy paint effects on mobile

Current hotspots:

- `style.css`
  - sticky header blur
  - drawer blur/compositing
  - search modal blur
  - share modal blur
- `assets/css/tools-page.css`
  - tools page panel blur and heavy shadows

Recommended fixes:

- disable `backdrop-filter` below `820px`
- reduce large shadows on overlays and premium cards on smaller screens
- keep the layout, but flatten the visual treatment on phones

Why this matters:

- mobile GPUs pay more for blur and layered compositing than desktop

### 2. Reduce above-the-fold homepage density on mobile

Current hotspots:

- `front-page.php`
  - hero
  - compact side cards
  - early homepage sections

Recommended fixes:

- reduce the number of items shown immediately on mobile
- consider hiding one secondary homepage section behind a later interaction
- keep the hero, but make the first viewport lighter

Why this matters:

- mobile has to stack and render many rich blocks in one column

### 3. Further tighten hero image delivery

Current hotspots:

- `functions.php`
  - homepage hero preload
- `front-page.php`
  - homepage hero image
- `single.php`
  - single-post hero image

Recommended fixes:

- consider a slightly smaller mobile hero crop/target than the current `nwm-hero-mobile`
- tighten mobile `sizes` hints further if possible
- regenerate thumbnails if another smaller hero size is introduced

Why this matters:

- mobile LCP is often dominated by the hero image

## Priority 2

### 4. Reduce tools-page visual cost on mobile

Current hotspots:

- `assets/css/tools-page.css`
  - premium ad/demo card shadows
  - blurred glass-like panels

Recommended fixes:

- remove blur effects on mobile for tools-page ad/demo areas
- tone down shadows below `820px`

Why this matters:

- tools pages likely cost more on mobile than desktop because of layered effects

### 5. Reduce single-post JS work where possible

Current hotspots:

- `functions.php`
  - reading bar script
  - TOC script
  - single cleanup script

Recommended fixes:

- only load TOC logic if a TOC exists
- only load share-modal behavior if share UI is rendered
- confirm reading-bar logic is skipped when not needed

Why this matters:

- mobile CPU is more sensitive to small script costs

### 6. Add reduced-motion / reduced-effects handling

Current hotspots:

- `style.css`
  - transitions on menus, overlays, cards, and reading bar
- `plugins/nerdywithme-tools/assets/css/nerdywithme-tools.css`
  - tool card and tab transitions

Recommended fixes:

- add a global `prefers-reduced-motion` block
- use it to reduce transitions and optionally heavier visual polish on mobile

Why this matters:

- improves accessibility and reduces some paint/composite churn

## Priority 3

### 7. Revisit mobile search modal content density

Current hotspots:

- `header.php`
  - suggested categories
  - recommended posts
- `style.css`
  - search modal card styling

Recommended fixes:

- reduce recommended post count on smaller screens
- or defer the suggested block until after modal open

Why this matters:

- helps mobile interaction cost and modal render load

### 8. Revisit font loading strategy for mobile

Current hotspots:

- `functions.php`
  - Google Fonts request for `Fredoka` and `Outfit`

Recommended fixes:

- keep the brand font only where needed
- reduce loaded `Outfit` weights if possible
- trim any unused font weights

Why this matters:

- mobile suffers more from font transfer and render delay than desktop

## Recommended First Batch

When ready to implement, start with this batch:

1. remove `backdrop-filter` and soften heavy shadows on mobile overlays/cards
2. trim mobile above-the-fold homepage density
3. conditionally load fewer single-post scripts
4. add reduced-motion / reduced-effects rules

This batch is the most likely to move the mobile score meaningfully.
