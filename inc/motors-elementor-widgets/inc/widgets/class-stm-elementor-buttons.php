<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Stm_Elementor_Buttons extends Widget_Base {
	protected $handler = 'stm-elementor-buttons';

	public function get_name() {
		return 'stm-elementor-buttons';
	}

	public function get_title() {
		return esc_html__( 'Motors Leasing, Van, Car buttons', 'motors-child' );
	}

	public function get_icon() {
		return 'eicon-woo-cart';
	}

	public function get_keywords() {
		return [ 'buttons', 'stylemix', 'motors' ];
	}

	public function get_categories() {
		return [ 'motors' ];
	}

	public function get_script_depends() {
		return array( $this->handler );
	}

	public function get_style_depends() {
		return array( $this->handler );
	}


	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'General', 'elementor-pro' ),
			]
		);
		$templates = $this->get_elementor_templates(); // Function to fetch Elementor templates and their IDs
		$this->add_control(
			'additional_template_select',
			[
				'label'              => esc_html__( 'Select Template', 'motors-child' ),
				'type'               => Controls_Manager::SELECT2,
				'options'            => $templates,
				'label_block'        => true,
				'show_label'         => false,
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);
		$this->end_controls_section();

	}

	private function get_elementor_templates() {
		$templates = [];
		// Check if Elementor is active
		if ( class_exists( 'Elementor\Plugin' ) ) {
			$templates_manager = \Elementor\Plugin::$instance->templates_manager;
			// Get all templates
			$templates_data = $templates_manager->get_source( 'local' )->get_items();
			foreach ( $templates_data as $template ) {
				$templates[ $template['template_id'] ] = $template['title'];
			}
		}

		return $templates;
	}

	public function render() {

		$current_post_type = get_post_type();

		// Check if the current post type is 'listings'
		if ( $current_post_type === 'listings' ) {
			// Display our Widget.
			$template_id = intval( $this->get_settings_for_display( 'additional_template_select' ) );
			if ( ! empty( $template_id ) ) {
				echo do_shortcode( '[elementor-template id="' . $template_id . '"]' );
			}

			 $default_template = locate_template('templates/widgets/stm-buttons/'.$this->handler.'.php');

            if ($default_template) {
                include $default_template;
            }
		}
	}

}

