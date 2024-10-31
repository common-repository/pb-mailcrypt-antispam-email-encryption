=== PB MailCrypt - AntiSpam Email Encryption ===
Contributors: pascalbajorat
Donate link: https://www.bajorat-media.com/
Tags: shortcode, security, protection, email, encryption, spam, antispam, post, posts, page, admin, links, shortcode, email-encryption
Requires at least: 4.0
Tested up to: 6.1
Stable tag: 3.1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This Plugin provides functions for an automatic email encryption and protection against spam.

== Description ==

This plugin provides a function for automatic email encryption and a shortcode for manual settings. It will help you to protect every email address on your website against spam.

Simply enable the automatic protection mode and you are safe.

> #### MailCrypt Shortcode:
> `[mailcrypt email="name@example.com" href="Alternative Contact Page URL" title="A-Tag title  Attribute"]`
>
> - email = required
> - href = optional
> - title = optional
>
> Change the link text with an enclosing shortcode:
> `[mailcrypt email="name@example.com" href="Alternative URL"]link text[/mailcrypt]`

You can also use a shorter variant of [mailcrypt] -> [mail]

It's also possible to use PB MailCrypt in an automatic mode, you can enable this feature under "settings > general".

**New:** Now supports Advanced Custom Fields.

If you have any questions or problems, you can ask me: [Pascal Bajorat - Webdesigner / WordPress / WebDeveloper](https://www.bajorat-media.com/ "Pascal Bajorat - Webdesigner / WordPress / WebDeveloper Berlin")

== Installation ==

1.	Upload the full directory to wp-content/plugins
2.	Activate plugin Tags Page in plugins administration
3.	Now, you can use the shortcode [mailcrypt email="name@example.com"]
4.	Enable the automatic mode under "settings > general"

== Changelog ==

= 1.0 =
* Initial release.

= 1.0.1 =
* Only a few changes in descriptions and links

= 1.0.2 =
* WordPress 4.0 Update

= 2.0.0 =
* Added a Settings-Panel
* Plugin will now automatically encrypt mail addresses (if enabled)
* General improvements

= 2.1.0 =
* Shortcode-Update: Supports now enclosing and self-closing shortcodes
* Changed Text Domain

= 2.1.1 =
* Bug fix

= 2.2.0 =
* Bug fix
* Exclude list: Now you can exclude page or post IDs from the auto-mode

= 2.3.0 =
* The automatic mode now supports Advanced Custom Fields textarea and wysiwyg editor

= 2.3.1 =
* Bugfix for ACF integration

= 3.0.0 =
* New settings design
* Bugfix and optimization

= 3.0.1 =
* Bugfix and optimization

= 3.1.0 =
* Bugfix and optimization

== License ==

GNU General Public License v.3 - http://www.gnu.org/licenses/gpl-3.0.html
