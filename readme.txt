=== WindCodex ScraperBlock – Block Web Scrapers & Bots from Your WordPress Site ===
Contributors: windcodex
Tags: ai bot blocker, bot protection, content protection, noai, crawler blocking
Requires at least: 5.8
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Block AI scrapers and unwanted crawlers with bot rules, robots.txt and noai meta controls, per-page settings, and basic rate limiting.

== Description ==

**ScraperBlock** is a lightweight AI bot blocker plugin for WordPress. It helps prevent content scraping by known crawler user-agents while keeping setup simple for site owners.

Use layered controls in one settings screen:

* Bot and scraper user-agent blocking
* robots.txt blocking directives
* Optional Apache `.htaccess` blocking
* noai / meta tag output
* Per-page protection control
* Basic block log and dashboard count

= Why Use ScraperBlock =

* Protect blog posts, product pages, and site content from scraping bots.
* Reduce unwanted automated traffic with practical bot filtering.
* Control protections with clear toggles and simple defaults.
* Monitor recent blocked activity without extra services.

= Free Features =

* Enable Protection (master switch)
* robots.txt blocking
* `.htaccess` blocking (Apache)
* noai / meta tags for AI bot opt-out
* Per-page control on posts/pages
* Basic rate limiting
* Custom user-agents (one signature per line)
* Default bot list (50+ known bots)
* Basic block log (last 50 events)
* Live Dashboard (basic last 24-hour count)

= How It Works =

1. Install and activate ScraperBlock.
2. Go to **Settings > ScraperBlock**.
3. Enable the protection modules you want.
4. Save settings.
5. Review blocked requests in Logs and dashboard summary.

= Requirements =

* WordPress 5.8+
* PHP 7.4+
* Apache required only for `.htaccess` mode

= Privacy =

ScraperBlock stores technical security data (for example IP, user-agent, URL path, reason, action, and timestamp) in your WordPress database for local monitoring. The free plugin does not require a third-party API.

== Installation ==

1. Upload the plugin ZIP in **Plugins > Add New > Upload Plugin**.
2. Click **Install Now** and **Activate**.
3. Open **Settings > ScraperBlock** and configure options.

== Frequently Asked Questions ==

= Does this plugin block all bots automatically? =

It blocks bots matching the built-in and custom user-agent signatures.

= Will it affect normal search engines? =

The default list is focused on scraping and AI crawler signatures. Review custom entries carefully.

= Does `.htaccess` blocking work on Nginx? =

No. `.htaccess` is Apache-only. Use robots/meta/runtime controls on Nginx.

= Can I disable protection on one page? =

Yes. Use the per-page ScraperBlock meta option.

= Do analytics and CSV export exist in free? =

Advanced analytics and CSV export are available in ScraperBlock Pro.

== Screenshots ==

1. ScraperBlock settings and protection toggles.
2. Default bot list and custom user-agent editor.
3. Basic logs panel.
4. Per-page protection control in editor.

== Changelog ==

= 1.0.0 =
* Initial release.
* AI/scraper bot blocking.
* robots.txt and `.htaccess` controls.
* noai/meta controls and per-page rules.
* Basic rate limiting, logs, and dashboard count.

== Upgrade Notice ==

= 1.0.0 =
Initial release.