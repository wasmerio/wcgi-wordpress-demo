<?php
/**
 * Internationalization helper.
 *
 * @package     Kirki
 * @category    Core
 * @author      Aristeides Stathopoulos
 * @copyright   Copyright (c) 2016, Aristeides Stathopoulos
 * @license     http://opensource.org/licenses/https://opensource.org/licenses/MIT
 * @since       1.0
 */

if ( ! class_exists( 'Kirki_l10n' ) ) {

	/**
	 * Handles translations
	 */
	class Kirki_l10n {

		/**
		 * The plugin textdomain
		 *
		 * @access protected
		 * @var string
		 */
		protected $textdomain = 'mesmerize';

		/**
		 * The class constructor.
		 * Adds actions & filters to handle the rest of the methods.
		 *
		 * @access public
		 */
		public function __construct() {
			if ( ! Kirki::should_run() ) {
				return;
			}

			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		}

		/**
		 * Load the plugin textdomain
		 *
		 * @access public
		 */
		public function load_textdomain() {

			if ( null !== $this->get_path() ) {
				load_textdomain( $this->textdomain, $this->get_path() );
			}
			load_plugin_textdomain( $this->textdomain, false, Kirki::$path . '/languages' );

		}

		/**
		 * Gets the path to a translation file.
		 *
		 * @access protected
		 * @return string Absolute path to the translation file.
		 */
		protected function get_path() {
			$path_found = false;
			$found_path = null;
			foreach ( $this->get_paths() as $path ) {
				if ( $path_found ) {
					continue;
				}
				$path = wp_normalize_path( $path );
				if ( file_exists( $path ) ) {
					$path_found = true;
					$found_path = $path;
				}
			}

			return $found_path;

		}

		/**
		 * Returns an array of paths where translation files may be located.
		 *
		 * @access protected
		 * @return array
		 */
		protected function get_paths() {

			return array(
				WP_LANG_DIR . '/' . $this->textdomain . '-' . get_locale() . '.mo',
				Kirki::$path . '/languages/' . $this->textdomain . '-' . get_locale() . '.mo',
			);

		}

		/**
		 * Shortcut method to get the translation strings
		 *
		 * @static
		 * @access public
		 *
		 * @param string $config_id The config ID. See Kirki_Config.
		 *
		 * @return array
		 */
		public static function get_strings( $config_id = 'global' ) {

			$translation_strings = array(
				'background-color'      => esc_attr__( 'Background Color', 'mesmerize' ),
				'background-image'      => esc_attr__( 'Background Image', 'mesmerize' ),
				'no-repeat'             => esc_attr__( 'No Repeat', 'mesmerize' ),
				'repeat-all'            => esc_attr__( 'Repeat All', 'mesmerize' ),
				'repeat-x'              => esc_attr__( 'Repeat Horizontally', 'mesmerize' ),
				'repeat-y'              => esc_attr__( 'Repeat Vertically', 'mesmerize' ),
				'inherit'               => esc_attr__( 'Inherit', 'mesmerize' ),
				'background-repeat'     => esc_attr__( 'Background Repeat', 'mesmerize' ),
				'cover'                 => esc_attr__( 'Cover', 'mesmerize' ),
				'contain'               => esc_attr__( 'Contain', 'mesmerize' ),
				'background-size'       => esc_attr__( 'Background Size', 'mesmerize' ),
				'fixed'                 => esc_attr__( 'Fixed', 'mesmerize' ),
				'scroll'                => esc_attr__( 'Scroll', 'mesmerize' ),
				'background-attachment' => esc_attr__( 'Background Attachment', 'mesmerize' ),
				'left-top'              => esc_attr__( 'Left Top', 'mesmerize' ),
				'left-center'           => esc_attr__( 'Left Center', 'mesmerize' ),
				'left-bottom'           => esc_attr__( 'Left Bottom', 'mesmerize' ),
				'right-top'             => esc_attr__( 'Right Top', 'mesmerize' ),
				'right-center'          => esc_attr__( 'Right Center', 'mesmerize' ),
				'right-bottom'          => esc_attr__( 'Right Bottom', 'mesmerize' ),
				'center-top'            => esc_attr__( 'Center Top', 'mesmerize' ),
				'center-center'         => esc_attr__( 'Center Center', 'mesmerize' ),
				'center-bottom'         => esc_attr__( 'Center Bottom', 'mesmerize' ),
				'background-position'   => esc_attr__( 'Background Position', 'mesmerize' ),
				'background-opacity'    => esc_attr__( 'Background Opacity', 'mesmerize' ),
				'on'                    => esc_attr__( 'ON', 'mesmerize' ),
				'off'                   => esc_attr__( 'OFF', 'mesmerize' ),
				'all'                   => esc_attr__( 'All', 'mesmerize' ),
				'cyrillic'              => esc_attr__( 'Cyrillic', 'mesmerize' ),
				'cyrillic-ext'          => esc_attr__( 'Cyrillic Extended', 'mesmerize' ),
				'devanagari'            => esc_attr__( 'Devanagari', 'mesmerize' ),
				'greek'                 => esc_attr__( 'Greek', 'mesmerize' ),
				'greek-ext'             => esc_attr__( 'Greek Extended', 'mesmerize' ),
				'khmer'                 => esc_attr__( 'Khmer', 'mesmerize' ),
				'latin'                 => esc_attr__( 'Latin', 'mesmerize' ),
				'latin-ext'             => esc_attr__( 'Latin Extended', 'mesmerize' ),
				'vietnamese'            => esc_attr__( 'Vietnamese', 'mesmerize' ),
				'hebrew'                => esc_attr__( 'Hebrew', 'mesmerize' ),
				'arabic'                => esc_attr__( 'Arabic', 'mesmerize' ),
				'bengali'               => esc_attr__( 'Bengali', 'mesmerize' ),
				'gujarati'              => esc_attr__( 'Gujarati', 'mesmerize' ),
				'tamil'                 => esc_attr__( 'Tamil', 'mesmerize' ),
				'telugu'                => esc_attr__( 'Telugu', 'mesmerize' ),
				'thai'                  => esc_attr__( 'Thai', 'mesmerize' ),
				'serif'                 => _x( 'Serif', 'font style', 'mesmerize' ),
				'sans-serif'            => _x( 'Sans Serif', 'font style', 'mesmerize' ),
				'monospace'             => _x( 'Monospace', 'font style', 'mesmerize' ),
				'font-family'           => esc_attr__( 'Font Family', 'mesmerize' ),
				'font-size'             => esc_attr__( 'Font Size', 'mesmerize' ),
				'mobile-font-size'      => esc_attr__( 'Mobile Font Size', 'mesmerize' ),
				'font-weight'           => esc_attr__( 'Font Weight', 'mesmerize' ),
				'line-height'           => esc_attr__( 'Line Height', 'mesmerize' ),
				'font-style'            => esc_attr__( 'Font Style', 'mesmerize' ),
				'letter-spacing'        => esc_attr__( 'Letter Spacing', 'mesmerize' ),
				'top'                   => esc_attr__( 'Top', 'mesmerize' ),
				'bottom'                => esc_attr__( 'Bottom', 'mesmerize' ),
				'left'                  => esc_attr__( 'Left', 'mesmerize' ),
				'right'                 => esc_attr__( 'Right', 'mesmerize' ),
				'center'                => esc_attr__( 'Center', 'mesmerize' ),
				'justify'               => esc_attr__( 'Justify', 'mesmerize' ),
				'color'                 => esc_attr__( 'Color', 'mesmerize' ),
				'add-image'             => esc_attr__( 'Add Image', 'mesmerize' ),
				'change-image'          => esc_attr__( 'Change Image', 'mesmerize' ),
				'no-image-selected'     => esc_attr__( 'No Image Selected', 'mesmerize' ),
				'add-file'              => esc_attr__( 'Add File', 'mesmerize' ),
				'change-file'           => esc_attr__( 'Change File', 'mesmerize' ),
				'no-file-selected'      => esc_attr__( 'No File Selected', 'mesmerize' ),
				'remove'                => esc_attr__( 'Remove', 'mesmerize' ),
				'select-font-family'    => esc_attr__( 'Select a font-family', 'mesmerize' ),
				'variant'               => esc_attr__( 'Variant', 'mesmerize' ),
				'subsets'               => esc_attr__( 'Subset', 'mesmerize' ),
				'size'                  => esc_attr__( 'Size', 'mesmerize' ),
				'height'                => esc_attr__( 'Height', 'mesmerize' ),
				'spacing'               => esc_attr__( 'Spacing', 'mesmerize' ),
				'ultra-light'           => esc_attr__( 'Thin (100)', 'mesmerize' ),
				'ultra-light-italic'    => esc_attr__( 'Thin (100) Italic', 'mesmerize' ),
				'light'                 => esc_attr__( 'Extra light (200)', 'mesmerize' ),
				'light-italic'          => esc_attr__( 'Extra light (200) Italic', 'mesmerize' ),
				'book'                  => esc_attr__( 'Light (300)', 'mesmerize' ),
				'book-italic'           => esc_attr__( 'Light (300) Italic', 'mesmerize' ),
				'regular'               => esc_attr__( 'Normal (400)', 'mesmerize' ),
				'italic'                => esc_attr__( 'Normal (400) Italic', 'mesmerize' ),
				'medium'                => esc_attr__( 'Medium (500)', 'mesmerize' ),
				'medium-italic'         => esc_attr__( 'Medium (500) Italic', 'mesmerize' ),
				'semi-bold'             => esc_attr__( 'Semi Bold (600)', 'mesmerize' ),
				'semi-bold-italic'      => esc_attr__( 'Semi Bold (600) Italic', 'mesmerize' ),
				'bold'                  => esc_attr__( 'Bold (700)', 'mesmerize' ),
				'bold-italic'           => esc_attr__( 'Bold (700) Italic', 'mesmerize' ),
				'extra-bold'            => esc_attr__( 'Extra Bold (800)', 'mesmerize' ),
				'extra-bold-italic'     => esc_attr__( 'Extra Bold (800) Italic', 'mesmerize' ),
				'ultra-bold'            => esc_attr__( 'Black (900)', 'mesmerize' ),
				'ultra-bold-italic'     => esc_attr__( 'Black (900) Italic', 'mesmerize' ),
				'invalid-value'         => esc_attr__( 'Invalid Value', 'mesmerize' ),
				'add-new'               => esc_attr__( 'Add new', 'mesmerize' ),
				'row'                   => esc_attr__( 'row', 'mesmerize' ),
				'limit-rows'            => esc_attr__( 'Limit: %s rows', 'mesmerize' ),
				'open-section'          => esc_attr__( 'Press return or enter to open this section', 'mesmerize' ),
				'back'                  => esc_attr__( 'Back', 'mesmerize' ),
				'reset-with-icon'       => sprintf( esc_attr__( '%s Reset', 'mesmerize' ),
					'<span class="dashicons dashicons-image-rotate"></span>' ),
				'text-align'            => esc_attr__( 'Text Align', 'mesmerize' ),
				'text-transform'        => esc_attr__( 'Text Transform', 'mesmerize' ),
				'none'                  => esc_attr__( 'None', 'mesmerize' ),
				'capitalize'            => esc_attr__( 'Capitalize', 'mesmerize' ),
				'uppercase'             => esc_attr__( 'Uppercase', 'mesmerize' ),
				'lowercase'             => esc_attr__( 'Lowercase', 'mesmerize' ),
				'initial'               => esc_attr__( 'Initial', 'mesmerize' ),
				'select-page'           => esc_attr__( 'Select a Page', 'mesmerize' ),
				'open-editor'           => esc_attr__( 'Open Editor', 'mesmerize' ),
				'close-editor'          => esc_attr__( 'Close Editor', 'mesmerize' ),
				'switch-editor'         => esc_attr__( 'Switch Editor', 'mesmerize' ),
				'hex-value'             => esc_attr__( 'Hex Value', 'mesmerize' ),
				'addwebfont'            => esc_attr__( 'Add Web Font', 'mesmerize' ),
			);

			// Apply global changes from the kirki/config filter.
			// This is generally to be avoided.
			// It is ONLY provided here for backwards-compatibility reasons.
			// Please use the kirki/{$config_id}/l10n filter instead.
			$config = apply_filters( 'kirki/config', array() );
			if ( isset( $config['i18n'] ) ) {
				$translation_strings = wp_parse_args( $config['i18n'], $translation_strings );
			}

			// Apply l10n changes using the kirki/{$config_id}/l10n filter.
			return apply_filters( 'kirki/' . $config_id . '/l10n', $translation_strings );

		}
	}
}
