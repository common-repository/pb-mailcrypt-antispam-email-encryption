<?php

/* Security-Check */
if ( !class_exists('WP') ) {
    die();
}

if( ! class_exists('pbSettingsFramework') ):
    class pbSettingsFramework
    {
        const version = '1.1.0';
        static $textDomain = false;
        static $page = false;
        static $section = false;
        static $optionGroup = false;
        static $args = array();

        /**
         * pbSettingsFramework constructor.
         *
         * @param array $args
         */
        public function __construct($args=array())
        {
            self::$textDomain = $args['text-domain'] || 'pbSettingsFramework';
            self::$page = $args['page'];
            self::$section = $args['section'];
            self::$optionGroup = $args['option-group'];
            self::$args = $args;
        }

        /**
         * Register setting
         *
         * @param array $setting
         */
        public static function registerSetting($setting)
        {
            if( ! isset(self::$args['option-group']) ) {
                die(__FUNCTION__.': $args[\'option-group\'] not set!');
            }

            return register_setting(self::$args['option-group'], $setting);
        }

        /**
         * Add settings section
         *
         * @param $id
         * @param $title
         * @param $callback
         */
        public static function addSettingsSection($id, $title, $callback)
        {
            add_settings_section(
                $id,
                $title,
                $callback,
                self::$args['page']
            );
        }

        /**
         * Add settings field
         *
         * @param $id
         * @param $title
         * @param $args
         * @param array $callback
         * @param bool $register_setting
         */
        public static function addSettingsField($id, $title, $args, $callback=array(__CLASS__, 'fieldsHTML'), $register_setting=true)
        {
            if( $register_setting )
                register_setting( self::$args['option-group'], $id, 'esc_attr' );


            add_settings_field(
                $id,
                '<label for="'.$id.'">'.$title.'</label>',
                $callback,
                self::$page,
                self::$section,
                array_merge_recursive(
                    array(
                        'id' => $id,
                        'section' => self::$section
                    ),

                    $args
                )
            );
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
         * html code for fields
         *
         * @param $args
         */
        public static function fieldsHTML( $args )
        {
            $option = get_option($args['id']);
            $html = '';

            if( self::getArrayKey('type', $args) == 'text' ) {

                if ( empty( $option ) ) {
                    $val = self::getArrayKey('default', $args);
                } else {
                    $val = $option;
                }
                $html = '<input type="text" id="' . $args['id'] . '" name="' . $args['id'] . '" class="regular-text" value="' . $val . '" '.((self::getArrayKey('disabled', $args))?'disabled="disabled"':'').' />';

                $desc = self::getArrayKey('desc', $args);

                if ( ! empty( $desc ) ) {
                    $html .= '<p class="description">' . $desc . '</p>';
                }

            } elseif( $args['type'] == 'checkbox' ) {

                if( $option === false ){
                    $val = self::getArrayKey('default', $args);
                }else{
                    $val = $option;
                }
                $html = '<input type="checkbox" id="'.$args['id'].'" name="'.$args['id'].'" value="1" '.((self::getArrayKey('disabled', $args))?'disabled="disabled"':'').' '.checked(1, $val, false).'/>';

                $html .= '<label for="'.$args['id'].'"> '. self::getArrayKey('desc', $args) .'</label>';

            } elseif( $args['type'] == 'select' ) {

                if ( empty( $option ) ) {
                    $val = self::getArrayKey('default', $args);
                } else {
                    $val = $option;
                }

                $html = '<select id="'.$args['id'].'" name="'.$args['id'].'">';
                foreach ($args['select'] as $name => $value ) {
                    $html .= '<option value="'.$name.'" '.(($val==$name)?'selected="selected"':'').' '.((self::getArrayKey('disabled', $args))?'disabled="disabled"':'').'>'.esc_html($value).'</option>';
                }
                $html .= '</select>';

                $desc = self::getArrayKey('desc', $args);

                if ( ! empty( $desc ) ) {
                    $html .= '<p class="description">' . $desc . '</p>';
                }

            }

            echo $html;
        }

        public static function doSettingsSections( $page ) {
            global $wp_settings_sections, $wp_settings_fields;

            if ( ! isset( $wp_settings_sections[$page] ) )
                return;

            foreach ( (array) $wp_settings_sections[$page] as $section ) {
                if ( $section['prepend'] )
                    echo $section['prepend'];

                if ( $section['title'] )
                    echo "<h2 class='pb-section-title'>{$section['title']}</h2>\n";

                if ( $section['callback'] )
                    call_user_func( $section['callback'], $section );

                if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
                    continue;

                echo '<div class="pb-section-wrap">';
                echo '<table class="form-table">';
                do_settings_fields( $page, $section['id'] );
                echo '</table>';
                echo '</div>';

                if ( $section['append'] )
                    echo $section['append'];
            }
        }
    }
endif; //class_exists