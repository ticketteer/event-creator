<?php

namespace Ticketteer\EventCreator;

class CalendarWidget extends \WP_Widget {

  public function __construct() {
    $this->base = EventCreator::get_instance();
		$widget_ops = array(
			'classname' => 'event-creator-calendar-widget',
			'description' => 'Shows a list of events set up in the EventCreator',
		);

    wp_register_script( 'event-creator-calendar',
      (plugin_dir_url( dirname(dirname(__FILE__)) ) . '/assets/js/event-creator-calendar.js'),
      array('jquery'),
      0
    );
    add_action( 'wp_enqueue_scripts', array($this, 'enqueue_calendar_scripts'));

		parent::__construct( 'event_creator_calendar_widget', 'Event Creator Calendar', $widget_ops );
	}

  public function enqueue_calendar_scripts() {
    wp_enqueue_script('event-creator-calendar');
    $tt_opts = array(
      'api_endpoint' => $this->base->api_pub_endpoint,
      'public_key' => get_option('ticketteer-pub-key'),
    );
    wp_localize_script( 'event-creator-calendar', 'ttOpts', $tt_opts );
  }

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
    // $title = apply_filters( 'widget_title', $instance['title'] );
    $this->base->load_admin_partial( 'calendar_widget', array(
      'instance'      => $instance,
      'fields'        => $this->get_field_values($instance),
      'textdomain'    => $this->base->plugin_slug,
    ) );
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
   *
   * @since 1.0.0
   *
	 */
	public function form( $instance ) {
    $this->base->load_admin_partial( 'calendar_widget_form', array(
      'instance'      => $instance,
      'fields'        => $this->get_fields($instance),
      'textdomain'    => $this->base->plugin_slug,
    ), $instance );
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
   *
   * @since 1.0.0
   *
	 */
	public function update( $new_instance, $old_instance ) {
    $instance = array();
    $fields = $this->get_fields($instance);
    foreach( $fields as $field) {
      $instance[$field['name']] = empty($new_instance[$field['name']]) ? '' : strip_tags($new_instance[$field['name']]);
    }
    return $instance;
	}

  /**
   * gets all fields for this widget
   *
   * @return array of fields
   *
   * @since 1.0.0
   *
   */
  private function get_fields($instance) {
    $value = array_key_exists('buy_button_text', $instance) && !empty($instance['buy_button_text']) ? $instance['buy_button_text'] : '';
    return array(
      array(
        'type' => 'text',
        'name' => 'buy_button_text',
        'calc_name' => $this->get_field_name('buy_button_text'),
        'label' => __('[Buy Tickets]-Text', $this->base->plugin_slug, 'event-creator'),
        'value' => $value,
        'id' => $this->get_field_id('buy_button_text'),
      ),
    );
  }

  /**
   * get all fields' values
   *
   * @param  array $instance widget $instance holding variables
   *
   * @return array with key:value style
   *
   * @since 1.0.0
   *
   */
  private function get_field_values($instance) {
    $fields = $this->get_fields($instance);
    $values = array();
    foreach( $fields as $field) {
      $values[$field['name']] = $field['value'];
    }
    return $values;
  }

}

add_action( 'widgets_init', __NAMESPACE__ . '\calendar_widget', 0);
function calendar_widget(){
    register_widget( __NAMESPACE__ . '\CalendarWidget' );
}
