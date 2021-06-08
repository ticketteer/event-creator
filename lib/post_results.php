<?php
//
//namespace Ticketteer\EventCreator;
//
//add_filter( 'posts_results', __NAMESPACE__ . '\add_event_creator_results', 10, 2 );
//
///**
// * extend the dates return object
// *
// * @param array $posts the results for the date posts
// *
// * @return array $posts extended with additional data like event, venue, etc.
// *
// * @since 1.0.0
// *
// */
//function add_event_creator_results( array $posts ) {
//  foreach ( $posts as $post ) {
//    if ( $post->post_type == 'dates' ) extend_date_results($post);
//  }
//  return $posts;
//}
//
///**
// * extend a $date object with additional data like event and venue
// *
// * @param  WP_Post &$post [description]
// *
// * @return WP_Post the modified WP_Post object
// *
// * @since 1.0.0
// *
// */
//function extend_date_results( \WP_Post &$post ) {
//  $starts_at = get_post_meta( $post->ID, 'starts_at', true );
//  if ($starts_at) {
//    // $post->starts_at = new DateTime();
//    // $post->starts_at->setTimestamp($starts_at);
//  }
//  $event_id = get_post_meta($post->ID, 'event_id', true);
//  if ($event_id) {
//    $post->event = get_post( $event_id );
//  }
//  $venue_id = get_post_meta($post->ID, 'venue_id', true);
//  if ($venue_id) {
//    $post->venue = get_post( $venue_id );
//  }
//}
//
