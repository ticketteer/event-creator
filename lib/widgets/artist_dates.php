<?php

namespace Ticketteer\EventCreator;

class ArtistDatesWidget extends \WP_Widget {

  public function __construct() {
    $this->base = EventCreator::get_instance();
		$widget_ops = array(
			'classname' => 'event-creator-artist-dates-widget',
			'description' => 'Shows a list of artist dates',
		);
		parent::__construct( 'event_creator_artist_dates_widget', 'Event Creator Artist Dates', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
    // $title = apply_filters( 'widget_title', $instance['title'] );
    $this->base->load_admin_partial( 'artist_dates_widget', array(
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
    return array(
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

add_action( 'widgets_init', __NAMESPACE__ . '\artist_dates_widget', 0);
function artist_dates_widget(){
  register_widget( __NAMESPACE__ . '\ArtistDatesWidget' );
}
