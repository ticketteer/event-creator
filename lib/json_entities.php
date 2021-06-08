<?php

namespace Ticketteer\EventCreator;

/**
 * gets a formatted response for a single date
 *
 * @param  int|sting $post_id id of the date
 *
 * @return array formatted response
 *
 * @since 1.0.0
 *
 */
function get_date_entity($post_id){
  $date = get_post($post_id);
  return formatted_date_entity($date);
}

/**
 * gets a formatted response for an array fo dates
 *
 * @param  array $dates (array of WP_Post objects)
 *
 * @return array formatted response
 *
 * @since 1.0.0
 *
 */
function get_date_entities($dates){
  $response = [];
  foreach( $dates as $date ){
    array_push($response, formatted_date_entity($date));
  }
  return $response;
}

/**
 * converts a $wp_post (date) result into an array containing all meta fields
 *
 * @param  object $wp_date [description]
 *
 * @return array  containing meta fields (in 'meta' key ) and post values
 *
 * @since 1.0.0
 *
 */
function formatted_date_entity($wp_date){
  $custom = get_post_custom($wp_date->ID);
  $date = $wp_date->to_array();
  $date['meta'] = array();
  foreach( $custom as $key => $value ){
    if (sizeof($value) > 0 ) {
      if ($key == 'venue_id'){
        $venue = get_post($value[0]);
        if ($venue) {
          $date['meta']['venue_name'] = $venue->post_title;
          $date['meta']['venue_id'] = $value[0];
        }
      } else if ($key == 'ticketteer_date_id'){
        $date['meta']['ticketteer_link'] = 'https://app.ticketteer.com/orders/' . $value[0];
      } else if ($key == 'starts_at'){
        $d = new \DateTime();
        $d->setTimestamp($value[0]);
        $date['meta']['starts_at_weekday'] = $d->format('l');
        $date['meta']['formatted_starts_at_date'] = $d->format('d.m.Y');
        $date['meta']['formatted_starts_at_time'] = $d->format('G:i');
        $date['meta']['starts_at'] = $d->format(\DateTime::ISO8601);
      } else {
        $date['meta'][$key] = $value[0];
      }
    }
  }
  return $date;
}
