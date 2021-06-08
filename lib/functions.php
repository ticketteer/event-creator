<?php

/**
 * return args and filter for get_dates and get_dates_query
 *
 * array['from']          date   start date where to start from
 *                                 defaults to null
 * array['to']            date   end date (practical for archives)
 *                                 defaults to null
 * array['asc']             bool   default true
 *
 * @param  array $filter an array of filter attributes
 * @return array of arguments for a valid wp_query
 *
 * @since 1.0.0
 *
 */
function get_dates_args(array $filter) {
  $filter['asc'] = isset($filter['asc']) ? $filter['asc'] : true;
  $meta_query = array();
  if ( isset($filter['from']) ) {
    array_push( $meta_query, array(
      'key' => 'starts_at',
      'value' => $filter['from'],
      'compare' => '>=',
      )
    );
  }
  if ( isset($filter['to']) ) {
    array_push( $meta_query, array(
      'key' => 'starts_at',
      'value' => $filter['to'],
      'compare' => '<=',
      )
    );
  }
  $args = array(
  	'post_type' => 'dates',
    'post_status' => array( 'publish' ),
    'meta_query' => $meta_query,
    'meta_key' => 'starts_at',
    'orderby' => 'meta_value_num',
    'order' => $filter['asc'] ? 'ASC' : 'DESC',
    'posts_per_page' => -1,
  );
  return $args;
}

function get_event_relation($fieldname, $post_id = null) {
  $id = isset($post_id) ? $post_id : get_the_ID();
  $field = get_post_meta( $id, $fieldname, true );
  if (!$field) return null;
  return get_post($field);
}

/**
 * return args and return all dates associated with event
 *
 * array['from']          date   start date where to start from
 *                                 defaults to null
 * array['to']            date   end date (practical for archives)
 *                                 defaults to null
 * array['asc']             bool   default true
 *
 * @param  array $filter an array of filter attributes
 * @return array of arguments for a valid wp_query
 *
 * @since 1.0.0
 *
 */
function get_event_dates($filter = array(), $post_id = null) {
  $id = isset($post_id) ? $post_id : get_the_ID();
  $filter['asc'] = isset($filter['asc']) ? $filter['asc'] : true;
  $meta_query = array();
  array_push( $meta_query,
    array(
      'key' => 'event_id',
      'value' => $id,
    )
  );
  if ( isset($filter['from']) ) {
   array_push( $meta_query, array(
     'key' => 'starts_at',
     'value' => $filter['from'],
     'compare' => '<=',
     )
   );
  }
  if ( isset($filter['to']) ) {
   array_push( $meta_query, array(
     'key' => 'starts_at',
     'value' => $filter['to'],
     'compare' => '<=',
     )
   );
  }
  $args = array(
   'post_type' => 'dates',
   'post_status' => array( 'publish' ),
   'meta_query' => $meta_query,
   'meta_key' => 'starts_at',
   'orderby' => 'meta_value_num',
   'order' => $filter['asc'] ? 'ASC' : 'DESC',
   'posts_per_page' => -1,
  );
  return get_posts( $args );
}

/**
 * receive a list of future dates for current or given artist
 *
 * array['from']          date   start date where to start from
 *                                 defaults to null
 * array['to']            date   end date (practical for archives)
 *                                 defaults to null
 * array['asc']             bool   default true
 *
 * @param  array $filter an array of filter attributes
 * @return array of arguments for a valid wp_query
 *
 * @since 1.0.0
 *
 */
function get_artist_dates($filter = array(), $post_id = null) {
  $id = isset($post_id) ? $post_id : get_the_ID();
  $filter['asc'] = isset($filter['asc']) ? $filter['asc'] : true;

  $event_args = array(
    'post_type' => 'events',
    'post_status' => array( 'publish' ),
    'meta_query' => array(
      array(
        'key' => 'artist_id',
        'value' => $id,
      ),
    ),
    'posts_per_page' => -1,
    'fields' => 'ids',
  );

  $event_ids = get_posts($event_args);

  $dates_args = array(
    'post_type' => 'dates',
    'post_status' => array( 'publish' ),
    'meta_key' => 'starts_at',
    'orderby' => 'meta_value_num',
    'order' => $filter['asc'] ? 'ASC' : 'DESC',
    'posts_per_page' => -1,
    'meta_query' => array(
      array(
        'key' => 'event_id',
        'value' => $event_ids,
        'compare' => 'IN',
      ),
    ),
  );

  return get_posts($dates_args);
}

