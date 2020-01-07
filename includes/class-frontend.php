<?php

/**
 * Register scripts, styles, fetch data and show component
 */

namespace ms\GoogleMap;

defined('ABSPATH') || exit;

class Frontend
{

	public function __construct()
	{
		add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
	}

	// Register general scripts and styles
	public function register_scripts()
	{
		wp_register_style('googlemaps', plugins_url('assets/googlemap.css', __DIR__));
		wp_register_script('googlemap-api', 'https://maps.googleapis.com/maps/api/js?key=' . get_option('wpmaps_api_key'), null, null, true);
		wp_register_script('googlemaps-infobox', plugins_url('assets/vendor/googlemap-infobox.js', __DIR__), array('jquery'), null, null, true);
		wp_register_script('googlemaps', plugins_url('assets/googlemap.js', __DIR__), array('jquery', 'googlemap-api', 'googlemaps-infobox'), null, true);
	}

	// Retrieve data for maps and pass it to JS
	public function showComponent($atts, $content)
	{
		$atts = shortcode_atts(array(
			'id' => ''
		), $atts);
		ob_start();
		$data = [];
		if (have_rows('wpmap_marker', $atts['id'])) :
			while (have_rows('wpmap_marker', $atts['id'])) : the_row();
				$position = get_sub_field('wpmap_marker_position');
				$image = get_sub_field('wpmap_content_image');
				$content = get_sub_field('wpmap_content');
				$image = $image ? $image['url'] : '';
				$icon = get_sub_field('wpmap_marker_icon');
				$icon = $icon ? $icon['url'] : '';

				$data[] = array(
					$position['lat'],
					$position['lng'],
					$image,
					$content,
					$icon
				);

			endwhile;
		endif;

		wp_localize_script(
			'googlemaps',
			'googlemaps_' . $atts['id'],
			array(
				'markers' => $data,
				'zoom' => get_field('wpmap_zoom', $atts['id'])
			)
		);
		wp_enqueue_script('googlemaps');
		wp_enqueue_style('googlemaps');
?>

<div class="wpmaps wpmaps--<?php echo $atts['id']; ?>" data-id="<?php echo $atts['id']; ?>"></div>

<?php
		return ob_get_clean();
	}
}