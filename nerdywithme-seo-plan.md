# NerdyWithMe SEO — Full Competitor Plan

**Status:** Draft for review (not approved for build)  
**Plugin slug:** `nerdywithme-seo`  
**Goal:** A fully built WordPress SEO suite that replaces Yoast / AIOSEO / Rank Math for NerdyWithMe — and is strong enough to stand as a real competitor, not a thin wrapper.  
**Last updated:** 2026-09-13  

> Review loop: edit this file freely. Mark sections `Approved`, `Change`, or `Defer`. Do not start coding until the **Go / No-Go** section is signed off.

---

## 1. Product thesis

### Why build our own
- Existing big SEO plugins are generic. They do not deeply understand NerdyWithMe’s trading tools, calculator pages, content style, or brand workflow.
- We already ship theme + tools as a controlled stack. Owning SEO keeps meta, schema, sitemaps, and redirects consistent with that stack.
- Competitors are not “magic.” Most ranking impact comes from correct titles, canonicals, schema, crawl control, redirects, and internal linking — all implementable in WordPress.
- Building in-house means no upsell walls, no conflicting free-tier limits, and features shaped for *our* publishing workflow.

### What “fully built competitor” means here
Parity with the **practical product surface** of Yoast Premium + AIOSEO Pro + Rank Math Pro — not cloning every marketing checkbox forever.

**Must win on:**
1. Correct, clean technical SEO output (no duplicate tags, no junk schema)
2. Fast editor UX for posts/pages/tools
3. Strong schema + sitemaps + redirects
4. Useful content analysis (actionable, not noisy)
5. AI assists that save time without locking core SEO behind credits
6. Deep integration with NerdyWithMe theme + tools plugin

**Will deliberately not chase (v1–v2):**
- Multi-site agency white-label SaaS dashboards
- Paid keyword-rank tracking SaaS as a primary product
- Local multi-location map suites (unless NWM needs them)
- WooCommerce / Easy Digital Downloads deep store SEO (unless shop launches)
- “AI brand mention monitoring across ChatGPT/Gemini” style cloud products

Those can be later modules if the business needs them.

---

## 2. Competitive baseline (what we must cover)

Research snapshot (2025–2026 market): Yoast, AIOSEO, and Rank Math converge on the same core. Rank Math often leads free-tier breadth; Yoast leads beginner guidance; AIOSEO leads packaged business/local/Woo workflows. Newer arms race: AI metadata, `llms.txt`, AI crawler controls, IndexNow, internal-link AI.

### Feature parity matrix

| Capability | Yoast | AIOSEO | Rank Math | NWM SEO target |
|---|---|---|---|---|
| Title / meta description templates + per-URL override | Yes | Yes | Yes | **Required** |
| Social previews (OG + Twitter/X) | Yes | Yes | Yes | **Required** |
| Canonicals + robots meta | Yes | Yes | Yes | **Required** |
| XML sitemaps (posts, pages, taxonomies, images) | Yes | Yes | Yes | **Required** |
| News / video / RSS sitemap variants | Paid/tiers | Paid/tiers | Strong | **Phase 2** |
| Schema (Article, FAQ, HowTo, Org, WebSite, Breadcrumb) | Basic→Pro | Strong paid | Very strong free | **Required core; expand Phase 2** |
| Breadcrumbs (UI + schema) | Yes | Yes | Yes | **Required** |
| Redirect manager + 404 monitor | Paid / paid | Paid | Free | **Required (include in core)** |
| Content / readability analysis + focus keyphrases | Yes | Yes | Yes (multi KW free) | **Required (multi KW)** |
| Internal link suggestions | Paid | Paid | Strong | **Phase 2** |
| Image SEO (alt, title, filename helpers) | Limited | Paid | Yes | **Phase 1 light / Phase 2 auto** |
| robots.txt + .htaccess editors | Yes | Yes | Yes | **Required** |
| Search Console connect / verification | Partial | Yes | Yes | **Phase 1 verify; Phase 2 stats** |
| Import from Yoast / Rank Math / AIOSEO | Yes | Yes | Yes | **Required before public replace** |
| AI title/description helpers | Paid credits | Paid credits | Paid units | **Phase 2 (optional provider)** |
| `llms.txt` / AI crawler controls / IndexNow | Emerging | Emerging | Emerging | **Phase 2** |
| Local SEO / Woo | Add-ons | Strong | Strong | **Defer unless needed** |
| Tool/calculator SEO awareness | No | No | No | **NWM differentiator — Required** |

