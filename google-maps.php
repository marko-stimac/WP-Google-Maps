<?php
/*
 * Plugin Name: Google Maps
 * Description: Display multiple Google maps, ACF PRO is required
 * Version: 1.0.0
 * Author: Marko Štimac
 * Author URI: https://marko-stimac.github.io/
 * Text Domain: wpmaps
 */

namespace ms\GoogleMap;

defined('ABSPATH') || exit;

require_once 'includes/class-backend.php';
require_once 'includes/class-frontend.php';

new Backend();
$google_map = new Frontend();

add_shortcode('wpmaps', array($google_map, 'showComponent'));