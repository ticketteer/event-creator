<?php

namespace Ticketteer\EventCreator;

class ArtistsHelper {

  public static $instance;

  private $base;

  public function __construct() {
    $this->base = EventCreator::get_instance();
    add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );
  }

  /**
   * Return singleton of this class
   *
   * @return object Singleton of ArtistPostTypes
   *
   * @since 1.0.0
   *
   */
  public static function get_instance() {
    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ArtistsHelper ) ) {
      self::$instance = new ArtistsHelper();
    }
    return self::$instance;
  }

  /**
   * Create an artist menu entry
   *
   * @since 1.0.0
   *
   */
  public function admin_menu() {
  }

  /**
   * Loads the artists page from the partial and renders
   *
   * @since 1.0.0
   *
   */
  public function artists_page() {
     $this->base->load_admin_partial( 'artists_list', array(
       'textdomain'    => $this->base->plugin_slug,
     ) );
  }

  /**
   * Reners the new artist page on demand
   *
   * @since 1.0.0
   *
   */
  public function new_artist_page() {
     $this->base->load_admin_partial( 'artist_form', array(
       'textdomain'    => $this->base->plugin_slug,
       'artist'        => null
     ) );
  }

}

// Load the addons class.
$artists_helper = ArtistsHelper::get_instance();
