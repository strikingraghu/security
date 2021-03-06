<?php
namespace Securityelementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;



// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 *
 * Security elementor about us section widget.
 *
 * @since 1.0
 */
class Security_Counter extends Widget_Base {

	public function get_name() {
		return 'security-counter';
	}

	public function get_title() {
		return __( 'Counter', 'security' );
	}

	public function get_icon() {
		return 'eicon-counter';
	}

	public function get_categories() {
		return [ 'security-elements' ];
	}

	protected function _register_controls() {

		$repeater = new \Elementor\Repeater();


        // ----------------------------------------  Features content ------------------------------
        $this->start_controls_section(
            'counter_content',
            [
                'label' => __( 'Counter', 'security' ),
            ]
        );
        $this->add_control(
            'counter', [
                'label' => __( 'Create Counter', 'security' ),
                'type' => Controls_Manager::REPEATER,
                'title_field' => '{{{ label }}}',
                'fields' => [
                    [
                        'name'          => 'label',
                        'label'         => __( 'Title', 'security' ),
                        'type'          => Controls_Manager::TEXT,
                        'label_block'   => true,
                        'default'       => 'Projects Completed'
                    ],
                    [
                        'name'    => 'number',
                        'label'   => __( 'Number', 'security' ),
                        'type'    => Controls_Manager::TEXT,
                        'default' => '2536'
                    ]
                ],
            ]
        );

        $this->end_controls_section(); // End content

        //------------------------------ Style counter ------------------------------
        $this->start_controls_section(
            'style_counter', [
                'label' => __( 'Style Counter', 'security' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'color_countertitle', [
                'label' => __( 'Title Color', 'security' ),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .single-fact h1' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'color_number', [
                'label'     => __( 'Number Color', 'security' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .single-fact p' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'color_bg', [
                'label'     => __( 'Background Color', 'security' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#fab700',
                'selectors' => [
                    '{{WRAPPER}} .facts-area' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
	}

	protected function render() {

    $settings = $this->get_settings();


    ?>
    <section class="facts-area section-gap">
        <div class="container">
            <div class="row">
                <?php
                if( is_array( $settings['counter'] ) && count( $settings['counter'] ) > 0 ):
                    foreach( $settings['counter'] as $val ):
                ?>
                <div class="col single-fact">
                    <?php
                    if( ! empty( $val['number'] ) ) {
                        echo security_heading_tag(
                            array(
                                'tag'  => 'h1',
                                'class' => 'counter',
                                'text' => absint( $val['number'] )
                            )
                        );
                    }
                    //
                    if( ! empty( $val['label'] ) ) {
                        echo security_paragraph_tag(
                            array(
                                'text' => esc_html( $val['label'] )
                            )
                        );
                    }
                    ?>
                </div>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>
        </div>  
    </section>
    <!-- end fact Area -->

    <?php

    }
	
}
