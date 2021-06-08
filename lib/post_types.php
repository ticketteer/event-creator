<?php

namespace Ticketteer\EventCreator;

trait PostTypes {

  private function create_post_type_events() {

    $label_names = [
      'name'          => __('Event Creator', $this->plugin_slug, 'event-creator'),
      'singular_name' => __('Event', $this->plugin_slug, 'event-creator'),
      'add_new'       => __('New Event', $this->plugin_slug, 'event-creator'),
      'add_new_item'       => __('Create new Event', $this->plugin_slug, 'event-creator'),
      'edit_item'       => __('Edit Event', $this->plugin_slug, 'event-creator'),
      'view_item'       => __('View Event', $this->plugin_slug, 'event-creator'),
      'view_items'       => __('View all Events', $this->plugin_slug, 'event-creator'),
      'search_items'       => __('Search Events', $this->plugin_slug, 'event-creator'),
      'not_found'       => __('No Events found', $this->plugin_slug, 'event-creator'),
      'not_found_in_trash'       => __('No Events found in Trash', $this->plugin_slug, 'event-creator'),
      'all_items'       => __('Events', $this->plugin_slug, 'event-creator'),
      'archives'       => __('Events Archives', $this->plugin_slug, 'event-creator'),
      'attributes'       => __('Event Attributes', $this->plugin_slug, 'event-creator'),
    ];
    register_post_type('events',
      [
        'labels'      => $label_names,
        'public'      => true,
        'has_archive' => true,
        'show_in_nav_menus' => true,
  			'hierarchical' => true,
        'supports' => [
          'title',
          'thumbnail',
        ],
        'can_export' => true,
        'taxonomies' => array(
          'event-type',
        ),
        'menu_position'       => apply_filters( 'event_creator_post_type_menu_position', 311 ),
        'menu_icon'           => plugins_url( 'assets/css/images/menu-icon@2x.png', $this->filename ),
      ]
    );
  }

  private function create_post_type_dates() {
     register_post_type('dates',
        [
          'labels'      => [],
          'public'      => true,
          'has_archive' => true,
          'can_export'  => true,
    			'hierarchical' => true,
          'show_ui'     => false,
          'exclude_from_search' => true,
          'taxonomies' => array(
            'category',
          ),
        ]
     );
  }

  private function create_post_type_artists() {
      $label_names = [
        'name'          => __('Artists', $this->plugin_slug, 'event-creator'),
        'singular_name' => __('Artist', $this->plugin_slug, 'event-creator'),
        'add_new'       => __('New Artist', $this->plugin_slug, 'event-creator'),
        'add_new_item'       => __('Create new Artist', $this->plugin_slug, 'event-creator'),
        'edit_item'       => __('Edit Artist', $this->plugin_slug, 'event-creator'),
        'view_item'       => __('View Artist', $this->plugin_slug, 'event-creator'),
        'view_items'       => __('View all Artists', $this->plugin_slug, 'event-creator'),
        'search_items'       => __('Search Artists', $this->plugin_slug, 'event-creator'),
        'not_found'       => __('No Artists found', $this->plugin_slug, 'event-creator'),
        'not_found_in_trash'       => __('No Artists found in Trash', $this->plugin_slug, 'event-creator'),
        'all_items'       => __('Artists', $this->plugin_slug, 'event-creator'),
        'archives'       => __('Artists Archives', $this->plugin_slug, 'event-creator'),
        'attributes'       => __('Artist Attributes', $this->plugin_slug, 'event-creator'),
      ];
     register_post_type('artists',
        [
          'labels'      => $label_names,
          'public'      => true,
          'has_archive' => true,
          'can_export'  => false,
          'show_in_nav_menus' => true,
    			'hierarchical' => true,
          'supports' => [
            'title',
            'thumbnail',
          ],
          'show_ui'     => true,
          'show_in_menu' => 'edit.php?post_type=events',
        ]
     );
  }

  private function create_post_type_venues() {
      $label_names = [
        'name'          => __('Venues', $this->plugin_slug, 'event-creator'),
        'singular_name' => __('Venue', $this->plugin_slug, 'event-creator'),
        'add_new'       => __('New Venue', $this->plugin_slug, 'event-creator'),
        'add_new_item'       => __('Create new Venue', $this->plugin_slug, 'event-creator'),
        'edit_item'       => __('Edit Venue', $this->plugin_slug, 'event-creator'),
        'view_item'       => __('View Venue', $this->plugin_slug, 'event-creator'),
        'view_items'       => __('View all Venues', $this->plugin_slug, 'event-creator'),
        'search_items'       => __('Search Venues', $this->plugin_slug, 'event-creator'),
        'not_found'       => __('No Venues found', $this->plugin_slug, 'event-creator'),
        'not_found_in_trash'       => __('No Venues found in Trash', $this->plugin_slug, 'event-creator'),
        'all_items'       => __('Venues', $this->plugin_slug, 'event-creator'),
        'archives'       => __('Venues Archives', $this->plugin_slug, 'event-creator'),
        'attributes'       => __('Venue Attributes', $this->plugin_slug, 'event-creator'),
      ];
     register_post_type('venues',
        [
          'labels'      => $label_names,
          'public'      => true,
          'has_archive' => true,
          'can_export'  => false,
    			'hierarchical' => true,
          'supports' => [
            'title',
            'thumbnail',
          ],
          'show_ui'     => true,
          'show_in_menu' => 'edit.php?post_type=events',
        ]
     );
  }

  public function create_event_type_taxonomy() {
    register_taxonomy(
  		'event-type',
  		'post',
  		array(
  			'label' => __( 'Event Categories', $this->plugin_slug, 'event-creator' ),
  			'hierarchical' => true,
  		)
  	);
  }

  private function get_default_event_types() {
    return array(
      // 'cabaret' => __('Cabaret', $this->plugin_slug),
      // 'theatre' => __('Theatre', $this->plugin_slug),
      // 'festival' => __('Festival', $this->plugin_slug),
      // 'dance' => __('Dance', $this->plugin_slug),
      // 'performance' => __('Performance', $this->plugin_slug),
      // 'children' => __('For children', $this->plugin_slug),
    );
  }

  public function create_default_event_types() {
    foreach( $this->get_default_event_types() as $slug => $term) {
      if (! term_exists( $term, 'event-type' ) ) {
        wp_insert_term( $term, 'event-type', array(
          'slug' => $slug,
          )
        );
      }
    }
  }
}
