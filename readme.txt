=== Options Inspector ===
Contributors: Charles
Donate link: http://sexywp.com/plugin-options-inspector.htm
Tags: options, management, admin, developer, tools
Requires at least: 2.7
Tested up to: 2.5.1
Stable tag: 1.0.2

Options Inspector is a tool with which you can easily view all the options in your database, even its data is serialized, and alter exactly a certain part of option value.

== Description ==

Options Inspector is a tool with which you can list all the options in your database, view a certain one in detail, even its data is serialized, and alter exactly a certain part of option value. It is mainly designed for plugin developers and theme designers.

When I am debugging a plugin, I always want to konw, whether the options in this plugin are saved exactly or not. Usually, I add var_dump statement in my source code to print the options out. Everything looks good, but when I finished my job, it bothered me a lot to remove this debug statements. What annoyed me even more is that when I change my mind and changed the structure of the option, I must use additional statement to alter the option or directly use SQL in phpMyAdmin. Finally, I created this tool to assist the plugin development.

Features:

    * List all options order by option_id.
    * Search option through keyword.
    * View unsierialized value of options.
    * Modify option use PHP code.

== Installation ==

1. Upload `options-inspector` directory to the `/wp-content/plugins/` directory

2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Do you have any question? =

If you have one, please send me a email.

== Other Notes ==

None.

== Screenshots ==

1. This screenshot is the the admin view of the plugin.
