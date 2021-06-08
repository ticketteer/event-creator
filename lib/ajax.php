<?php

namespace Ticketteer\EventCreator;

add_action( 'wp_ajax_event_creator_create_date', __NAMESPACE__ . '\event_creator_ajax_create_date' );
add_action( 'wp_ajax_event_creator_list_dates', __NAMESPACE__ . '\event_creator_ajax_list_dates' );
add_action( 'wp_ajax_event_creator_delete_date', __NAMESPACE__ . '\event_creator_ajax_delete_date' );

/**
 * Fail with given message and status code
 * Immediately exits script
 *
 * @param  string $msg    A meaningful message to help interpret the error
 * @param  int $status status code
 *
 * @since 1.0.0
 *
 */
function fail_with( $msg, $status ) {
  http_response_code($status);
  echo $msg;
  die;
}

/**
 * Creates a new date for the given post
 *
 * @since 1.0.0
 */
function event_creator_ajax_create_date() {

  // Run a security check first.
  check_admin_referer( 'event_creator_dates_nonce', 'nonce' );

  $event_id      = absint( $_POST['event_id'] );
  if ( !isset($_POST['event_id']) ) fail_with('Missing event_id', 400);
  if ( !isset($_POST['meta']) ) fail_with('Missing meta field', 400);
  if ( !isset($_POST['meta']['venue_id']) ) fail_with('Missing venue', 400);
  if ( !isset($_POST['meta']['starts_at_date']) ) fail_with('Missing meta starts_at_date field', 400);
  if ( !isset($_POST['meta']['starts_at_time']) ) fail_with('Missing meta starts_at_time field', 400);
  if ( !preg_match('/\d\d:\d\d/', $_POST['meta']['starts_at_time']) ) fail_with('starts_at_time must be of format 00:00', 400);

  $event = get_post($event_id);
  if (!$event) fail_with('event_id not found', 404);

  $meta = [];
  $starts_at_date = $_POST['meta']['starts_at_date'];
  $starts_at_time = $_POST['meta']['starts_at_time'];
  $starts_str = $starts_at_date . ' ' . $starts_at_time . ':00';
  $starts_at = strtotime($starts_str);

  foreach( $_POST['meta'] as $key => $value) {
    $meta[$key] = $value;
  }

  $meta['starts_at'] = $starts_at;
  $meta['event_id'] = $event->ID;

  $data = array(
    'post_title' => $starts_at_date . ' ' . $starts_at_time . ' - ' . $event->post_title,
    'post_type' => 'dates',
    'post_status' => 'publish',
    'post_author' => get_current_user_id(),
    'meta_input' => $meta,
  );

  if (array_key_exists('date_id', $_POST) && isset($_POST['date_id'])) {
    $data['ID'] = $_POST['date_id'];
    $date_id = wp_update_post( $data );
  } else {
    $date_id = wp_insert_post( $data );
  }

  $first_date = get_event_field('first_date', $event_id);
  if (empty($first_date) || $first_date->getTimestamp() > $starts_at) {
    update_post_meta( $event_id, 'first_date', $starts_at );
  }

  $last_date = get_event_field('last_date', $event_id);
  if (empty($last_date) || $last_date->getTimestamp() < $starts_at) {
    update_post_meta( $event_id, 'last_date', $starts_at );
  }

  do_action( 'event_creator_date_saved', get_post($date_id) );

  wp_send_json_success(array_merge(extract_response_message(), get_date_entity($date_id)));
  die;

}

function extract_response_message() {
  $user_id = get_current_user_id();
  $message = array(
    'errors' => get_transient("event-creator-tt-sync-errors-${user_id}"),
    'info' => $msg = get_transient("event-creator-tt-sync-info-${user_id}"),
  );
  delete_transient("event-creator-tt-sync-errors-${user_id}");
  delete_transient("event-creator-tt-sync-info-${user_id}");
  return $message;
}

function event_creator_ajax_delete_date() {
  $date_id = $_GET['date_id'];
  wp_delete_post($date_id);
  wp_send_json_success();
  die;
}

/**
 * lists all dates for given event_id
 *
 * @return object response object including data: [dates]
 *
 * @since 1.0.0
 *
 */
function event_creator_ajax_list_dates() {
  $event_id = $_GET['event_id'];
  if ( !isset($event_id) ) fail_with('Missing event_id', 400);
  $dates = get_event_dates(array('asc' => true), $event_id);
  wp_send_json_success(get_date_entities($dates));
  die;
}
