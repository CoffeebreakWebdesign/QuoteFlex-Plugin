=== QuoteFlex ===
Contributors: (your-username)
Tags: quotes, random quotes, quote sets, inspirational, wisdom, motivational
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Flexible quote management for WordPress - search, import, organize and display inspiring quotes with quote sets.

== Description ==

QuoteFlex is a powerful yet easy-to-use WordPress plugin that lets you display beautiful random quotes on your website. Search thousands of quotes from free APIs, organize them into quote sets, and display them anywhere with shortcodes, widgets, or Gutenberg blocks.

= Key Features =

* 🔍 **API Search & Import** - Search external quote APIs and selectively import quotes
* 📦 **Quote Sets** - Create curated collections for different pages (e.g., homepage quotes, leadership quotes)
* ✨ **Duplicate Prevention** - Visual indicators prevent re-importing existing quotes
* ✏️ **Manual Entry** - Add your own custom quotes when API doesn't have what you need
* 🎨 **Multiple Templates** - Choose from minimal, boxed, or card display styles
* ⚡ **AJAX Refresh** - Let visitors load new quotes without page reload
* 🧩 **Gutenberg Block** - Modern block editor integration
* 📱 **Fully Responsive** - Looks great on all devices
* 🎯 **100% Free** - All features, no premium upsells

= Perfect For =

* Bloggers who want to add inspiring quotes to their posts
* Motivational websites needing fresh content
* Business sites displaying leadership wisdom
* Educational sites sharing knowledge
* Any website that wants to engage visitors with quotes

= How Quote Sets Work =

Quote Sets let you organize your quotes into themed collections. For example:

* Create a "Homepage Inspiration" set for your homepage
* Create a "Leadership" set for your business blog
* Create a "Writing Tips" set for your author website

Then display specific sets on different pages using shortcodes:
`[quoteflex set="homepage-inspiration"]`

Each quote can belong to multiple sets, giving you maximum flexibility.

= Display Options =

**Shortcode:**
`[quoteflex]` - Display random quote
`[quoteflex set="leadership"]` - Display from specific set
`[quoteflex template="boxed"]` - Use boxed template
`[quoteflex show_author="no"]` - Hide author name

**Gutenberg Block:**
Add the "QuoteFlex Quote" block from the block inserter with visual controls.

**Widget:**
Add "QuoteFlex Random Quote" widget to any sidebar.

**Template Function:**
`<?php quoteflex_display_quote( array( 'set' => 'homepage' ) ); ?>`

= Templates =

* **Default** - Clean, simple design with left border
* **Boxed** - Elegant box with floating quotation mark
* **Card** - Modern card layout with footer
* **Minimal** - Compact design perfect for sidebars

= API Integration =

QuoteFlex integrates with Quotable.io, a free API with thousands of inspiring quotes. Search by keyword, author, or topic, then import the quotes you want. The plugin automatically detects duplicates so you never import the same quote twice.

= Privacy & Data =

QuoteFlex does not collect or transmit any user data. All quotes are stored locally in your WordPress database. API searches are cached for performance but contain no personal information.

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Go to Plugins → Add New
3. Search for "QuoteFlex"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin zip file
2. Log in to your WordPress admin panel
3. Go to Plugins → Add New → Upload Plugin
4. Choose the zip file and click "Install Now"
5. Activate the plugin

= Getting Started =

1. Go to **QuoteFlex → Search & Import** to add your first quotes
2. Create quote sets in **QuoteFlex → Quote Sets**
3. Display quotes using shortcode: `[quoteflex set="your-set-name"]`
4. Or add the QuoteFlex block in the block editor
5. Or add the QuoteFlex widget to your sidebar

== Frequently Asked Questions ==

= How do I display quotes on my website? =

Use the shortcode `[quoteflex]` in any post, page, or widget. You can also use the QuoteFlex Gutenberg block or the QuoteFlex widget in your sidebars.

= Can I create different quote collections for different pages? =

Yes! Use Quote Sets to create curated collections. For example, create a "Homepage" set for your homepage and a "Blog" set for your blog posts. Then use `[quoteflex set="homepage"]` to display quotes from that specific set.

= What quote APIs does QuoteFlex support? =

Currently, QuoteFlex uses Quotable.io, a free quote API with thousands of quotes from famous authors. More APIs may be added in future versions.

= Can I add my own quotes? =

Absolutely! Use the "Add New Quote" page to manually enter quotes that aren't in the API.

= Will this slow down my website? =

No. QuoteFlex stores all quotes locally in your WordPress database and uses efficient queries. API responses are cached for one hour to minimize external requests.

= Can I customize the quote display? =

Yes! You can choose from 4 built-in templates (default, boxed, card, minimal) or create your own template by adding a file to your theme folder: `your-theme/quoteflex/quote-custom.php`

= How do I remove all plugin data? =

Go to QuoteFlex → Settings → Advanced and check "Delete all data when plugin is uninstalled". Then when you uninstall the plugin, all quotes, sets, and settings will be permanently deleted.

= Does this plugin work with page builders? =

Yes! You can use the shortcode in any page builder that supports WordPress shortcodes. The Gutenberg block works in the WordPress block editor.

= Can quotes belong to multiple sets? =

Yes! When editing a quote, you can assign it to as many sets as you want. This gives you maximum flexibility in organizing your quotes.

= Is there support available? =

This plugin is provided as-is without active support. However, you can find documentation at quoteflex.io and community support may be available through the WordPress.org support forums.

== Screenshots ==

1. Dashboard - Overview of your quotes and sets with statistics
2. Search & Import - Search Quotable.io API and import quotes (duplicates highlighted)
3. All Quotes - Manage your quote collection with filtering and bulk actions
4. Quote Sets - Create curated collections for different pages
5. Add New Quote - Simple form to manually add quotes
6. Frontend Display - Beautiful quote display on your website (boxed template)
7. Gutenberg Block - Easy integration with block editor controls
8. Settings Page - Configure API, display options, and defaults

== Changelog ==

= 1.0.0 =
* Initial release
* API search and import from Quotable.io
* Quote sets functionality for organizing quotes
* Multiple display templates (default, boxed, card, minimal)
* Gutenberg block integration
* Sidebar widget support
* AJAX refresh button for loading new quotes
* Duplicate detection prevents re-importing
* Responsive design
* Full localization support
* Security: Nonce verification, input sanitization, output escaping
* Performance: Database query optimization, API caching

== Upgrade Notice ==

= 1.0.0 =
Initial release of QuoteFlex! Start managing and displaying inspiring quotes on your WordPress site.

== Support ==

For documentation and tutorials, visit [quoteflex.io](https://quoteflex.io)

**Note:** This plugin is provided as-is without active support or warranty. Use at your own risk.

== Credits ==

* Quotable.io API for providing free access to thousands of quotes
* WordPress community for excellent documentation and standards
