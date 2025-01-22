<?php
define ('MOTORS_CHILD_ELEMENTOR_WIDGETS_PATH', get_stylesheet_directory().'/inc/motors-elementor-widgets' );
define ('MOTORS_CHILD_ELEMENTOR_WIDGETS_URL', get_stylesheet_directory_uri().'/inc/motors-elementor-widgets' );

require_once 'inc/Helpers/TemplateManagerChild.php';
require_once 'inc/Helpers/TemplateManagerLeasingChild.php';

add_action('init', 'stm_add_init_widgets');
function stm_add_init_widgets() {
	add_action( 'elementor/widgets/register', 'stm_add_widgets' );
}
function stm_add_widgets() {
	require_once 'inc/widgets/class-stm-elementor-buttons.php';
	\Elementor\Plugin::instance()->widgets_manager->register( new Stm_Elementor_Buttons() );
}