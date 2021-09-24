=== Web from feeds ===
Contributors: mojmik
Donate link: none
Tags: custom posts,product feeds,google product feed,comission junction,cj,ajax,content,generator,content generator
Requires at least: 4.7
Tested up to: 5.8
Stable tag: 1.2
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generate whole website from product feeds in google format (or any other) used by affiliate systems like Commission Junction. Easily customize with simple tempating system. Blazing fast thanks to internal caching system.

== Description ==

Some of features this plugin brings:

*   import / bulk import even huge feed files
*   automatically generate categories with product counts
*   store products as custom posts+metas or in dedicated table (for lightning fast filtering)
*   internal caching system for even better performance
*   easy templating system for custom appearance
*   ajax powered loading more products (instead of pagination, next items are loaded when you scroll to the bottom like on Facebook)
*   ajax product filtering
*   ajax processed outside wordpress core for maximum speed
*   multi language option
*   shortcodes for displaying static/ajax content, categories and brands listing (cj)

Workflow:
*   install plugin
*   visit site setup page in administration
*   customize options and templated
*   add fields and content manually or import it from feeds
*   place shortcodes where as you like


If interested drop me a message. 

[Demo can be found here](https://www.ukea.cz/)
[or here](https://www.81gr.com/)

== Frequently Asked Questions ==

= My files are big, can I import them anyway?

*   No problem! I processed files from Comission Junction with size of almost 1GB. You can upload them with FTP to the plugin folder, and then use csv bulk import for processing. 

= How can I customize how custom posts look like? =

*   See html files in templates folder.

= Translations are available? =

*   Sure! For internal templates, internal translating engine is available. Other translations are conducted using standard wordpress tools. 

= What are the shortcodes? =

*   For showing content on a page:
[majaxstaticcontent type="cj"]

*   filter by a field mauta_cj_type:
[majaxstaticcontent type="cj" mauta_cj_type="home-living%"]

*   other filtering example: 
[majaxstaticcontent type="cj" mauta_cj_price="between;150|290" mauta_cj_type="accessories%"]

*   show text search on sidebar / widget:
[majaxsearchbox type="cj"]

*   show all categories on sidebar / widget:
[cjcategories type="cj"]

== Screenshots ==

1. creation of custom posts
2. importing feeds
3. demonstration usage on a website

== Changelog ==

= 3.1 =
*   Release to WP plugin repo.
