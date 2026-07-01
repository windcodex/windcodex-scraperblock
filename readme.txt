=== WindCodex ScraperBlock – Block AI Scrapers & Bots from WordPress & WooCommerce ===
Contributors: windcodex
Tags: bot protection, ai scraper, content protection, web scraper, woocommerce
Requires at least: 5.8
Tested up to: 7.0
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Block AI scrapers, price bots, and content theft from your WordPress & WooCommerce site. robots.txt controls, noai meta tags, rate limiting. Free.

== Description ==

**WindCodex ScraperBlock** is a free WordPress plugin that blocks AI scrapers, content crawlers, and unwanted bots from harvesting your site. Protect blog posts, product pages, and proprietary content from being fed into AI training datasets – without slowing your site down for real visitors.

Setup takes under two minutes: install, enable the protections you want, save.

= Why Your Site Needs Bot Protection =

AI crawlers – including GPTBot (OpenAI), ClaudeBot (Anthropic), Google-Extended, ByteSpider, CCBot (Common Crawl), and dozens more – continuously scrape WordPress sites for training data. For content creators, WooCommerce store owners, and publishers, this means:

* Your original content gets harvested and used without permission or attribution.
* AI training traffic inflates your server load and bandwidth costs.
* **WooCommerce store owners face a specific threat** – price bots and competitor scrapers continuously harvest product prices, stock levels, and catalog data to undercut your pricing in real time.
* Proprietary product descriptions, pricing strategies, and business data are exposed to competitors and AI systems.
* Scraper traffic can mask real user patterns in your analytics.

ScraperBlock gives you practical, layered defences against these threats – all from one settings screen.

= Free Features =

**Protection Controls**
* **Master protection switch** – Enable or disable all ScraperBlock protections with a single toggle.
* **50+ default bot signatures** – Pre-loaded, categorized list of known AI scrapers, content crawlers, and price bots including GPTBot (OpenAI), ClaudeBot (Anthropic), Google-Extended, ByteSpider (ByteDance), CCBot (Common Crawl), Diffbot, PerplexityBot, and more. Maintained and updated regularly.
* **Custom user-agent rules** – Add your own bot signatures, one per line. Target bots not in the default list.

**Blocking Methods**
* **Runtime user-agent blocking** – Intercepts matching bots at the PHP layer before any content is served. Works on all server types.
* **robots.txt blocking** – Automatically injects `Disallow` directives for blocked bots into your robots.txt file. Signals crawlers to stay away before they visit.
* **Apache `.htaccess` blocking** – Adds server-level `RewriteRule` blocks for matched user-agents. Stops bots before they reach PHP (Apache only).

**AI Opt-Out Meta Tags**
* **`noai` and `noimageai` meta tags** – Outputs `<meta name="robots" content="noai, noimageai">` on your pages. Signals AI training opt-out to crawlers that respect meta directives.
* **Per-page meta control** – Override protection settings on individual posts and pages using a meta box in the editor. Enable, disable, or customize protection per page.

**Monitoring**
* **Basic block log** – Stores the last 50 blocked request events with IP, user-agent, URL path, reason, and timestamp.
* **Live dashboard count** – Shows a basic count of blocked requests from the last 24 hours. See activity at a glance without leaving wp-admin.
* **Basic rate limiting** – Limit request frequency from individual IPs to reduce scraper throughput.

= Who Needs ScraperBlock? =

* **Bloggers and content creators** – Protect original articles and creative work from AI training scrapes.
* **WooCommerce store owners** – Block competitor price scrapers and AI bots that harvest product prices, descriptions, and stock levels to undercut your pricing or feed your catalog into AI systems.
* **News and media publishers** – Opt out of AI content aggregation and training dataset inclusion.
* **Membership and course sites** – Prevent paid content from being scraped by bots that bypass login pages via API or sitemap traversal.
* **Agencies** – Deploy consistent bot protection across client sites.

= How It Works =

