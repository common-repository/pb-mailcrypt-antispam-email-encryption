<?php
/* Security-Check */
if ( !class_exists('WP') ) {
    die();
}

if( !class_exists('pbMailCryptSettings') ):

    class pbMailCryptSettings extends pbMailCrypt
    {

        public static $settings = false;

        public static function addSettings()
        {
            add_action('admin_menu', array(__CLASS__, 'optionsPageMenu'));
            add_action('admin_init', array(__CLASS__, 'initSettings'));

            add_filter('plugin_action_links_'.parent::$basename, array(__CLASS__, 'settingsLink'));

            $showUpgradeBanner = get_option('pbmc_upgrade_notice');

            if( $showUpgradeBanner != self::$verMajor && self::getArrayKey('page', $_GET) != 'pb-mailcrypt' ) {
                add_action( 'admin_notices', array(__CLASS__, 'adminUpgradeNotice') );
            }
        }

        public static function adminUpgradeNotice()
        {
            $current_user = wp_get_current_user();

            $username = ((!empty($current_user->user_firstname)) ? $current_user->user_firstname : $current_user->user_login );

            echo '<div class="notice pb-custom-message mailcrypt" style="max-width: 100%;"><p>';
            echo sprintf(
                __('<strong>Hey %s</strong>, we have updated <a href="%s">Mailcrypt</a>. Visit the <a href="%s">plugin settings page</a> to get deeper insights about the new features. This message will disappear automatically after you\'ve visited the plugin settings.', 'pb-mailcrypt-antispam-email-encryption'),
                $username,
                admin_url('options-general.php?page=pb-mailcrypt'),
                admin_url('options-general.php?page=pb-mailcrypt')
            );
            echo '</p><a href="'.admin_url('options-general.php?page=pb-mailcrypt').'" class="pb-btn">'.__('Close', 'pb-mailcrypt-antispam-email-encryption').'</a></div>';
        }

        public static function settingsLink( $data )
        {
            if( ! current_user_can('manage_options') ) {
                return $data;
            }

            $data = array_merge(
                $data,
                array(
                    sprintf(
                        '<a href="%s">%s</a>',
                        add_query_arg(
                            array(),
                            admin_url('options-general.php?page=pb-mailcrypt')
                        ),
                        __('Settings', 'pb-mailcrypt-antispam-email-encryption')
                    )
                )
            );

            return $data;
        }

        public static function initSettings()
        {
            self::$settings = new pbSettingsFramework(array(
                'text-domain' => 'pb-mailcrypt-antispam-email-encryption',
                'page' => 'pb-mailcrypt-antispam-email-encryption',
                'section' => 'pb-mailcrypt-antispam-email-encryption',
                'option-group' => 'pb-mailcrypt-antispam-email-encryption'
            ));

            // register a new setting for "wporg" page
            register_setting('pb-mailcrypt-antispam-email-encryption', 'pbmc_options');

            self::$settings->addSettingsSection(
                'pb-mailcrypt-antispam-email-encryption',
                __('Settings', 'pb-mailcrypt-antispam-email-encryption'),
                function(){
                    echo '<div class="pb-section-wrap" style="margin-bottom: 2px">';
                    echo '<p><strong>'.__('Here you can enable the automatic email encryption mode for PB MailCrypt. If you have problems with the automatic email encryption or  special cases that not be auto detected you can use the following shortcode:', 'pb-mailcrypt-antispam-email-encryption').'</strong></p>';
                    echo '</div>';

                    echo '<div class="pb-custom-message code" style="margin-bottom: 2px"><p>';
                    echo '<code>[mailcrypt email="name@example.com" href="'.__('Alternative Contact-Page URL', 'pb-mailcrypt-antispam-email-encryption').'" title="'.__('A-Tag title attribute', 'pb-mailcrypt-antispam-email-encryption').'"]</code>';
                    echo '<code>[mailcrypt email="name@example.com" href="'.__('Alternative Contact-Page URL', 'pb-mailcrypt-antispam-email-encryption').'" title="'.__('A-Tag title attribute', 'pb-mailcrypt-antispam-email-encryption').'"] '.__('link text', 'pb-mailcrypt-antispam-email-encryption').' [/mailcrypt]</code>';
                    echo '</p></div>';

                    echo '<div class="pb-section-wrap">';
                    echo '<p>'.sprintf(
                            __('PB MailCrypt is a free WordPress Plugin by <a href="%s" target="_blank">Pascal Bajorat</a> and made with %s in Berlin, Germany. If you like it and maybe want to <a href="%s" target="_blank">buy me a cup of coffee or a beer</a> I would appreciate that very much.', 'pb-mailcrypt-antispam-email-encryption'),
                            'https://www.pascal-bajorat.com',
                            '<span style="color: #f00;">&#9829;</span>',
                            'https://www.pascal-bajorat.com/spenden/'
                        ).'</p>';
                    echo '</div>';

                }
            );

            self::$settings->addSettingsField(
                self::setting_pb_mailcrypt_auto_mode,
                __('Auto mode', 'pb-mailcrypt-antispam-email-encryption'),
                array(
                    'type' => 'checkbox',
                    'default' => '',
                    'desc' => __('enable automatic email encryption', 'pb-mailcrypt-antispam-email-encryption')
                )
            );

            self::$settings->addSettingsField(
                self::setting_pb_mailcrypt_link,
                __('Link or Page-ID', 'pb-mailcrypt-antispam-email-encryption'),
                array(
                    'type' => 'text',
                    'default' => '/',
                    'desc' => __('Alternative Contact-Page URL', 'pb-mailcrypt-antispam-email-encryption')
                )
            );

            self::$settings->addSettingsField(
                self::setting_pb_mailcrypt_exclude,
                __('Exclude from auto mode', 'pb-mailcrypt-antispam-email-encryption'),
                array(
                    'type' => 'text',
                    'default' => '',
                    'desc' => __('Page or Post IDs that will be excluded from auto mode, e.g. 1, 2, 3', 'pb-mailcrypt-antispam-email-encryption')
                )
            );

        }

        public static function optionsPageMenu()
        {
            add_submenu_page(
                'options-general.php',
                __('PB MailCrypt - AntiSpam E-Mail Encryption', 'pb-mailcrypt-antispam-email-encryption'),
                __('MailCrypt', 'pb-mailcrypt-antispam-email-encryption'),
                'manage_options',
                'pb-mailcrypt',
                array(__CLASS__, 'optionsPage')
            );
        }

        public static function optionsPage()
        {
            if (!current_user_can('manage_options')) {
                return;
            }

            update_option( 'pbmc_upgrade_notice', self::$verMajor, false );
            ?>
            <div class="wrap pb-wp-app-wrapper">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

                <div class="pb-wrapper">
                    <div class="pb-main">
                        <form action="<?php echo admin_url('options.php') ?>" method="post" target="_self">
                            <?php
                            settings_fields('pb-mailcrypt-antispam-email-encryption');
                            pbSettingsFramework::doSettingsSections('pb-mailcrypt-antispam-email-encryption');
                            submit_button();
                            ?>
                        </form>
                    </div>
                    <div class="pb-sidebar">
                        <h3><?php esc_html_e('Plugins & Support', 'pb-mailcrypt-antispam-email-encryption') ?></h3>

	                    <?php if( strstr(get_locale(), 'de') ): ?>
                            <div class="pb-support-box">
                                <h4><?php _e('WordPress Kurs', 'pb-mailcrypt-antispam-email-encryption') ?></h4>
                                <p><?php _e('Möchtest du mit WordPress richtig durchstarten? In meinem WordPress Kurs erfährst du spannende Tipps und Tricks zu WordPress und SEO!', 'pb-mailcrypt-antispam-email-encryption') ?></p>

                                <p>
                                    <a href="https://wordpress-kurs.pascal-bajorat.com/" class="button" target="_blank"><?php _e('Jetzt Kurs ansehen', 'pb-mailcrypt-antispam-email-encryption') ?></a>
                                </p>
                            </div>
	                    <?php endif; ?>

                        <div class="pb-plugin-box">
                            <h4>
                                <span class="icon">
                                    <img src="<?php echo plugins_url( 'img/seo-friendly-images.png', constant('pbmc_file') ); ?>" >
                                </span>
                                <span class="text"><?php _e('PB SEO Friendly Images', 'pb-mailcrypt-antispam-email-encryption') ?></span>
                            </h4>
                            <div class="desc">
                                <p><?php _e('This plugin is a full-featured solution for SEO friendly images. Optimize "alt" and "title" attributes for all images and post thumbnails. This plugin helps you to improve your traffic from search engines.', 'pb-mailcrypt-antispam-email-encryption') ?></p>
                                <p><a href="<?php echo admin_url('plugin-install.php?s=PB+SEO+Friendly+Images&tab=search&type=term') ?>" class="button"><?php _e('Install Plugin', 'pb-mailcrypt-antispam-email-encryption') ?></a></p>
                            </div>
                        </div>
                        <div class="pb-plugin-box">
                            <h4>
                                <span class="icon">
                                    <img src="<?php echo plugins_url( 'img/primusnote.png', constant('pbsfi_file') ); ?>" alt="<?php _e('PrimusNote', 'pb-mailcrypt-antispam-email-encryption') ?>" />
                                </span>
                                <span class="text"><?php _e('PrimusNote', 'pb-mailcrypt-antispam-email-encryption') ?><br /><?php _e('Project Management', 'pb-mailcrypt-antispam-email-encryption') ?></span>
                            </h4>
                            <div class="desc">
                                <p><?php _e('PrimusNote is a Project Management and Team Collaboration software based on WordPress.', 'pb-mailcrypt-antispam-email-encryption') ?></p>
                                <p><a href="https://goo.gl/D9P49K" target="_blank" class="button"><?php _e('Install Plugin', 'pb-mailcrypt-antispam-email-encryption') ?></a></p>
                            </div>
                        </div>

                        <div class="pb-support-box">
                            <h4><?php _e('Support', 'pb-mailcrypt-antispam-email-encryption') ?></h4>
                            <p><?php _e('Do you need some help with this plugin? I am here to help you. Get in touch:', 'pb-mailcrypt-antispam-email-encryption') ?></p>
                            
                            <p><a href="https://wordpress.org/support/plugin/pb-mailcrypt-antispam-email-encryption" class="button" target="_blank"><?php _e('Support Forum', 'pb-mailcrypt-antispam-email-encryption') ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }


    }

endif; // class_exists