### Differentiation (why ours can be better *for us*)
1. **First-class Tools SEO** — calculator URLs, hub pages, tool schema (`WebApplication` / `SoftwareApplication`), tool sitemaps, conflict-free merge with `nerdywithme-tools`.
2. **Trading/education content presets** — schema + title templates tuned for market explainers, how-tos, glossary, and tool landing pages.
3. **Lean frontend** — no bloat scripts on public pages; analysis stays in admin/editor.
4. **One-stack branding** — admin UI matches NWM Tools; dark-mode aware; same packaging pipeline.
5. **No hostage features** — redirects, multi-keyphrase, and core schema ship in the product we use (not behind a paywall we invented for ourselves).

---

## 3. Current NWM starting point

### Already exists (in `nerdywithme-tools`)
- Per-tool meta titles (admin SEO tab)
- On active tool URLs: meta description, canonical, basic OG (`title`, `description`, `url`)
- Theme has `title-tag` support and a clean `wp_head()` attach point
- Theme already strips some head noise (generator, emoji, shortlink, etc.)

### Gaps (everything else)
- Sitewide titles/descriptions for posts, pages, archives, home, authors, taxonomies
- OG images / Twitter cards
- Robots / noindex controls
- JSON-LD schema graph
- Breadcrumbs
- Custom/advanced sitemaps
- Redirects + 404 log
- Editor SEO sidebar / metabox + content analysis
- Import/conflict handling with other SEO plugins

### Architecture decision (locked recommendation)
**Build a separate plugin `plugins/nerdywithme-seo/`.**  
Do **not** expand tools into a full SEO suite.

Reasons:
- Clear ownership of `<head>` output
- Install/activate independently
- Easier packaging and versioning
- Tools plugin stays focused on calculators

**Integration rule:** SEO plugin becomes the single owner of head tags. Tools plugin either:
- A) hands tool SEO fields to SEO plugin via filters/API, or  
- B) disables its own head output when SEO plugin is active  

Preferred: **B + shared field sync** (tools keep tool-specific fields; SEO plugin renders them).

---

## 4. Product naming & conventions

| Item | Value |
|---|---|
| Folder / slug | `nerdywithme-seo` |
| Text domain | `nerdywithme-seo` |
| Main file | `nerdywithme-seo.php` |
| Classes | `NerdyWithMe_Seo_*` |
| Constants | `NERDYWITHME_SEO_VERSION`, `_PATH`, `_URL`, `_FILE` |
| Option key | `nerdywithme_seo_settings` |
| Post meta prefix | `_nwm_seo_*` |
| CSS/JS prefix | `nwm-seo-` |
| Admin menu | **NWM SEO** (top-level, sibling to NWM Tools) |
| Capability | `manage_options` for settings; `edit_posts` for metabox |

Mirror the tools plugin pattern: bootstrap → singleton → `includes/` feature classes → `assets/` → Settings API + custom admin UI → public helpers with `function_exists` guards.

---

## 5. Proposed architecture

