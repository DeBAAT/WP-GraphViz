=== Plugin Name ===
Contributors: DeBAAT
Donate link: http://www.de-baat.nl/WP_Graphviz
Tags: graphviz, network, diagram, graph, dot, neato, twopi, circo, fdp, visualisation, visualization, layout, hierarchical
Requires at least: 3.6
Tested up to: 3.6
Stable tag: 0.1.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to provide GraphViz functionality for WordPress sites.

== Description ==

[GraphViz](http://www.graphviz.org/) is a powerful tool for visualising network and tree structures that connect objects.

This WordPress plugin provides a shortcode mechanism to create GraphViz graphics within blogs, using the shortcode mechanism.

It's working is based on the viz.js code as provided by Mike Daines:

	https://github.com/mdaines/viz.js

Special thanks goes to chrisy as author of TFO Graphviz, e.g. for providing the inspiration for this readme:

	http://wordpress.org/plugins/tfo-graphviz/

== Installation ==


1. Upload `wp-graphviz.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use shortcode `[wp_graphviz]<dot code>[/wp_graphviz]` in your posts or pages

== Frequently Asked Questions ==

= What is GraphViz? =

[GraphViz](http://www.graphviz.org/) is a way of generating visualisations of structural relationships between objects.
Almost any kind of diagram where something _connects_ to something else can be drawn and automatically laid out using the DOT language.

= How do I use this plugin? =

Use the `[wp_graphviz]` shortcode. Various uses are explained in the "_How to use_" section.

= How do I write DOT? =

The online documentation for [GraphViz](http://www.graphviz.org/) is terse and not especially helpful, 
in particular the [DOT language](http://www.graphviz.org/doc/info/lang.html) page is only helpful if you happen to be able to read an approximation of [BNF](http://en.wikipedia.org/wiki/Backus%E2%80%93Naur_Form).

There are however several other introductions to Graphviz and DOT, including [an excerpt on the O'Reilly LinuxDevCenter.com site](http://linuxdevcenter.com/pub/a/linux/2004/05/06/graphviz_dot.html). 
Another approach would be to look at the examples in the [Graphviz gallery](http://www.graphviz.org/Gallery.php).

If in doubt, find an example and experiment with it.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 0.1.0 =
* First version starting the plugin.

== Upgrade Notice ==

= 0.1.0 =
As this is the first version, there is no upgrade info yet.


== How to use WP GraphViz ==

The shortcode syntax is:

`
[wp_graphviz <options>]
 <DOT code>
[/wp_graphviz]
`

Where `<options>` is anything from this list. All are entirely optional:

* `id="`*&lt;id&gt;*`"`

  Provides the identifier used to link the generated image to an image map. If you use the `simple` option then it also
  provides the name of the generated DOT graph container (since GraphViz uses this to generate the image map). If not given
  then an identifier is generated with the form `wp_graphviz_N` where *N* is an integer that starts at one when the plugin
  is loaded and is incremented with use.

* `output="<png|gif|jpg|svg>"`

  Indicates the desired image format. Defaults to `png`.

* `simple="yes|no"`

  The `simple` option provides a very basic DOT wrapper around your code such that the following is possible:
 
  `
  [wp_graphviz simple="yes"] a -> b -> c; [/wp_graphviz]
  `

  The generated code would look like:

  `
  digraph wp_graphviz_1 {
      a -> b -> c;
  }
  `

  See the `id` option for a description of where the name of the `digraph` comes from. `simple` defaults to `no`.

* `title="`*&lt;title&gt;*`"`

  Indicates the title of the image. This is used in the `alt` and `title` attributes of the image reference. This
  defaults to an empty string. Note that image maps may indicate a `title` string which will appear in tool-tips.
