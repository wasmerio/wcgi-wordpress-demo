=== WP Fastest Cache ===
Contributors: emrevona
Donate link: http://profiles.wordpress.org/emrevona/
Tags: cache, Optimize, performance, wp-cache, core web vitals
Requires at least: 3.3
Tested up to: 6.1
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The simplest and fastest WP Cache system

== Description ==

<h4>Official Website</h4>

You can find more information on our web site (<a href="http://www.wpfastestcache.com/">wpfastestcache.com</a>)

When a page is rendered, php and mysql are used. Therefore, system needs RAM and CPU. 
If many visitors come to a site, system uses lots of RAM and CPU so page is rendered so slowly. In this case, you need a cache system not to render page again and again. Cache system generates a static html file and saves. Other users reach to static html page.
<br><br>
In addition, the site speed is used in Google’s search ranking algorithm so cache plugins that can improve your page load time will also improve your SEO ranking.
<br><br>
Setup of this plugin is so easy. You don't need to modify the .htacces file. It will be modified automatically.

<h4>Features</h4>

1. Mod_Rewrite which is the fastest method is used in this plugin
2. All cache files are deleted when a post or page is published
3. Admin can delete all cached files from the options page
4. Admin can delete minified css and js files from the options page
5. Block cache for specific page or post with Short Code
6. Cache Timeout - All cached files are deleted at the determinated time
7. Cache Timeout for specific pages
8. Enable/Disable cache option for mobile devices
9. Enable/Disable cache option for logged-in users
10. SSL support
11. CDN support
12. Cloudflare support
13. Preload Cache - Create the cache of all the site automatically
14. Exclude pages and user-agents
15. WP-CLI cache clearing

<h4>Performance Optimization</h4>

In the premium version there are many features such as Minify Html, Minify Css, Enable Gzip Compression, Leverage Browser Caching, Add Expires Headers, Combine CSS, Combine JS, Disable Emoji.

1. Generating static html files from your dynamic WordPress blog
2. Minify Html - You can decrease the size of page
3. Minify Css - You can decrease the size of css files
4. Enable Gzip Compression - Reduce the size of files sent from your server to increase the speed to which they are transferred to the browser
5. Leverage browser caching - Reduce page load times for repeat visitors
6. Combine CSS - Reduce number of HTTP round-trips by combining multiple CSS resources into one
7. Combine JS
8. Disable Emoji - You can remove the emoji inline css and wp-emoji-release.min.js

<h4>Premium Performance Optimization</h4>

The free version is enough to speed up your site but in the premium version there are extra features such as Mobile Cache, Widget Cache, Minify HTML Plus, Minify CSS Plus, Minify JS, Combine JS Plus, Defer Javascript, Optimize Images, Convert WebP, Database Cleanup, Google Fonts Async, Lazy Load for super fast load times.

1. Mobile Cache
2. Widget Cache
3. Minify HTML Plus
4. Minify CSS Plus
5. Minify Javascript - Minifying JavaScript files can reduce payload sizes and script parse time
6. Combine JS Plus
7. Defer Javascript - Eliminate render-blocking JavaScript resources. Consider delivering critical JS inline and deferring all non-critical JS
8. Optimize Images - Optimized images load faster and consume less cellular data
9. Convert WebP - Serve images in next-gen formats. Image formats like JPEG 2000, JPEG XR, and WebP often provide better compression than PNG or JPEG, which means faster downloads and less data consumption
10. Database Cleanup
11. Google Fonts Async
12. Lazy Load - Defer offscreen images. Consider lazy-loading offscreen and hidden images after all critical resources have finished loading to lower time to interactive

<h4>Information</h4>

It is very inconvenient to use multiple caching plugins at the same time. That's why you need to disable plugins such as LiteSpeed Cache, WP-Optimize, W3 Total Cache, WP Super Cache, SiteGround Optimizer, Breeze while using WP Fastest Cache.

WP Fastest Cache is compatible with most popular plugins such as Contact Form 7, Yoast SEO, Elementor Website Builder, Classic Editor, Akismet Spam Protection, WooCommerce, Contact Form by WPForms, Really Simple SSL, All-in-One WP Migration, Yoast Duplicate Post, Wordfence Security – Firewall & Malware Scan, WordPress Importer, UpdraftPlus WordPress Backup Plugin, MonsterInsights, All in One SEO, WP Mail SMTP by WPForms.

