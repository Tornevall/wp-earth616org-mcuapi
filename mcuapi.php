<?php
/**
 * Plugin Name: Earth616.ORG MCU Timeline API
 * Plugin URI:
 * Description: Connects Earth616.ORG MCU Timeline API to WordPress.
 * Version: 1.0.0
 * Author: Tornevall
 * Author URI:
 * Text Domain: emt_widget
 * Domain Path:
 */

require_once(__DIR__ . '/vendor/autoload.php');
require_once(sprintf('%s/%s', __DIR__, '/Earth_MCU_Widget.php'));
add_action('widgets_init', 'earth616_mcu_widget');

function earth616_mcu_widget() {
	register_widget('Earth_MCU_Widget');
}