```text
plugins/nerdywithme-seo/
├── nerdywithme-seo.php                 # bootstrap, constants, activation
├── README.md
├── .codex-plugin/plugin.json
├── includes/
│   ├── class-nerdywithme-seo-plugin.php        # orchestrator / hooks
│   ├── class-nerdywithme-seo-admin.php         # settings UI
│   ├── class-nerdywithme-seo-metabox.php       # post editor panel
│   ├── class-nerdywithme-seo-titles.php        # title templates + document_title
│   ├── class-nerdywithme-seo-meta.php          # description, robots, canonical
│   ├── class-nerdywithme-seo-social.php        # OG + Twitter
│   ├── class-nerdywithme-seo-schema.php        # JSON-LD graph builder
│   ├── class-nerdywithme-seo-sitemaps.php      # sitemap provider / overrides
│   ├── class-nerdywithme-seo-breadcrumbs.php   # trail + schema + shortcode
│   ├── class-nerdywithme-seo-robots.php        # robots.txt / wp_robots
│   ├── class-nerdywithme-seo-redirects.php     # redirect engine
│   ├── class-nerdywithme-seo-404-monitor.php   # 404 logger
│   ├── class-nerdywithme-seo-analysis.php      # content / keyphrase checks
│   ├── class-nerdywithme-seo-image.php         # image SEO helpers
│   ├── class-nerdywithme-seo-import.php        # import from competitors
│   ├── class-nerdywithme-seo-conflict.php      # detect other SEO plugins
│   ├── class-nerdywithme-seo-tools-bridge.php  # integrate with NWM Tools
│   ├── class-nerdywithme-seo-ai.php            # optional AI providers (Phase 2)
│   └── class-nerdywithme-seo-rest.php          # REST for editor analysis
├── assets/
│   ├── css/nwm-seo-admin.css
│   ├── css/nwm-seo-metabox.css
│   ├── js/nwm-seo-admin.js
│   └── js/nwm-seo-metabox.js
└── views/                                       # admin partials
```

### Data model (high level)
- **Global settings** in `nerdywithme_seo_settings` (templates, toggles, social defaults, modules on/off)
- **Per-post meta** `_nwm_seo_title`, `_nwm_seo_description`, `_nwm_seo_focus_kw`, `_nwm_seo_robots`, `_nwm_seo_canonical`, `_nwm_seo_og_*`, `_nwm_seo_schema_*`, etc.
- **Redirects / 404s** in custom DB tables (`wp_nwm_seo_redirects`, `wp_nwm_seo_404`) for performance and querying
- **Analysis cache** optional transient/meta to avoid recomputing on every load

### Module system
Every major feature is a toggleable module (Rank Math-style). Keeps memory/CPU lean and lets us ship incomplete Phase 2 modules disabled by default.

---

## 6. Feature specification (phased)

### Phase 0 — Foundations (week 1)
**Outcome:** installable plugin skeleton that owns head output safely.

- Plugin bootstrap, activation/deactivation, uninstall options
- Conflict detector (Yoast / Rank Math / AIOSEO / SEOPress) with admin notice + “disable NWM SEO output” kill switch
- Settings shell with tabs
- Packaging hooks matching existing release rule (`nerdywithme-seo-X.Y.Z.zip`)
- Tools bridge: when active, suppress duplicate tool meta from tools plugin and read tool fields

**Exit criteria:** activate on local site with theme + tools; no duplicate titles/descriptions/canonicals.

---

### Phase 1 — Core SEO engine (weeks 2–5)  ← minimum “replace Yoast free”
**Outcome:** daily publishing without another SEO plugin.

#### 1.1 Titles & meta
- Global templates for: home, posts, pages, CPT, categories, tags, authors, search, 404, archives
- Variables: `%title%`, `%sitename%`, `%sep%`, `%excerpt%`, `%category%`, `%tag%`, `%author%`, `%date%`, `%page%`, `%currentyear%`, `%focuskw%`, tool-specific `%tool_name%`
- Per-post override metabox fields
- Separator + sitename controls
- Force rewrite of `document_title_parts`