/**
 * get all dates filtered by $filter
 *
 * @param  array  $filter array of filter criteria
 * @return [type]         [description]
 */
function get_dates_query($filter = array()) {
  return query_posts( get_dates_args($filter) );
}

/**
 * receive all dates by filter as an array
 *
 * @param  array  $filter for filter options see documentation of get_dates_args
 *
 * @return array $dates (custom post type)
 *
 * @since 1.0.0
 *
 */
function get_dates(array $filter = array()) {
  return get_posts( get_dates_args( $filter ) );
}

/**
 * get the event for this $post if available
 *
 * @return WP_Post event object (WP_Post custom post type)
 *
 * @since 1.0.0
 *
 */
function get_the_event($post_id = null) {
  $id = isset($post_id) ? $post_id : get_the_ID();
  $event_id = get_post_meta($id, 'event_id', true);
  if ( !empty($event_id) ) return get_post($event_id);
}

/**
 * receives the artist for an event
 *
 * @param  int $event_id must be an event id not any post id
 *
 * @return WP_Post  artist (custom post type)
 *
 * @since 1.0.0
 *
 */
function get_the_artist($post_id = null) {
  $id = isset($post_id) ? $post_id : get_the_ID();
  $artist_id = get_post_meta($id, 'artist_id', true);
  if ( !empty($artist_id) ) return get_post($artist_id);
}

/**
 * get the venue for this $post if available
 *
 * @param  int $post_id optional $post_id
 *
 * @return WP_Post venue object (WP_Post custom post type)
 *
 * @since 1.0.0
 *
 */
function get_the_venue($post_id = null) {
  $id = isset($post_id) ? $post_id : get_the_ID();
  $venue_id = get_post_meta($id, 'venue_id', true);
  if ( !empty($venue_id) ) return get_post($venue_id);
}

/**
 * get a date field
 * @param  string $fieldname the name of the field to get
 *
 * @param  int|string $post_id the id of the post, defaults to the_post_ID()
 *
 * @return mixed the value of the requested field
 *
 * @since 1.0.0
 */
function get_date_field($fieldname, $id = null) {
  $id = isset($id) ? $id : get_the_ID();
  if ($fieldname == 'starts_at') {
    $field_value = get_post_meta( $id, $fieldname, true );
    $d = new DateTime();
    $d->setTimestamp($field_value);
    return $d;
  }
  return get_post_meta( $id, $fieldname, true );
}

/**
 * echos output of get_date_field. See get_date_field options for documentation
 *
 * @since 1.0.0
 *
 */
function the_date_field($fieldname, $id = null) {
  echo get_date_field($fieldname, $id);
}

/**
 * get a venue field
 * @param  string $fieldname the name of the field to get
 *
 * @param  int|string $post_id the id of the post, defaults to the_post_ID().
 *                    if $post is a date, $venue will automatically be retrieved.
 *
 * @return mixed the value of the requested field
 *
 * @since 1.0.0
 */
function get_venue_field($fieldname, $post_id = null) {
  $id = isset($post_id) ? $post_id : get_the_ID();
  if (!$post_id && get_post()->post_type == 'dates') {
    $id = get_post_meta( $id, 'venue_id', true );
    if ( !isset($id) ){
      error_log('failed to retrieve get_venue_field for ' . $fieldname);
      return null;
    }
  }
  if ($fieldname == 'name') return get_post($id)->post_title;
  if ($fieldname == 'title') return get_post($id)->post_title;
  if ($fieldname == 'first_date') {
    $field_value = get_post_meta( $id, $fieldname, true );
    $d = new DateTime();
    $d->setTimestamp($field_value);
    return $d;
  }
  if ($fieldname == 'last_date') {
    $field_value = get_post_meta( $id, $fieldname, true );
    $d = new DateTime();
    $d->setTimestamp($field_value);
    return $d;
  }
  return get_post_meta( $id, $fieldname, true );
}