1. Install and activate ScraperBlock.
2. Go to **Settings > ScraperBlock**.
3. Enable the protection modules you want (runtime blocking, robots.txt, meta tags, per-page control).
4. Save settings.
5. Monitor blocked requests in the Logs panel and Dashboard count widget.

= 🚀 Pro Version =

Need content poisoning, honeypot traps, behavioural detection, real-time threat feed, geo-based blocking, IP allowlists, block scheduling, and advanced analytics?
[ScraperBlock Pro is available at windcodex.com](https://windcodex.com/product/woocommerce-bot-protection-plugin/)

ScraperBlock Pro adds advanced features for high-traffic sites and serious content protection:

* **Content poisoning** – Serve subtly corrupted content to detected scrapers, degrading the value of stolen data.
* **Honeypot traps** – Invisible links that only bots follow – automatically flag and block crawlers.
* **Behavioural detection** – Identify bots by traffic pattern, not just user-agent string.
* **Real-time threat feed** – Cloud-updated block list with new bot signatures pushed automatically.
* **Geo-based blocking** – Block all traffic from specific countries at the application layer.
* **IP allowlists & blocklists** – Block individual IP addresses and CIDR ranges in addition to user-agents.
* **Block scheduling** – Define time windows when protection is active or relaxed.
* **Advanced analytics** – Full traffic breakdown by bot, country, URL, and time range with CSV export.

= Requirements =

* WordPress 5.8 or higher
* PHP 7.4 or higher
* Apache is required only for `.htaccess` blocking mode. All other modes work on any server.

= Privacy =

ScraperBlock stores technical security data (IP address, user-agent string, URL path, block reason, action, and timestamp) in your WordPress database for local monitoring purposes. The free plugin does not require or contact any third-party API. No data is transmitted off your server.

== Installation ==

**From your WordPress dashboard:**

1. Go to **Plugins > Add New**.
2. Search for **WindCodex ScraperBlock** or **ScraperBlock**.
3. Click **Install Now**, then **Activate**.
4. Open **Settings > ScraperBlock** to configure your protections.

**Manual installation:**

1. Download the plugin ZIP file.
2. Upload the `windcodex-scraperblock` folder to `/wp-content/plugins/`.
3. Activate through the **Plugins** screen in WordPress.

== Frequently Asked Questions ==

= Does this plugin block all bots automatically? =

ScraperBlock blocks requests that match the built-in default bot list or your custom user-agent signatures. The default list includes 50+ known AI scrapers and content crawlers. Bots not on the list are not blocked unless you add their signature manually.

= Which AI bots does ScraperBlock block by default? =

The default list includes GPTBot (OpenAI), ClaudeBot (Anthropic), Google-Extended (Google AI training), ByteSpider (ByteDance), CCBot (Common Crawl), Diffbot, PerplexityBot, YouBot, and more. The list is maintained and updated with new bot signatures regularly.

= Will ScraperBlock affect Google Search or Bing indexing? =

No. The default bot list targets AI training and content scraping crawlers only. Google Search bot (Googlebot), Bing bot (bingbot), and other standard search engine crawlers are not in the default list and are not blocked. Your SEO and search indexing are unaffected.

= Does blocking bots in robots.txt actually stop them? =

robots.txt is a voluntary standard – compliant crawlers will respect it, but malicious or poorly configured bots may ignore it. For stronger enforcement, use runtime blocking (PHP layer) or .htaccess blocking (Apache) in addition to robots.txt directives. ScraperBlock lets you enable all three simultaneously.

= What is the difference between runtime blocking and .htaccess blocking? =

Runtime blocking intercepts requests at the PHP level – it works on any web server (Apache, Nginx, LiteSpeed, etc.) but runs after the server has already accepted the connection. Apache `.htaccess` blocking runs at the web server level before PHP executes, making it more efficient for high-traffic sites on Apache. Both can be enabled together.

= Does `.htaccess` blocking work on Nginx or LiteSpeed? =

No. `.htaccess` blocking is Apache-specific. For Nginx and LiteSpeed sites, use runtime blocking and robots.txt mode – both work on any server.

= What are noai meta tags? =

`noai` and `noimageai` are meta tag directives that signal to AI crawlers that the page's content and images should not be used for AI training. They follow the same convention as the existing `noindex` and `nofollow` meta robots tags. Compliance varies by crawler – ScraperBlock outputs these tags on all pages (or per-page if configured) so that compliant crawlers respect your opt-out.

= Can I disable protection on a specific page? =

Yes. ScraperBlock adds a meta box to the post and page editor. You can enable or disable protection individually per page, overriding the global settings.

= How do I monitor which bots are being blocked? =

The **Logs** panel shows the last 50 blocked events with IP address, user-agent, URL, reason, and timestamp. The **Dashboard** widget shows a basic count of blocks in the last 24 hours. Advanced analytics with full historical data, filtering, and CSV export are available in ScraperBlock Pro.

= Does ScraperBlock block competitor price scrapers? =

Yes. Price scrapers typically use identifiable user-agent strings. If a competitor's price bot is in the default block list or you add its user-agent signature manually, ScraperBlock will block it at the PHP layer before any content is served. For WooCommerce stores, this prevents competitors from automatically monitoring and undercutting your prices.

= What is content poisoning and is it in the free version? =

Content poisoning is an advanced technique where detected bots are served subtly incorrect or corrupted content instead of a hard block – degrading the quality of scraped data without alerting the scraper operator. Content poisoning is a ScraperBlock Pro feature. The free version uses hard blocking (403 or redirect response) for all matched bots.

= How do I know if my site is being scraped right now? =

Go to **Settings > ScraperBlock > Logs**. The block log shows the last 50 blocked events with IP address, user-agent, URL path, and timestamp. The Dashboard widget shows a count of blocked requests in the last 24 hours. If you see high volume from unfamiliar user-agents not yet in the default list, copy the user-agent string and add it to your custom signatures. Advanced analytics with full historical data and filtering are available in ScraperBlock Pro.

= Does rate limiting affect real visitors? =

Rate limiting applies per IP address and is tuned to catch high-frequency automated requests – not normal browsing behaviour. Real visitors do not make hundreds of requests per minute the way scrapers do, so the threshold should not affect them under normal conditions.

= Is this plugin compatible with Cloudflare? =

ScraperBlock's runtime and robots.txt/meta modes work independently of Cloudflare. If you use Cloudflare's bot management features, they complement each other – Cloudflare blocks at the edge, ScraperBlock blocks at the application layer for any traffic that reaches your origin server. `.htaccess` mode applies only to requests that reach your Apache server, same as without Cloudflare.

= Does ScraperBlock affect my site's performance? =

ScraperBlock is a lightweight plugin. User-agent matching is a string comparison that runs early in the request lifecycle and exits immediately for legitimate traffic. The performance overhead for real visitors is negligible.

== Screenshots ==

1. **Settings page** – Master switch, protection modules, and custom user-agent editor.
2. **Default bot list** – Pre-loaded list of 50+ known AI scrapers with toggle controls.
3. **Block logs panel** – Last 50 blocked events with IP, user-agent, URL, and reason.
4. **Per-page protection control** – Meta box in the post and page editor for individual page overrides.

== Changelog ==

= 1.0.1 =
* Improved: Settings page UI for better usability.
* Added: Admin review request notice.
* Added: Pro features notice.

= 1.0.0 =
* Initial release.
* Runtime user-agent blocking with 50+ default AI scraper and crawler signatures.
* robots.txt blocking directives injection.
* Apache `.htaccess` blocking rules.
* `noai` and `noimageai` meta tag output.
* Per-page protection control in post and page editor.
* Custom user-agent signature editor (one per line).
* Basic rate limiting per IP.
* Basic block log (last 50 events) with IP, user-agent, URL, reason, and timestamp.
* Live dashboard count (last 24 hours blocked requests).

== Upgrade Notice ==

= 1.0.1 =
Adds inline Pro upsell banner, review request notice, and help button in the settings header. No database changes – safe to update.

= 1.0.0 =
Initial release – no upgrade steps required.
