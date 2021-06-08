<?php

namespace Ticketteer\EventCreator;

add_action( 'event_creator_venue_saved', __NAMESPACE__ . '\sync_venue' );
add_action( 'event_creator_date_saved', __NAMESPACE__ . '\sync_date' );
add_action( 'event_creator_event_saved', __NAMESPACE__ . '\sync_event' );
add_action( 'admin_notices', __NAMESPACE__ . '\show_sync_errors' );
add_action( 'admin_notices', __NAMESPACE__ . '\show_sync_info' );

function sync_event($post) {
    error_log(" sync event!");

  global $pagenow;
  if ( in_array( $pagenow, array( 'post-new.php' ) ) ) {
    return;
  }

  $body = array(
    'sync_id' => $post->ID,
    'title' => $post->post_title,
    'subtitle' => get_event_field('subtitle', $post->ID),
    'description' => get_the_excerpt($post->ID),
    'book_until_min_before' => get_option( 'default_book_until_min' ),
    'book_until_seats_perc' => get_option( 'default_book_until_perc' ),
    'price_group' => get_event_field('price_group', $post->ID),
    'is_public' => true,
  );
  $body = sync_ticketteer($post, $body, 'lineup_events');
  if (isset($body) && $body->lineup_event) {
    \update_post_meta( $post->ID, 'ticketteer_event_id', $body->lineup_event->id );
  }
}

function sync_date($post) {
  $starts = get_date_field('starts_at', $post->ID)->format('U');
  $starts -= get_option('gmt_offset') * 3600; // fix timezone offset errors from wordpress
  $cancelled = get_date_field('cancelled', $post->ID);
  $premiere = get_date_field('premiere', $post->ID);
  $body = array(
    'sync_id' => $post->ID,
    'starts_at' => $starts,
    'cancelled' => $cancelled == '1',
    'premiere' => $premiere == '1',
    'lineup_event_sync_id' => get_date_field('event_id', $post->ID),
    'lineup_org_sync_id' => get_date_field('venue_id', $post->ID),
    'note' => get_date_field('note', $post->ID),
  );
  $body = sync_ticketteer($post, $body, 'lineup_dates');
  if (isset($body) && $body->lineup_date) {
    \update_post_meta( $post->ID, 'ticketteer_date_id', $body->lineup_date->id );
  }
}

function sync_venue($post) {
  $body = array(
    'sync_id' => $post->ID,
    'seats' => get_venue_field('seats', $post->ID),
    'name' => $post->post_title,
    'description' => $post->post_content,
  );
  sync_ticketteer($post, $body, 'lineup_orgs');
}

/**
 * add error to be trnasported to admin_notices
 *
 * @param string $msg message to be shown
 *
 * @since 1.0.0
 *
 */
function add_error( $msg ) {
  $user_id = get_current_user_id();
  set_transient("event-creator-tt-sync-errors-${user_id}", $msg, 45);
}

/**
 * add info to be transported to admin_notices
 *
 * @param string $msg the message to be shown
 *
 * @since 1.0.0
 *
 */
function add_info( $msg ) {
  $user_id = get_current_user_id();
  set_transient("event-creator-tt-sync-info-${user_id}", $msg, 45);
}

function sync_ticketteer($post, $body_args, $type) {
  $start = microtime(true);
  $ec = EventCreator::get_instance();

  $response = wp_remote_request( $ec->api_endpoint . $type . '/sync',
    array(
      'method'     => 'POST',
      'headers'    => array(
        'X-PRIVATE-KEY' => get_option( 'ticketteer-key' )
      ),
      'body'       => $body_args,
    )
  );

    error_log("response is" . var_dump($response));

  if (is_array($response) && $response['response'] && $response['response']['code']) {
    $code = $response['response']['code'];
    if ($code == 404) {
      add_error('Not found');
    }
    else if ($code == 401) {
      add_error('Ticketteer Key is invalid (please check Event Creator -> Settings)');
    }
    else if ($code == 400 || $code == 409) {
      add_error('Validation error: ' . $response['response']['message']);
    }
    else if ($code == 419) {
      if ( strpos($response['response']['message'], 'Lineup org not found') >= 0 ) {
        add_error('Venue has not been synced yet. Go to venue and save it once more');
      } elseif ( strpos($response['response']['message'], 'event not found') >= 0 ) {
        add_error('Your event is out of sync. Save it once more');
      } else {
        add_error('Something went wrong error: ' . $response['response']['message']);
      }
    }
    else if ($code == 201) {
      $time_elapsed_secs = microtime(true) - $start;
      $body = json_decode(wp_remote_retrieve_body($response));
      $text = process_response_text($type, $body);
      add_info( $text . ' (in ' . number_format($time_elapsed_secs, 3) . ' seconds)');
      return $body;
    }
  }
  else if (!is_array($response) && get_class($response) == 'WP_Error') {
    add_error( $response->get_error_message() );
  }
}

function process_response_text($type, $body) {
  $ec = EventCreator::get_instance();
  if ($type == 'lineup_orgs') {
    $url = $ec->app_url . '/venues/' . $body->lineup_org->id;
    $text = "successfully synced <a target=\"_blank\" href=\"" . $url . "\">" . $body->lineup_org->name . "</a>";
  } else if ($type == 'lineup_events') {
    $url = $ec->app_url . '/lineup/' . $body->lineup_event->id . '/';
    $text = "successfully synced <a target=\"_blank\" href=\"" . $url . "\">" . $body->lineup_event->title . "</a>";
  } else if ($type == 'lineup_dates') {
    $url = $ec->app_url . '/lineup/' . $body->lineup_date->lineup_event_id . '/dates/' . $body->lineup_date->id;
    $text = "successfully synced <a target=\"_blank\" href=\"" . $url . "\">" . date_create($body->lineup_date->starts_at)->format('d. m. Y') . " " . $body->lineup_date->title . "</a>";
  }
  return $text;
}

function show_sync_errors() {
  if (defined('DOING_AJAX') && DOING_AJAX) return;
  $user_id = get_current_user_id();
  $msg = get_transient("event-creator-tt-sync-errors-${user_id}");
  if ( $msg ) {?>
    <div class="error">
      <p><span class="ticketteer-text-logo">Ticke<span class="ticketteer-tt">tt</span>eer</span> Synchronization failed: <?php echo $msg; ?></p>
    </div><?php
    delete_transient("event-creator-tt-sync-errors-${user_id}");
  }
}

function show_sync_info() {
  if (defined('DOING_AJAX') && DOING_AJAX) return;
  $user_id = get_current_user_id();
  $msg = get_transient("event-creator-tt-sync-info-${user_id}");
  if ( $msg ) {?>
    <div class="updated notice notice-success is-dismissible">
      <p><span class="ticketteer-text-logo">Ticke<span class="ticketteer-tt">tt</span>eer</span> <?php echo $msg; ?></p>
    </div><?php
    delete_transient("event-creator-tt-sync-info-${user_id}");
  }
}
