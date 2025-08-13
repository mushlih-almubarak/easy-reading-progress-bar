=== Easy Reading Progress Bar ===
Contributors: mushlih
Tags: animation, progress bar, reading time, estimated reading time, read time
Requires at least: 5.8
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://buymeacoffee.com/mushlih

A simple, lightweight, and highly compatible reading progress bar for your WordPress posts.

== Description ==

Easy Reading Progress Bar adds a sleek, customizable progress bar to your single post pages, providing readers with a visual indicator of their progress as they scroll through an article. It's designed to be feather-light, incredibly fast, and compatible with the vast majority of WordPress themes, including modern Block Themes and popular classic themes.

**Key Features:**

* **Extremely Lightweight:** No extra CSS or JavaScript files loaded. All code is inlined and optimized for performance.
* **Highly Compatible:** Works seamlessly with many themes.
* **Customizable:** Easily change the progress bar's color and position (top or bottom of the page) from a simple settings page.
* **Dependency-Free:** The frontend script does not rely on jQuery, ensuring it won't conflict with other plugins or slow down your site.
* **Developer Friendly:** Includes a filter (`erpb_bar_height`) to programmatically change the bar's height.

== Installation ==

Installing the plugin is simple!

**From your WordPress Dashboard:**

1.  Navigate to 'Plugins' > 'Add New'.
2.  Search for 'Easy Reading Progress Bar'.
3.  Click 'Install Now' and then 'Activate'.
4.  Go to 'Settings' > 'Reading Progress Bar' to customize the options. That's it!

**Manual Installation:**

1.  Download the plugin zip file from WordPress.org.
2.  Navigate to 'Plugins' > 'Add New' in your WordPress dashboard.
3.  Click 'Upload Plugin' and select the zip file you downloaded.
4.  Activate the plugin.
5.  Go to 'Settings' > 'Reading Progress Bar' to customize.

== Frequently Asked Questions ==

= Will this plugin slow down my site? =

Absolutely not. Performance is a core feature. The plugin only loads its tiny, optimized code on single post pages and does not enqueue any extra CSS or JS files, keeping your site fast.

= Does this work with my theme? =

Yes, most likely! The plugin was built for high compatibility. It intelligently detects the main content area in classic and modern block themes, and a wide range of other theme structures.

= Can I change the height of the bar? =

Yes. While there is no setting for it in the settings page to keep things simple, developers can use a WordPress filter to change the height. The default is 7px.

Example code to add to your theme's `functions.php` file to change the height to 10px:
`add_filter( 'erpb_bar_height', function() { return '10'; } );`

== Changelog ==

= 1.0.0 =
* Initial public release.
* Features an easy, simple, lightweight, dependency-free reading progress bar for single posts.
* Includes a settings page to customize bar color and position (top or bottom).
* Designed for high compatibility with modern block themes and popular classic themes.
