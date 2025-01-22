<?php
function flance_write_log( $message, $file = 'logs/logfile.log' ) {

	ob_start();
	print_r( $message );
	$message         = ob_get_clean();
	$theme_directory = get_stylesheet_directory();
	$log_file_path = $theme_directory . '/' . $file;
	$log_directory = dirname( $log_file_path );
	if ( ! file_exists( $log_directory ) ) {
		mkdir( $log_directory, 0755, true );
	}
	file_put_contents( $log_file_path, ' ' . $message . "\n",  LOCK_EX );
}

function enqueue_custom_scripts() {
	wp_enqueue_style('general-style', get_stylesheet_directory_uri() . '/assets/css/new-style.css', [], time());
    wp_enqueue_script('stm-butterbean_child', get_stylesheet_directory_uri() . "/assets/js/stm_butterbean_fields_child.js", array(), time(), true);
    wp_enqueue_script('stm-theme-multiselect', STM_LISTINGS_URL . '/assets/js/jquery.multi-select.js', array('jquery'), time(), true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');
require_once 'butterbean_helpers.php';
require_once 'motors-elementor-widgets/loader.php';
require_once 'stm-motors-extends/loader.php';
require_once 'vehicle_functions/vehicle_functions.php';
function stm_my_autoloader( $class ) {
	$directory = __DIR__.'/vehicle_functions/classes_helpers';
	$file      = $directory . '/' . $class . '.php';

	if ( file_exists( $file ) ) {
		require_once $file;
	}
}

spl_autoload_register( 'stm_my_autoloader' );
UserRoleProductMapper::init();