<h4>Supported languages: </h4>

* 中文 (by suifengtec)
* Deutsch
* English
* Español (by Javier Esteban)
* Español de Venezuela (by Yordan Soares)
* Español de Argentina (by Mauricio Lopez)
* فارسی (by Javad Rahimi)
* Français (by Cyrille Sanson)
* Italiana (by Luisa Ravelli)
* 日本語 (by KUCKLU)
* Nederlands (by Frans Pronk https://ifra.nl)
* Polski (by roan24.pl)
* Português
* Português do Brasil (Mario Antonio Sesso Junior)
* Română
* Русский (by Maxim)
* Slovenčina
* Suomi (by Arhi Paivarinta)
* Svenska (by Linus Wileryd)
* Türkçe
* 繁體中文 (Alex Lion)

== Installation ==

1. Upload `wp-fastest-cache` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Permission of .htacces must 644
4. Enable this plugin on option page

== Screenshots ==

1. Performance Comparison
2. Other Performance Comparison
3. Without Cache
4. With Cache
5. Main Settings Page
6. Preload
7. New Post
8. Update Cache
9. Delete Cache
10. All cached files are deleted at the determinated time
11. Block caching for post and pages (TinyMCE)
12. Clean cached files via admin toolbar easily
13. Exclude Page
14. CDN
15. Enter CDN Information
16. File Types
17. Specify Sources
18. Database Cleanup

== Changelog ==

= 1.1.0 =
* to show cache if the url contains a parameter of Yandex Click Identifier
* [FEATURE] Excluding Yandex Click Identifier [<a target="_blank" href="https://www.wpfastestcache.com/features/cache-url-with-yandex-click-id-parameters-querystring/">Details</a>]
* [FEATURE] Adding "Regular Expression" option for the Exclude Pages feature [<a target="_blank" href="https://www.wpfastestcache.com/features/using-regular-expression-to-exclude-a-page/">Details</a>]

= 1.0.9 =
* to improve the style of exclude feature wizard
* to fix hiding the toolbar when logged in
* to fix PHP Notice: Undefined offset: -1 in js-utilities.php on line 67
* to fix PHP Fatal error: Uncaught Error: Non-static method cannot be called statically in clearing-specific-pages.php on line 58

= 1.0.8 =
* to stop showing the "DONOTCACHEPAGE is defined as TRUE" comment in the footer for the ajax requests
* <strong>[FEATURE]</strong> Clearing Specific Pages [<a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-of-specific-urls-when-updating-or-posting/">Details</a>]
* to fix the site url on the exclude page
* to fix PHP Notice:  Function WP_User_Query::query was called incorrectly. User queries should not be run before the plugins_loaded hook

= 1.0.7 =
* <strong>[FEATURE]</strong> Clearing Specific Pages (BETA) [<a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-of-specific-urls-when-updating-or-posting/">Details</a>]
* to add last-modified header when cache is served via php

= 1.0.6 =
* to serve sources via cdn for excluded pages if cdn is enabled
* to fix PHP Notice: Undefined offset: -1 in js-utilities.php  on line 48

= 1.0.5 =
* to fix E_NOTICE: Undefined variable: path in wpFastestCache.php on line 2142
* to add excluding feature for Buffer Callback Filter [<a target="_blank" href="https://www.wpfastestcache.com/tutorial/buffer-callback-filter/#exclude">Details</a>]

= 1.0.4 =
* to add avif extensions for cdn
* to add WPFC_SERVE_ONLY_VIA_CACHE [<a target="_blank" href="https://www.wpfastestcache.com/tutorial/how-to-serve-cache-only-via-php/">Details</a>]

= 1.0.3 =
* Photon will no longer be supported [<a target="_blank" href="https://www.wpfastestcache.com/blog/photon-will-no-longer-be-supported/">Details</a>]
* to exclude category url for preload if any error occurs

= 1.0.2 =
* to add WP-CLI command for clearing cache of a post [<a target="_blank" href="https://www.wpfastestcache.com/features/wp-cli-commands/">Details</a>]
* to fix Warning scandir() at wpFastestCache.php:302
* to fix Warning file_put_contents(/cache/wpfc-minified/index.html) at cache.php:1090
* to fix Warning unlink(wp-cache-config.php) admin.php:885

= 1.0.1 =
* to clear only cache of post/page even if the "update post" option is disabled

= 1.0.0 =
* to define the save_settings() function of single preload feature as static function

= 0.9.9 =
* to clear cache when regular price of woocommorce is updated
* refactoring of Automatic Cache

= 0.9.8 =
* to clear cache after updating Elementor Website Builder plugin
* to clear cache after theme or plugin update by custom settings [<a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-after-theme-or-plugin-update/">Details</a>]
* to enable Auto Cache Panel for the classic editor which is enabled via add_filter()

= 0.9.7 =
* to clear cache after theme or plugin update by default [<a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-after-theme-or-plugin-update/">Details</a>]

= 0.9.6 =
* refactoring of serving non-exists minified files
* to fix htaccess rule for Polylang plugin
* to fix condition of clear cache after plugin update

= 0.9.5 =
* to prevent generating cache when DONOTCACHEPAGE is defined as true for Divi theme
* to add nonce security system for cdn saving 

= 0.9.4 =
* to make compatible the Auto Cache feature with the Disable Gutenberg plugin
* refactoring of rewrite rule of HTTP_USER_AGENT
* to check that resources have been successfully optimized

= 0.9.3 =
* to prevent removing "/" for exclude rules
* <strong>[FEATURE]</strong> to add "pause" feature for cdn [<a target="_blank" href="https://www.wpfastestcache.com/features/temporarily-disable-cdn/">Details</a>]
* to add wpfc_clear_all_site_cache() for clearing cache of all sites [<a target="_blank" href="https://www.wpfastestcache.com/tutorial/delete-the-cache-by-calling-the-function/">Details</a>]
* to add spinner for the buttons on the cdn wizard
* refactroing of excluding "There has been a critical error on this website" page

= 0.9.2 =
* <strong>[FEATURE]</strong> to create cache after publishing new post or updating a post [<a target="_blank" href="https://www.wpfastestcache.com/features/automatic-cache/">Details</a>]
* <strong>[FEATURE]</strong> Clear cache after activate/deactivate plugin [<a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-after-activate-deactivate-a-plugin/">Details</a>]

= 0.9.1.9 =
* <strong>[FEATURE]</strong> Clear cache after switch theme [<a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-after-switch-theme/">Details</a>]

= 0.9.1.8 =
* to make compatible the preload feature with WPML
* refactoring of clearing cache of content which is moved to trash
* to fix Notice: Undefined variable: no_selected in single-preload.php on line 39
* to add image/avif for browser caching

= 0.9.1.7 =
* to clear cache of the store homepage after WooCommerce order
* to fix vulnerability (discoverd by Gen Sato)
* to clear cache after Woocommerce order status changed
* to add WPFC_DISABLE_CLEARING_CACHE_AFTER_WOOCOMMERCE_ORDER_STATUS_CHANGED [<a target="_blank" href="https://www.wpfastestcache.com/tutorial/woocommerce-settings/#after-order-status-changed">Details</a>]

= 0.9.1.6 =
* to fix Notice: Undefined variable: order_arr in preload.php on line 161
* to fix Notice: Undefined property: stdClass::$go in preload.php on line 440
* to start using the API Token system instead of Global API for Cloudflare [<a target="_blank" href="https://www.wpfastestcache.com/tutorial/wp-fastest-cache-cloudflare/">Details</a>]
* to fix removing backslashes issue in the pre tag
* to disable cache for the IP based urls on the bitnami servers
* to disable cdn if the query string contains wc-api

= 0.9.1.5 =
* <strong>[FEATURE]</strong> to add Re-Order feture for Preload [<a target="_blank" href="https://www.wpfastestcache.com/features/re-order-preload/">Details</a>]

= 0.9.1.4 =
* to fix saving "Update Post" settings issue
* to fix saving "New Post" settings issue
* <strong>[FEATURE]</strong> Compatible with the AMP Takeover feature of <a target="_blank" href="https://wordpress.org/plugins/accelerated-mobile-pages/">AMP for WP – Accelerated Mobile Pages</a>

= 0.9.1.3 =
* to fix PHP Notice: Undefined offset: -1 js-utilities.php on line 84
* to show the details of the error on the Cloudflare cdn integraiton

= 0.9.1.2 =
* to add webp extension for CDN
* to replace the attribute which is data-bg-webp with cdn-url
* to save the Cloudflare zone id instead of getting it via api continuously
* to prevent calling cloudflare_clear_cache() function multiple times

= 0.9.1.1 =
* to prevent caching 403 forbidden page which is generated by iThemes Security plugin
* to convert domain name from IDNA ASCII to Unicode for CDN
* to minify the imported css sources
* to round if the preload number is decimal

= 0.9.1.0 =
* to fix PHP Notice: Undefined property: stdClass::$excludekeywords in wpFastestCache.php on line 1935
* to fix Undefined offset: 0 in cache.php on line 865

= 0.9.0.9 =
* <strong>[FEATURE]</strong> to add wizard allows you to show the clear cache button which exists on the admin toolbar based on user roles [<a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-link-on-the-toolbar/">Details</a>]
* to fix the replace problem when the cdn-url starts with a number
* to fix the little issue on the cloudflare integration

= 0.9.0.8 =
* to exclude PDF files from caching
* to add Modified Time into htaccess
* to add "Clear Cache of All Sites" feature for Clear Cache via URL [<a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-via-url/">Details</a>]

= 0.9.0.7 =
* <strong>[FEATURE]</strong> to add "exclude sources" feature for CDN
* to remove the DNS prefetch of s.w.org when emoji is disabled
* <strong>[FEATURE]</strong> to add wpfc_css_content filter [<a target="_blank" href="https://www.wpfastestcache.com/tutorial/modify-minified-css-by-calling-the-function-hook/">Details</a>]
* to fix scandir(): (errno 2): No such file or directory on js-utilities.php line 238

= 0.9.0.6 =
* <strong>[FEATURE]</strong> to add WP-CLI command for clearing minified sources [<a target="_blank" href="https://www.wpfastestcache.com/features/wp-cli-commands/">Details</a>]
* to fix Warning: parse_url() expects parameter 1 to be string, object given in preload.php on line 458
* <strong>[FEATURE]</strong> Compatible with <a target="_blank" href="https://wordpress.org/plugins/multiple-domain/">Multiple Domain</a>
* <strong>[FEATURE]</strong> to add Clear Cache of All Sites button [<a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-of-all-sites/">Details</a>]

= 0.9.0.5 =
* to fix replacing urls on the json source with cdn url
* to fix clearing cache on sites using Polylang plugin
* to prevent creating cache for feed of nonexistent content

= 0.9.0.4 =
* to fix PHP Fatal error:  Call to a member function lazy_load() on null in cache.php on line 798
* to clear sitemap cache after updating or publishing post
* to clear cache of the static posts page
* to replace urls on data-siteorigin-parallax attribute with cdn-url
* to fix the problem abour "Mobile" option
* <strong>[FEATURE]</strong> Clear cache after theme or plugin update [<a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-after-theme-or-plugin-update/">Details</a>]

= 0.9.0.3 =
* <strong>[FEATURE]</strong> Compatible with Multiple Domain Mapping on single site
* <strong>[BETA FEATURE]</strong> to create cache after publishing new post or updating a post [<a target="_blank" href="https://www.wpfastestcache.com/features/automatic-cache/">Details</a>]
* to fix clearing search (/?s=) result cache 
* to add settings link on the plugin list
* <strong>[FEATURE]</strong> Compatible with Polylang with one different subdomain or domain per language
* to exclude url which ends with slash if the permalink does not end with slush
* to exclude images for cdn if the url contains brizy_media=

= 0.9.0.2 =
* <strong>[FEATURE]</strong> to add Spanish (Argentina) language
* to add WPFC_TOOLBAR_FOR_SHOP_MANAGER [<a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-link-at-the-toolbar/">Details</a>]
* to support MultiSite
* to add wpfc_exclude_current_page() for excluding current page [<a target="_blank" href="https://www.wpfastestcache.com/features/exclude-page/#hook">Details</a>]
* <strong>[FEATURE]</strong> to add French language
* <strong>[FEATURE]</strong> to add Slovak language
* to show the solution for AWS S3 Access Denied [<a target="_blank" href="https://www.wpfastestcache.com/warnings/amazon-s3-cloudfront-access-denied-403-forbidden/">Details</a>]
* to show the solution for Using CDN on SSL Sites [<a target="_blank" href="https://www.wpfastestcache.com/warnings/how-to-use-cdn-on-ssl-sites/">Details</a>]

= 0.9.0.1 =
* to remove the clear cache button from column and to add clear cache action on row actions [<a target="_blank" href="https://www.wpfastestcache.com/tutorial/clear-cache-for-specific-page/">Details</a>]
* to hide clear cache icon on toolbar for IE
* to fix replacing cdn-url on data-product_variations attribute
* to add WPFC_TOOLBAR_FOR_EDITOR [<a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-link-at-the-toolbar/">Details</a>]
* <strong>[FEATURE]</strong> to add Persian language
* <strong>[FEATURE]</strong> to add Chinese (Taiwan) language
* <strong>[FEATURE]</strong> to add Spanish (Venezuela) language
* refactoring of checking admin users for exclution
* to fix E_WARNING on wpFastestCache.php line 1064

= 0.9.0.0 =
* to exclude the css source of elementor which is /elementor/css/post-[number].css to avoid increasing the size of minified sources
* to replace urls which have data-vc-parallax-image attribute with cdn-url
* to avoid clearing cache of feed after voting (All In One Schema.org Rich Snippets)
* to fix clearing cache after switching url on WPML

= 0.8.9.9 =
* to fix Undefined variable: count_posts in preload.php on line 112
* to update of Spanish translation
* to preload the language pages (WPML)
* to clear cache of the commend feed as well after clearing cache of a post

= 0.8.9.8 =
* to clear cache of /feed as well after clearing cache of a post
* to fix PHP Notice: Undefined index: wpfc in timeout.php on line 132
* to clear cache when a approved commens is updated
* to add swf extension for cdn
* to replace urls which have data-fullurl, data-bg, data-mobileurl and data-lazy attribute with cdn-url
* <strong>[FEATURE]</strong> Traditional Chinese language has been added
* to convert the icon from png to svg [by Roni Laukkarinen]
* to fix Undefined index: HTTP_HOST cache.php on line 321

EARLIER VERSIONS
For the changelog of earlier versions, please refer to [<a target="_blank" href="https://www.wpfastestcache.com/changelog/earlier-changelog-of-freemium-version/">the changelog on wpfastestcache.com</a>]

== Frequently Asked Questions ==

= How do I know my blog is being cached? =
You need to refresh a page twice. If a page is cached, at the bottom of the page there is a text like "&lt;!-- WP Fastest Cache file was created in 0.330816984177 seconds, on 08-01-14 9:01:35 --&gt;".

= Does it work with Nginx? =
Yes, it works with Nginx properly.

= Does it work with IIS (Windows Server) ? =
Yes, it works with IIS properly.

= Is this plugin compatible with Multisite? =
Yes, it is compatible with Multisite.

= Is this plugin compatible with Subdirectory Installation? =
Yes, it is compatible with Subdirectory Installation.

= Is this plugin compatible with Http Secure (https) ? =
Yes, it is compatible with Http Secure (https).

= Is this plugin compatible with Adsense? =
Yes, it is compatible with Adsense 100%.

= Is this plugin compatible with CloudFlare? =
Yes, it is but you need to read the details. <a href="http://www.wpfastestcache.com/tutorial/wp-fastest-cache-cloudflarecloudfront/">Click</a>

= Is this plugin compatible with qTranslate? =
Yes, it is compatible with qTranslate 100%.

= Is this plugin compatible with WP Hide & Security Enhancer? =
Yes, it is compatible with WP Hide & Security Enhancer.

= Is this plugin compatible with WP-PostViews? =
Yes, it is compatible with WP-PostViews. The current post views appear on the admin panel. The visitors cannot see the current post views. The developer of WP-PostViews needs to fix this issue.

= Is this plugin compatible with WooCommerce Themes? =
Yes, it is compatible with WooCommerce Themes 100%.


== Upgrade notice ==
....