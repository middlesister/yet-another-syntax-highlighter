# Yet Another Syntax Highlighter #

Author: middlesister 
Tags: code, highlight, syntax highlighting 
Requires at least: 3.4 
Tested up to: 3.4 
License: GPLv2 or later 
License URI: http://www.gnu.org/licenses/gpl-2.0.html 

## Description ##

This plugin will add syntax highlighting for your code snippets using [highlight.js](http://softwaremaniacs.org/soft/highlight/en/). It is based on the [wp-highlight.js](http://wordpress.org/extend/plugins/wp-highlightjs/) plugin by Igor Kalnitsky, but updated to use the settings API.

Highlight.js is great because it works automatically: find blocks of code and highlight it. It is also compatible with  the [HTML5 recommended](http://dev.w3.org/html5/spec/single-page.html#the-code-element) markup of code. 

Usage:

Any code marked up with ´<pre><code></code></pre>´ will be recognized and highlighted. Highlight.js will try to automatically detect the language, but if you want you can specify the language manually with a css class ´<pre><code class="ruby"></code></pre>´

There is also a shortcode available, `[code][/code]` with an optional parameter for language `[code lang=ruby][/code]`

The bundled highlight.js is version 7.3 and includes support for:
Appache, Applescript, bash, C#, C++, CSS, Diff, HTML/XML, HTTP, Ini, JSON, Java, Javascript, Markdown, Nginx, PHP, Perl, Python, Ruby, SQL

NOTE: Since version 7.3, highlight,js no longer works in IE8 and older. Your code will degrade gracefully to display with the default styles of your wordpress theme.


## Installation ##

1. Upload the `plugin` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings>Yet Another Syntax Highlighter and select the style you want to use for your syntax highlighting 

## Changelog ##

### 0.2 ###
* Change plugin name and slug because of name conflict with other plugin on wordpress.org

### 0.1 ###
* Initial release.