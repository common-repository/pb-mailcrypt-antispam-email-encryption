<?php
/*
Plugin Name: PB MailCrypt - AntiSpam Email Encryption
Plugin URI: https://wordpress.org/plugins/pb-mailcrypt-antispam-email-encryption/
Description: This Plugin provides functions for an automatic email encryption and protection against spam.
Version: 3.1.0
Author: Pascal Bajorat
Author URI: https://www.pascal-bajorat.com
Text Domain: pb-mailcrypt-antispam-email-encryption
Domain Path: /lang
License: GNU General Public License v.3

Copyright (c) 2018 by Pascal Bajorat.
*/

/* Security-Check */
if ( !class_exists('WP') ) {
    die();
}

if( ! defined('pbmc_file') ) {
    define('pbmc_file', __FILE__);
}

if( ! defined('pbmc_plugin_path') ) {
    define('pbmc_plugin_path', plugin_dir_path(__FILE__));
}

require_once 'inc'.DIRECTORY_SEPARATOR.'pbSettingsFramework.php';

class pbMailCrypt
{
    public static $verMajor = '3.1';
    public static $verMinor = '0';

    public static $basename = false;

	const linkClass = 'mailcrypt';
	const replaceChar = '∂';
	const emailPattern = '/[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/';
	const emailPattern2 = '/>[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])<?/';
	const emailPatternWithLink = '/<a[^>]+>[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])<\/a>?/';

	const setting_pb_mailcrypt_auto_mode = 'pb_mailcrypt_auto_mode';
	const setting_pb_mailcrypt_link = 'pb_mailcrypt_link';
	const setting_pb_mailcrypt_exclude = 'pb_mailcrypt_exclude';

	/**
	 * pbMailCrypt constructor.
	 *
	 * @since 1.0.0
	 */
	public static function init()
	{
        self::$basename = plugin_basename(__FILE__);

		/*
		 * Load language files
		 */
		load_plugin_textdomain('pb-mailcrypt-antispam-email-encryption', false, dirname(plugin_basename( __FILE__ )).'/lang/');

		/*
		 * Add JavaScript files
		 */
		add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_script'));

		/*
		 * Shortcodes
		 */
		add_shortcode('mailcrypt', array(__CLASS__, 'mailcrypt'));
		add_shortcode('mail', array(__CLASS__, 'mailcrypt'));

		/*
		 * Filter
		 */
		add_filter('the_content', array(__CLASS__, 'encrypt_mails_in_content'), 20);
		//add_filter('plugin_action_links_'.plugin_basename(__FILE__), array(__CLASS__, 'settings_link'));

		/*
		 * Support for AdvancedCustomFields
		 */
		if( ! is_admin() ) {
			add_filter('acf/load_value/type=textarea', array(__CLASS__, 'encrypt_mails_in_content'), 20);
			add_filter('acf/load_value/type=wysiwyg', array(__CLASS__, 'encrypt_mails_in_content'), 20);

			add_filter('acf_load_value-textarea', array(__CLASS__, 'encrypt_mails_in_content'), 20);
			add_filter('acf_load_value-wysiwyg', array(__CLASS__, 'encrypt_mails_in_content'), 20);
		} else {
            add_action( 'admin_enqueue_scripts', function(){
                wp_register_style(
                    'pbmc-admin-css',
                    plugins_url(dirname(self::$basename)).'/css/admin.css',
                    false,
                    self::$verMajor.'.'.self::$verMinor
                );
                wp_enqueue_style( 'pbmc-admin-css' );
            } );
        }

		/*
		 * Settings
		 */
		//add_action('admin_init' , array(__CLASS__, 'register_fields'));
	}

	/**
	 * wp_enqueue_scripts action
	 *
	 * @since 1.0.0
	 */
	public static function enqueue_script()
	{
        wp_register_script('pbMailCrypt', plugins_url('mailcrypt.js', __FILE__), array('jquery'), '1.0.1', true);

		wp_enqueue_script('jquery');
		wp_enqueue_script('pbMailCrypt');
	}

