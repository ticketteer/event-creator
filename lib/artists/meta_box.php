<?php

namespace Ticketteer\EventCreator;

class ArtistsMetaBox {

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
      'event-creator-artist-details',
      __('Artist details', $this->base->plugin_slug, 'event-creator'),
      array($this, 'meta_box'),
      'artists',
      'normal'
    );
  }

  public function meta_box($post) {

      if ( $post->post_type != 'artists' ) {
        return;
      }

     $this->base->load_admin_partial( 'artist_details_meta_box', array(
       'post'          => $post,
       'textdomain'    => $this->base->plugin_slug,
     ) );
  }

  public function save_meta_box( $post_id, $post ) {

    // Bail out if we fail a security check.
    if ( ! isset( $_POST['_artists'] ) ) {
      return;
    }

    // Bail out if running an autosave, ajax, cron or revision.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    foreach( $_POST['_artists'] as $key => $value) {
	    update_post_meta( $post_id, $key, $value );
    }

    // fire a hook for addons
    do_action( 'event_creator_artist_event_saved', $post );

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
    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ArtistsMetaBox ) ) {
      self::$instance = new ArtistsMetaBox();
    }
    return self::$instance;
  }

}

// Load the addons class.
$meta_box = ArtistsMetaBox::get_instance();
