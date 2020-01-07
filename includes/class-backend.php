<?php

/**
 * Registering settings and custom post type
 */

namespace ms\GoogleMap;

defined('ABSPATH') || exit;

class Backend
{
	public function __construct()
	{
		add_action('init', array($this, 'registerPostType'));
		add_action('admin_init', array($this, 'register_settings'));
		add_filter('acf/fields/google_map/api', array($this, 'register_acf_google_map_api'));
		add_action('template_redirect', array($this, 'disable_single_page'));
		add_action('edit_form_after_editor', array($this, 'generate_shortcode'));
		add_filter('plugin_row_meta', array($this, 'modify_plugin_meta'), 10, 2);
	}

	// Register options under Settings->General
	public function register_settings()
	{
		add_settings_section(
			'wpmaps_section',
			'Google maps',
			array(),
			'general'
		);

		add_settings_field(
			'wpmaps_api_key',
			'Google maps API key',
			array($this, 'display_api_key_input'),
			'general',
			'wpmaps_section',
			array(
				'wpmaps_api_key'
			)
		);

		register_setting('general', 'wpmaps_api_key', 'esc_attr');
	}

	// Display input for API key
	public function display_api_key_input($args)
	{
		$option = get_option($args[0]);
		echo '<input class="regular-text" type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" />';
	}

	// Connect Google map API key to ACF plugin
	public function register_acf_google_map_api($api)
	{

		$api['key'] = get_option('wpmaps_api_key');

		return $api;
	}

	// Register map post type
	public function registerPostType()
	{

		$args = array(
			'label'                 => __('Maps', 'wpmaps'),
			'supports'              => array('title'),
			'taxonomies'            => array(),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 100,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type('wpmaps', $args);
	}

	// Disable single post type view
	public function disable_single_page()
	{
		$queried_post_type = get_query_var('post_type');
		if (is_single() && 'wpmaps' ==  $queried_post_type) {
			wp_redirect(home_url(), 301);
			exit;
		}
	}

	// Display shortcode when creating a map so that user can copy it
	public function generate_shortcode()
	{
		global $typenow;
		if ($typenow == 'wpmaps') :
?>
<div class="postbox-container">
	<h2>Map shortcode</h2>
	<input class="regular-text" type="text" value="[wpmaps id=<?php echo htmlentities('"' . get_the_ID() . '"'); ?>]">
</div>
<?php
		endif;
	}

	// Add link to readme file on installed plugin listing
	public function modify_plugin_meta( $links_array, $file)
	{
		if(strpos( $file, 'google-maps/google-maps.php' ) !== false) {
			$links_array[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php#wpmaps_api_key') ) .'">Settings</a>';
			$links_array[] = '<a href="' . plugins_url('readme.md', __DIR__) . '" target="_blank">How to use</a>';
		}
		return $links_array;
	}
}