#### 1.2 Descriptions, canonicals, robots
- Meta description templates + override
- Auto-fallback from excerpt / trimmed content
- Canonical URL override
- Robots: index/noindex, follow/nofollow, noarchive, nosnippet, max-snippet/image-preview/video-preview
- Defaults: noindex for search, 404; configurable for authors/dates/tags

#### 1.3 Social
- Open Graph: title, description, image, type, url, site_name
- Twitter/X cards: summary / summary_large_image
- Default social image in settings
- Per-post social overrides + live preview in metabox
- Facebook/Twitter verification meta if needed

#### 1.4 Schema graph (v1 types)
Required v1:
- `WebSite` + `SearchAction`
- `Organization` / `Person` (from settings + existing social profiles)
- `WebPage` / `Article` / `BlogPosting`
- `BreadcrumbList`
- `FAQPage` (from FAQ block or metabox repeater)
- `HowTo` (optional metabox / block detection)
- Tool pages: `WebApplication` or `SoftwareApplication` + `WebPage`

Rules:
- Single `@graph` JSON-LD in footer/head
- Validatable in Google Rich Results / Schema Markup Validator
- No duplicate Organization nodes

#### 1.5 Sitemaps
Decision to confirm in review:
- **Option A (recommended):** Enhance WordPress core sitemaps via providers/filters (lighter, fewer conflicts)
- **Option B:** Replace core with custom XML sitemap system (more control, more maintenance)

v1 needs:
- Include/exclude post types & taxonomies
- Exclude noindexed URLs
- Image entries where useful
- Priority/frequency only if we invent our own sitemap (core ignores them anyway — document this honestly)
- Ping/IndexNow later (Phase 2)

#### 1.6 Breadcrumbs
- PHP helper + shortcode `[nwm_seo_breadcrumbs]`
- Theme integration helper for optional header/content placement
- Matching `BreadcrumbList` schema
- Separators / home label settings

#### 1.7 Editor experience
- Metabox (classic) + sidebar panel pattern compatible with block editor
- Google snippet preview
- Social previews
- Focus keyphrases: **up to 5**
- Score breakdown with clear pass/fail checks (not vague traffic-light theater only)
- Checks v1:
  - keyphrase in title, description, slug, first paragraph, H2s, image alts
  - title/description length
  - outbound / inbound links presence
  - content length thresholds (configurable)
  - single H1 expectation
  - canonical/robots sanity

#### 1.8 robots.txt editor
- Virtual robots.txt via `robots_txt` filter
- Sitemap URL advertisement
- AI bot allow/block presets (stored, Phase 2 polish)

#### 1.9 Image SEO (light)
- Require/ warn missing alt in analysis
- Optional attachment title/alt bulk helper later

**Phase 1 exit criteria:** publish a post + tool page; validate head tags, schema, sitemap, breadcrumbs; deactivate Yoast/AIOSEO with no SEO regression for NWM needs.

---

### Phase 2 — Competitor depth (weeks 6–10)
**Outcome:** “I don’t miss Rank Math Pro / AIOSEO Pro for our site.”

#### 2.1 Redirect manager
- 301 / 302 / 307 / 410 / 451
- Exact + regex rules
- Import CSV + import from Redirection plugin
- Auto-suggest redirect when slug/permalink changes
- Hit counters
- Enable/disable rules

#### 2.2 404 monitor
- Log 404s (URL, referrer, hit count, last seen)
- Ignore patterns (bots, query noise)
- One-click create redirect
- Retention / cleanup settings

#### 2.3 Advanced schema
- Additional types as needed for content: `Course`, `DefinedTerm` (glossary), `QAPage`, `VideoObject`, `NewsArticle`
- Custom JSON-LD escape hatch for power users
- Per-CPT default schema mapping

#### 2.4 Internal linking assistant
- Suggest related posts by shared keyphrase/tags/embeddings-lite (taxonomy + content similarity first)
- Orphan content report
- Broken internal link scan (admin cron)

