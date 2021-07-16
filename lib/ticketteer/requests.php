<?php

namespace Ticketteer\EventCreator;

add_action('event_creator_venue_saved', __NAMESPACE__ . '\sync_venue');
add_action('event_creator_date_saved', __NAMESPACE__ . '\sync_date');
add_action('event_creator_event_saved', __NAMESPACE__ . '\sync_event');
add_action('admin_notices', __NAMESPACE__ . '\show_sync_errors');
add_action('admin_notices', __NAMESPACE__ . '\show_sync_info');

function sync_event($post)
{
  global $pagenow;
  if (in_array($pagenow, array('post-new.php'))) {
    return;
  }

  $body = array(
    'ticketteer_id' => $post->ticketteer_event_id,
    'title' => $post->post_title,
    'subtitle' => get_event_field('subtitle', $post->ID),
    'description' => get_the_excerpt($post->ID),
    'book_until_min_before' => get_option('default_book_until_min'),
    'book_until_seats_perc' => get_option('default_book_until_perc'),
    'price_group' => get_event_field('price_group', $post->ID),
    'is_public' => true,
  );
  $body = sync_ticketteer($post, $body, 'events');
  error_log("event entity: " . $body->event->id);
  if (isset($body) && $body->event) {
    \update_post_meta($post->ID, 'ticketteer_event_id', $body->event->id);
  }
}

function sync_date($post)
{
  $starts = get_date_field('starts_at', $post->ID);
  $starts->setTimezone(new \DateTimeZone('UTC'));
  error_log("DATE STARTS AT " . print_r($starts->format('U'), true));

  $cancelled = get_date_field('cancelled', $post->ID);
  $premiere = get_date_field('premiere', $post->ID);
  $event_id = get_date_field('event_id', $post->ID);
  $location_id = get_date_field('venue_id', $post->ID);
  $body = array(
    'ticketteer_id' => $post->ticketteer_date_id,
    'starts_at' => $starts->format('U'),
    'cancelled' => $cancelled == '1',
    'premiere' => $premiere == '1',
    'seats' => get_venue_field('seats', $location_id),
    'event_id' => get_event_field('ticketteer_event_id', $event_id),
    'location_id' => get_venue_field('ticketteer_location_id', $location_id),
    'annotation' => get_date_field('note', $post->ID),
    'title' => get_event_field('title', $event_id),
    'subtitle' => get_event_field('subtitle', $event_id),
    'description' => get_the_excerpt($post->ID),
    'price_category_template_name' => get_event_field('price_group', $event_id),
    'on_sale' => true,
  );
  $body = sync_ticketteer($post, $body, 'dates');
  if (isset($body) && $body->date) {
    \update_post_meta($post->ID, 'ticketteer_date_id', $body->date->id);
  }
}

function sync_venue($post)
{
  $body = array(
    'ticketteer_id' => $post->ticketteer_location_id,
    'seats' => get_venue_field('seats', $post->ID),
    'name' => $post->post_title,
    'description' => $post->post_content,
  );
  $body = sync_ticketteer($post, $body, 'locations');
  if (isset($body) && $body->location) {
    \update_post_meta($post->ID, 'ticketteer_location_id', $body->location->id);
  }
}

/**
 * add error to be trnasported to admin_notices
 *
 * @param string $msg message to be shown
 *
 * @since 1.0.0
 *
 */
function add_error($msg)
{
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
function add_info($msg)
{
  $user_id = get_current_user_id();
  set_transient("event-creator-tt-sync-info-${user_id}", $msg, 45);
}

function sync_ticketteer($post, $body_args, $type)
{
  $start = microtime(true);
  $ec = EventCreator::get_instance();

  $response = wp_remote_request(
    $ec->api_endpoint . $type,
    array(
      'method'     => 'POST',
      'headers'    => array(
        'X-API-TOKEN' => get_option('ticketteer-key')
      ),
      'body'       => $body_args,
    )
  );

  if (is_array($response) && $response['response'] && $response['response']['code']) {
    $code = $response['response']['code'];
    if ($code == 404) {
      add_error('Not found');
    } else if ($code == 401) {
      add_error('Ticketteer Key is invalid (please check Event Creator -> Settings)');
    } else if ($code == 400 || $code == 409) {
      add_error('Validation error: ' . $response['response']['message']);
    } else if ($code == 419) {
      if (strpos($response['response']['message'], 'Lineup org not found') >= 0) {
        add_error('Venue has not been synced yet. Go to venue and save it once more');
      } elseif (strpos($response['response']['message'], 'event not found') >= 0) {
        add_error('Your event is out of sync. Save it once more');
      } else {
        add_error('Something went wrong error: ' . $response['response']['message']);
      }
    } else if ($code == 201) {
      $time_elapsed_secs = microtime(true) - $start;
      $body = json_decode(wp_remote_retrieve_body($response));
      $text = process_response_text($type, $body);
      add_info($text . ' (in ' . number_format($time_elapsed_secs, 3) . ' seconds)');
      return $body;
    }
  } else if (!is_array($response) && get_class($response) == 'WP_Error') {
    add_error($response->get_error_message());
  }
}

function process_response_text($type, $body)
{
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

function show_sync_errors()
{
  if (defined('DOING_AJAX') && DOING_AJAX) return;
  $user_id = get_current_user_id();
  $msg = get_transient("event-creator-tt-sync-errors-${user_id}");
  if ($msg) { ?>
    <div class="error">
      <p><span class="ticketteer-text-logo">Ticke<span class="ticketteer-tt">tt</span>eer</span> Synchronization failed: <?php echo $msg; ?></p>
    </div><?php
          delete_transient("event-creator-tt-sync-errors-${user_id}");
        }
      }

      function show_sync_info()
      {
        if (defined('DOING_AJAX') && DOING_AJAX) return;
        $user_id = get_current_user_id();
        $msg = get_transient("event-creator-tt-sync-info-${user_id}");
        if ($msg) { ?>
    <div class="updated notice notice-success is-dismissible">
      <p><span class="ticketteer-text-logo">Ticke<span class="ticketteer-tt">tt</span>eer</span> <?php echo $msg; ?></p>
    </div><?php
          delete_transient("event-creator-tt-sync-info-${user_id}");
        }
      }