/**
 * get an event field
 * @param  string $fieldname the name of the field to get
 *
 * @param  int|string $post_id the id of the post, defaults to the_post_ID().
 *                    if $post is a date, $venue will automatically be retrieved.
 *
 * @return mixed the value of the requested field
 *
 * @since 1.0.0
 */
function get_event_field($fieldname, $post_id = null) {
  $id = isset($post_id) ? $post_id : get_the_ID();
  if (!$post_id && get_post()->post_type == 'dates') {
    $id = get_post_meta( $id, 'event_id', true );
    if ( !isset($id) ){
      error_log('failed to retrieve get_event_field for ' . $fieldname);
      return null;
    }
  }
  if ($fieldname == 'first_date') {
    $field_value = get_post_meta( $id, $fieldname, true );
    if (!$field_value) return null;
    $d = new DateTime();
    $d->setTimestamp($field_value);
    return $d;
  }
  if ($fieldname == 'last_date') {
    $field_value = get_post_meta( $id, $fieldname, true );
    if (!$field_value) return null;
    $d = new DateTime();
    $d->setTimestamp($field_value);
    return $d;
  }
  if ($fieldname == 'name') return get_post($id)->post_title;
  if ($fieldname == 'title') return get_post($id)->post_title;
  return get_post_meta( $id, $fieldname, true );
}

/**
 * receive a list of tags for the date and optionally include
 * event tag names
 *
 * @param  int $date_id the id of the date
 *
 * @param  boolean $
 *
 * @return array wp_categories results
 *
 * @since 1.0.0
 *
 */
function get_date_tags($post_id = null, $include_event_tags) {
  $id = isset($post_id) ? $post_id : get_the_ID();
  $tags = array(); //get_categories($id);
  if (isset ($include_event_tags) && $include_event_tags ) :
    $event_id = get_post_meta($id, 'event_id', true);
    if ( isset($event_id) ) :
      $event_types = get_the_terms($event_id, 'event-type');
      if ($event_types) {
        $tags = array_merge($tags, $event_types);
      }
    endif;
  endif;
  return $tags;
}

/**
 * get an artist field
 * @param  string $fieldname the name of the field to get
 *
 * @param  int|string $post_id the id of the post, defaults to the_post_ID().
 *                    if $post is a date, $venue will automatically be retrieved.
 *
 * @return mixed the value of the requested field
 *
 * @since 1.0.0
 */
function get_artist_field($fieldname, $post_id = null) {
  $id = isset($post_id) ? $post_id : get_the_ID();
  if (!$post_id && get_post()->post_type == 'dates') {
    $event_id = get_post_meta( $id, 'event_id', true );
    if ( !isset($id) ) return null;
    $id = get_post_meta( $event_id, 'artist_id', true );
  }
  if ($fieldname == 'name') return get_post($id)->post_title;
  if ($fieldname == 'title') return get_post($id)->post_title;
  return get_post_meta( $id, $fieldname, true );
}

function ec_get_sold_out_text($data) {
  $sold_out_text = esc_html__('Sold out', $data['textdomain'], 'event-creator');
  if (!empty($data['fields']['sold_out_text'])) {
    $sold_out_text = $data['fields']['sold_out_text'];
  } elseif (!empty(get_option('default_sold_out_text'))) {
    $sold_out_text = get_option('default_sold_out_text');
  }
  return $sold_out_text;
}

function ec_get_rest_seats_text($data) {
  $rest_seats_text = esc_html__('Rest seats', $data['textdomain'], 'event-creator');
	if (!empty($data['fields']['rest_seats_text'])) {
		$rest_seats_text = $data['fields']['rest_seats_text'];
	} elseif (!empty(get_option('default_rest_seats_text'))) {
		$rest_seats_text = get_option('default_rest_seats_text');
	}
  return $rest_seats_text;
}