#### 2.5 Import / migration
- One-click import from:
  - Yoast SEO
  - Rank Math
  - AIOSEO
  - SEOPress (nice-to-have)
- Map titles, descriptions, focus KW, canonicals, robots, social, redirects where possible
- Post-import validation report

#### 2.6 Search Console / analytics bridge
- Site verification helpers
- Optional GSC OAuth for impressions/clicks on posts (nice-to-have; can defer if complexity high)
- Do **not** block Phase 2 completion on full rank-tracking SaaS

#### 2.7 AI Assist module (optional provider)
- Generate title / meta description drafts
- Suggest focus keyphrases
- Summarize content for OG description
- Provider abstraction: OpenAI-compatible API / user-supplied key
- Hard rule: **core SEO never depends on AI being configured**
- Store prompts tuned for NerdyWithMe voice

#### 2.8 AI-search readiness
- `llms.txt` generator (site summary + key URLs)
- IndexNow support
- AI crawler directives in robots settings
- Clear authorship/Organization entity consistency (E-E-A-T support, not a gimmick)

#### 2.9 News / video / HTML sitemap (as needed)
- Only build what NWM content actually uses
- HTML sitemap page shortcode for users/crawlers

**Phase 2 exit criteria:** redirects + 404 + import + advanced schema + internal link report working on staging; AI optional and documented.

---

### Phase 3 — Polish, performance, productization (weeks 11–14)
**Outcome:** production-hardened NWM SEO product.

- Performance audit: no frontend JS unless breadcrumbs/UI needs it
- Object caching friendly queries for redirects
- Bulk editor for titles/descriptions
- SEO Health dashboard (coverage: missing descriptions, noindex surprises, orphan pages, redirect loops)
- Setup wizard (first-run)
- Role permissions (editors vs admins)
- Full README + support docs
- Automated tests for title rendering, schema graph, redirect matching, import mapping
- Accessibility pass on admin UI
- Dark-mode parity with NWM Tools admin

---

### Phase 4 — Optional expansions (only if business needs)
- Local SEO module
- WooCommerce module
- Video/News SEO packs
- Multilingual (WPML/Polylang) compatibility pack
- White-label / multi-site agency mode
- Hosted rank tracking

---

## 7. Tools + theme integration plan

### With `nerdywithme-tools`
- Detect plugin via `function_exists('nerdywithme_tools')` / class checks
- When SEO active:
  - Tools stops printing description/canonical/OG
  - SEO reads tool meta titles / summaries / descriptions from tools settings
  - Tool URLs included in sitemap provider
  - Tool schema emitted on tool routes
- Shared admin cross-links: Tools ↔ SEO settings

### With theme
- Prefer filters/helpers over hard theme edits
- Optional small theme patch: breadcrumb mount point if desired
- Respect existing head cleanup; don’t reintroduce emoji/oEmbed noise
- Coordinate `wp_head` priorities with theme mode bootstrap / preloads

### Conflict policy
If Yoast / Rank Math / AIOSEO / SEOPress detected:
1. Show blocking admin notice
2. Offer “run anyway (dangerous)” only in debug
3. Default: NWM SEO output disabled until conflict cleared  
   *(Reviewer choice: or auto-prefer NWM and instruct to deactivate others.)*

---

## 8. UX principles (admin + editor)

1. **One job per screen** — Titles, Schema, Redirects, etc. as clear modules/tabs
2. **Preview-first editor** — snippet + social previews above long forms
3. **Explain every check** — why it matters + how to fix
4. **No frontend bloat** — public site gets tags/schema only
5. **Brand-consistent** — match NWM Tools admin styling / dark mode
6. **Honest scoring** — scores guide editing; never claim “rank #1 if green”

---

## 9. Non-functional requirements