	/**
	 * Mailcrypt Shortcode
	 *
	 * @param array $atts
	 * @since 1.0.0
	 * @return string
	 */
	public static function mailcrypt( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'email' => false,
			'href' => false,
			'title' => false
		), $atts ) );

		/** @var string|boolean $href */
		$hrefOption = get_option(self::setting_pb_mailcrypt_link);
		if( !empty($hrefOption) && $href == false ) {
			$href = $hrefOption;
		} elseif( $href == false ) {
			$href = '#';
		}

		if( is_numeric($href) ) {
			$href = get_permalink($href);

			if( empty($href) ) {
				$href = home_url();
			}
		}

		/** @var string $email */
		$emailSplit = @explode('@', $email);

		if( !empty($email) && is_array($emailSplit) ) {
			/** @var string|boolean $title */

			if( $content !== null && !empty($content) ) {
				$email = '<a href="' . esc_url($href) . '" ' . (( $title != false)? 'title="' . $title . '"':'') . ' class="' . self::linkClass . '"><span class="mc-inner-hidden" style="display:none;">' . $emailSplit[0] . '<span><span>' . self::replaceChar . '</span></span>' . $emailSplit[1] . '</span>'.$content.'</a>';
			} else {
				$email = '<a href="' . esc_url($href) . '" ' . (( $title != false)? 'title="' . $title . '"':'') . ' class="' . self::linkClass . '">' . $emailSplit[0] . '<span><span>' . self::replaceChar . '</span></span>' . $emailSplit[1] . '</a>';
			}
		}

		return $email;
	}

	/**
	 * Function to encrypt email addresses in the_content
	 *
	 * @param $email
	 * @since 2.0.0
	 * @return string
	 */
	public static function mailcrypt_content( $email )
	{
		$match = preg_match(self::emailPattern2, $email[0], $matches);

		if( $match ) {
			$email[0] = trim($matches[0], '><');
		}

		return self::mailcrypt(array(
			'email' => $email[0]
		));
	}

	/**
	 * Content Filter
	 *
	 * @param $content
	 * @since 2.0.0
	 * @return mixed
	 */
	public static function encrypt_mails_in_content( $content )
	{
		$excludeOpt = trim(get_option(self::setting_pb_mailcrypt_exclude));

		if( !empty($excludeOpt) ) {

			if( strstr($excludeOpt, ',') ) {
				$excludes = explode(',', $excludeOpt);
				$excludes = array_map('trim', $excludes);
			} else {
				$excludes = array($excludeOpt);
			}

			if( in_array(get_the_ID(), $excludes) ) {
				return $content;
			}
		}

		if( get_option(self::setting_pb_mailcrypt_auto_mode) == true ) {
			$content = preg_replace_callback(self::emailPatternWithLink, array(__CLASS__, 'mailcrypt_content'), $content);
			$content = preg_replace_callback(self::emailPattern, array(__CLASS__, 'mailcrypt_content'), $content);
		}

		return $content;
	}

    /**
     * get array key
     *
     * @param $key
     * @param $array
     * @return bool
     */
    public static function getArrayKey($key, $array)
    {
        if( array_key_exists($key, $array) ) {
            return $array[$key];
        } else {
            return false;
        }
    }

	/**
	 * Uninstall PB MailCrypt
	 * @since 2.0.0
	 */
	public static function uninstall()
	{
		/* Global */
		/** @var object $wpdb */
		global $wpdb;

		/* Remove settings */
		delete_option(self::setting_pb_mailcrypt_auto_mode);
		delete_option(self::setting_pb_mailcrypt_link);
		delete_option(self::setting_pb_mailcrypt_exclude);

		/* Clean DB */
		$wpdb->query("OPTIMIZE TABLE `" .$wpdb->options. "`");
	}
}

require_once 'inc'.DIRECTORY_SEPARATOR.'settings.php';

add_action(
	'plugins_loaded',
	array(
		'pbMailCrypt',
		'init'
	)
);

add_action(
    'plugins_loaded',
    array(
        'pbMailCryptSettings',
        'addSettings'
    )
);

register_uninstall_hook(
	__FILE__,
	array(
		'pbMailCrypt',
		'uninstall'
	)
);
