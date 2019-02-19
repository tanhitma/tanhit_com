=== BBSpoiler ===
Contributors: Flector
Donate link: http://goo.gl/CcxWYg
Tags: box, boxes, collapse, expand, hide, hidden, jquery, faq, faqs, shortcode, content, spoiler, spoilers, bbpress, post, posts, admin, animated, animation, plugin, javascript, answers, questions, knowledgebase, shortcodes, colored, color, accordion, accordions, page, pages, collapsible, display, expandable, slider, slide, media
Requires at least: 3.9
Tested up to: 5.0.1
Stable tag: trunk

This plugin allows you to hide text under the tags [spoiler]your text[/spoiler].

== Description ==

You can use this plugin to hide part of the text of a post in a nicely-formatted container that will becomes unhidden when clicked on. The plugin can be useful for creating FAQ pages, hiding large pictures, and things like that.

The plugin creates its own "Spoiler" button in the visual editor, but you can also add spoilers directly using tags. For example:

`[spoiler title='Title']Spoiler content[/spoiler]`

or

`[spoiler title='Title' collapse_link='no']Spoiler content[/spoiler]`

If you liked my plugin, please <strong>rate</strong> it.
 

== Installation ==

1. Upload <strong>bbspoiler</strong> folder to the <strong>/wp-content/plugins/</strong> directory.
2. Activate the plugin through the <strong>Plugins</strong> menu in WordPress.
3. That's all.

You can find the "Spoiler" plugin button in the visual editor.

== Frequently Asked Questions ==

= How do I indent paragraphs in spoiler text? =

This spoiler code gives you three paragraphs of text:

`
[spoiler title='Title' collapse_link='true']First Paragraph

Second Paragraph

Third Paragraph[/spoiler]
`

= Can I use spoilers within spoilers? =

Yes, but only up to two levels. Use the number 2 in the shortcode. 
The code should look like this:

`
[spoiler title='Parent']

[spoiler2 title='Child 1']text[/spoiler2]
[spoiler2 title='Child 2']text[/spoiler2]

[/spoiler]
`

= Does the plugin support localization? =

Yes, please use [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/bbspoiler).


== Screenshots ==

1. "Spoiler" button and plugin dialog window.
2. Sample spoiler.
3. Sample spoiler with pictures.
4. Second-level spoilers within a primary spoiler.
5. Spoiler in bbPress topic.
6. All color styles.

== Changelog ==

= 2.01 =
* fixed "ReferenceError" bug.
* removed the bundled languages in favour of language packs from translate.wordpress.org

= 2.00 =
* added 10 different color styles

= 1.01 =
* added support for bbPress

= 1.00 =
* first version