| Area | Requirement |
|---|---|
| PHP | Match theme/tools minimum (document exact version at build start) |
| WP | Support current WP major + previous |
| Performance | <50ms typical extra PHP on front for simple posts; redirects via efficient lookup |
| Security | Nonces, caps, sanitized meta, escaped output, prepared SQL |
| Privacy | 404 logs may store IPs — make IP logging optional/off by default |
| i18n | All strings translatable (`nerdywithme-seo`) |
| Uninstall | Optional clean delete of settings/meta/tables |
| Backups | Export settings + redirects JSON |

---

## 10. Testing strategy

### Manual
- Home, post, page, category, tag, author, search, 404
- Tools hub + each tool URL
- With/without featured image
- Redirect hit + 404 capture
- Import sample Yoast data on a staging copy

### Validators
- Google Rich Results Test
- Schema Markup Validator
- Facebook Sharing Debugger / opengraph.xyz
- Twitter/X card validator (or open graph fallbacks)
- XML sitemap fetch + URL sampling

### Automated (Phase 3)
- Unit tests for template variable replacement
- Unit tests for redirect matcher (exact/regex/priority)
- Schema graph shape tests
- Import field mapping tests

---

## 11. Packaging & release

Follow existing `.cursor/rules/packaging-release.mdc`:
- Version bump on every package
- ZIP root folder `nerdywithme-seo/`
- Artifact names: `nerdywithme-seo-X.Y.Z.zip` + latest aliases
- Copy to `C:\Users\DELL\Documents\nwm zip files`
- Start version at `0.1.0` for first usable Phase 1 build

Suggested milestone tags:
- `seo-0.1.x` — Phase 1 usable internal
- `seo-0.2.x` — Phase 2 competitor depth
- `seo-1.0.0` — production replace on live NWM

---

## 12. Effort & risk estimate

### Effort (one focused builder)
| Phase | Calendar estimate | Notes |
|---|---|---|
| Phase 0 | ~1 week | Skeleton + conflicts + tools bridge |
| Phase 1 | ~3–4 weeks | Real replace-Yoast-free capability |
| Phase 2 | ~4–5 weeks | Redirects/import/AI/internal links |
| Phase 3 | ~2–3 weeks | Hardening + health + docs |
| **Total to 1.0** | **~10–14 weeks** | Assumes steady focus, not nights-only |

### Major risks
1. **Scope creep** into SaaS analytics / local / Woo before core is solid  
2. **Duplicate meta bugs** with tools plugin or residual competitor plugins  
3. **Schema invalid graphs** causing rich-result loss  
4. **Redirect loops** hurting crawlability  
5. **Editor UX complexity** slowing publishing instead of helping  
6. **Underestimating import edge cases** from Yoast/Rank Math  
7. **AI provider costs/keys** if treated as mandatory

### Mitigations
- Module flags + strict phase gates
- Single head-output owner policy
- Validator checklist in Definition of Done
- Redirect dry-run / test tool
- Import on staging first with diff report
- AI always optional

---

## 13. Definition of Done for “we can drop Yoast/AIOSEO”

All must be true on staging:
- [ ] No other SEO plugin active
- [ ] Titles/descriptions/social correct on home, post, page, tool URL
- [ ] Canonical + robots behave as configured
- [ ] Schema validates for Article + Breadcrumb + Organization + Tool page
- [ ] Sitemap lists intended URLs and excludes noindex
- [ ] Breadcrumbs render where enabled
- [ ] Redirects + 404 monitor work
- [ ] Content analysis helps editors without blocking publish
- [ ] Tools SEO still correct via bridge
- [ ] Import path documented (even if site is greenfield)
- [ ] Performance acceptable on mobile (no extra frontend weight)
- [ ] Packaged ZIP installs cleanly on WordPress

---

## 14. Open decisions for your review

Mark each: **Approve / Change / Defer**

1. **Sitemap strategy:** enhance WP core sitemaps (A) vs fully custom sitemap system (B)?  
   - Recommendation: **A for v1**, revisit if blocked.
