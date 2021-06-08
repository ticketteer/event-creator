<?php

namespace Ticketteer\EventCreator;

function add_event_cols_head($defaults) {
  $ec = EventCreator::get_instance();
  $new_order = array();
  foreach ( $defaults as $key => $title) {
    $new_order[$key] = $title;
    if ($key == 'title') {
      $new_order['artist_name'] = __('Artist', $ec->plugin_slug);
      $new_order['from'] = __('From', $ec->plugin_slug);
      $new_order['to'] = __('To', $ec->plugin_slug);
    }
  }
  return $new_order;
}

function add_event_cols($col_name, $post_id) {
  if ($col_name == 'artist_name') {
    $artist = get_the_artist($post_id);
    if ($artist) {
      echo $artist->post_title;
    }
  } elseif ($col_name == 'from') {
    $from = get_event_field('first_date');
    if ($from) {
      echo $from->format('l, d. F Y H:i');
    }
  } elseif ($col_name == 'to') {
    $from = get_event_field('last_date');
    if ($from) {
      echo $from->format('l, d. F Y H:i');
    }
  }
}

function sortable_event_cols($cols) {
  $cols['artist_name'] = 'artist_name';
  $cols['from'] = 'from';
  $cols['to'] = 'to';
  return $cols;
}

function artist_name_orderby($query) {
  if (!is_admin()) return;
  $order_by = $query->get('orderby');
  if ($order_by == 'artist_name') {
    $query->set('meta_key', 'artist_id');
    $query->set('orderby', 'meta_value_num');
  } elseif ($order_by == 'from') {
    $query->set('meta_key', 'first_date');
    $query->set('orderby', 'meta_value_num');
  } elseif ($order_by == 'to') {
    $query->set('meta_key', 'last_date');
    $query->set('orderby', 'meta_value_num');
  }
}

add_filter('manage_events_posts_columns', __NAMESPACE__ . '\add_event_cols_head');
add_filter('manage_events_posts_custom_column', __NAMESPACE__ . '\add_event_cols', 10, 2);
add_filter('manage_edit-events_sortable_columns', __NAMESPACE__ . '\sortable_event_cols' );
add_action('pre_get_posts', __NAMESPACE__ . '\artist_name_orderby' );
