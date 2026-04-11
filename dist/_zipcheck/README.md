# NerdyWithMe Tools

Repo-local WordPress plugin scaffold for:

- trading calculators
- ad slot controls
- shortcode output for theme placements

Current first-pass features:

- settings page under `NWM Tools`
- ad slot storage for:
  - homepage after hero
  - sidebar
  - single inline
  - footer
- shortcode:
  - `[nwm_ad_slot slot="sidebar"]`
- shortcode:
  - `[nwm_risk_calculator]`

Next recommended steps:

1. connect theme ad placements to `nerdywithme_tools_render_ad_slot()`
2. add more calculators
3. add block editor support if needed