2. **Conflict handling:** force-deactivate competitors’ output vs refuse to run until they are removed?  
   - Recommendation: **refuse output until others deactivated** (safest).
3. **Tools meta ownership:** keep fields in Tools settings, SEO only renders — yes/no?  
   - Recommendation: **Yes**.
4. **Focus keyphrases count:** 5 to match Rank Math free? or unlimited with soft UI limit?  
   - Recommendation: **5 in v1**, configurable later.
5. **AI in scope for 1.0?** optional module vs wait until after redirects/import?  
   - Recommendation: **after redirects/import; optional for 1.0**.
6. **Breadcrumbs in theme by default** or shortcode-only until you approve theme patch?  
   - Recommendation: **helper + shortcode first**, theme mount after visual approval.
7. **404 IP logging:** off by default?  
   - Recommendation: **Off**.
8. **Public product ambition:** NWM-only internal tool vs WordPress.org distribution later?  
   - Recommendation: **build as clean redistribute-ready code**, decide publishing later.
9. **First live cutover target:** after Phase 1 or only after Phase 2?  
   - Recommendation: **internal use after Phase 1**, public/production cutover after Phase 2 redirects+import.
10. **Any must-have content types now?** (glossary CPT, courses, news, video)  
    - Recommendation: list them before schema Phase 2 starts.

---

## 15. Suggested review workflow

1. You comment directly in this file (or chat notes referencing section numbers).
2. We revise until Sections **1–2**, **6 phase gates**, and **14 decisions** feel right.
3. Freeze **MVP = Phase 0 + Phase 1** scope.
4. Only then create `plugins/nerdywithme-seo/` and begin implementation.
5. Keep this plan updated as a living spec; don’t silently expand scope mid-build.

---

## 16. Go / No-Go

**Go means:** approve product thesis, phase plan, architecture, and answered Section 14 decisions.  
**No-Go / revise means:** change scope (for example shrink to Phase 1 only, or add Woo/Local now).

- [ ] Product thesis approved
- [ ] Competitive scope approved (including explicit deferrals)
- [ ] Architecture + tools bridge approved
- [ ] Phase 0–3 plan approved
- [ ] Section 14 decisions answered
- [ ] Ready to build Phase 0 skeleton

**Owner sign-off:** ______________________  
**Date:** ______________________

---

## Appendix A — Admin IA (proposed tabs)

1. **Dashboard** — SEO health snapshot  
2. **General** — site name usage, separators, knowledge graph (org/person)  
3. **Titles & Meta** — template editor per type  
4. **Social** — defaults + verification  
5. **Schema** — defaults + type toggles  
6. **Sitemaps** — includes/excludes  
7. **Search Appearance / Robots** — robots.txt, noindex defaults  
8. **Redirects** — rules table  
9. **404 Monitor** — log + actions  
10. **Tools Bridge** — calculator SEO status  
11. **Import / Export**  
12. **Advanced** — modules, uninstall, conflict, AI keys  

Editor metabox / sidebar sections:
- Snippet preview  
- Focus keyphrases + analysis  
- Social  
- Schema  
- Advanced (canonical, robots, breadcrumbs title)

---

## Appendix B — Success metrics (after launch)

- Zero duplicate meta/schema issues in spot checks
- All priority tool URLs indexed with intended titles
- 404→redirect coverage for known old URLs within first month
- Editors can update SEO fields without developer help
- No meaningful regression in mobile performance scores attributable to SEO plugin
- Ability to say: “We are not depending on Yoast/AIOSEO anymore.”

---

## Appendix C — Out of scope reminders

Not in 1.0 unless explicitly promoted from Section 14:
- Hosted rank tracking product
- AI brand-mention monitoring across LLMs
- Full local multi-location SEO
- WooCommerce product SEO
- Automatic guest-post / link-building spam features
- Guaranteed ranking claims in UI copy
