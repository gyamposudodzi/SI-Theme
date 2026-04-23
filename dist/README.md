# NerdyWithMe Release Packages

## Contents
- Theme zip: `nerdywithme-theme-1.0.5.zip`
- Plugin zip: `nerdywithme-tools-0.1.0.zip`

## Install (WordPress)
1. Go to `Appearance > Themes > Add New > Upload Theme`
2. Upload `nerdywithme-theme-1.0.5.zip` and activate it.
3. Go to `Plugins > Add New > Upload Plugin`
4. Upload `nerdywithme-tools-0.1.0.zip` and activate it.

## Notes
- The theme is designed to work with the tools plugin for calculators and ad slots.
- The tools plugin includes admin controls for ad slots and calculators.

## Performance Handoff

### Code Work Already Finished
- Conditional script loading is in place for search, sliders, single-post features, and tools.
- Theme image sizes were audited and aligned to layout usage.
- Non-hero imagery is lazy loaded where appropriate.
- Font requests were reduced and fallback stacks were cleaned up.
- Layout-shift protections were added for sliders and ad slots.

### What To Do On The Live Site
1. Enable page caching through your host or caching plugin.
2. Enable image compression and serve final images as `WebP` when possible.
3. If your host offers it, enable object cache or server-level caching.
4. Turn on CDN delivery if available.
5. Keep only the plugins you truly need active.
6. If your host uses Apache/LiteSpeed, apply the cache snippet in `HTACCESS-PERFORMANCE-SNIPPET.txt`.

### Pages To Test In PageSpeed / Lighthouse
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
4. Repeat for single post, tools, and category/search.
5. Compare mobile and desktop separately instead of mixing results.

### Important Reminder
- Run the final speed pass only after real content, real images, and real ad creatives are in place.
- If a score drops after launch, check image weight, ads, and third-party plugins before changing theme code.
