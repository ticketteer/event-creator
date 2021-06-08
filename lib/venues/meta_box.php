<?php

namespace Ticketteer\EventCreator;

class VenuesMetaBox {

  public $base;

  public static $instance;

  public function __construct() {
    $this->base = EventCreator::get_instance();
    add_action('add_meta_boxes', array($this, 'create_meta_boxes'), 0);

    // save content
    add_action( 'save_post', array( $this, 'save_meta_box' ), 10, 2 );
  }

  public function create_meta_boxes() {

    add_meta_box(
      'event-creator-venue-details',
      __('Venue details', $this->base->plugin_slug, 'event-creator'),
      array($this, 'meta_box'),
      'venues',
      'normal'
    );
  }

  public function meta_box($post) {

      if ( $post->post_type != 'venues' ) {
        return;
      }

     $this->base->load_admin_partial( 'venue_details_meta_box', array(
       'post'          => $post,
       'textdomain'    => $this->base->plugin_slug,
     ) );
  }

  public function save_meta_box( $post_id, $post ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    if ( $post->post_type != 'venues' ) return;

    if ( ! isset( $_POST['_venues'] ) ) return;

    foreach( $_POST['_venues'] as $key => $value) {
	    update_post_meta( $post_id, $key, $value );
    }

    do_action( 'event_creator_venue_saved', $post );

  }

  /**
   * Return singleton of this class
   *
   * @return object Singleton of MetaBox
   *
   * @since 1.0.0
   *
   */
  public static function get_instance() {
    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof VenuesMetaBox ) ) {
      self::$instance = new VenuesMetaBox();
    }
    return self::$instance;
  }

}

// Load the addons class.
$meta_box = VenuesMetaBox::get_instance();
