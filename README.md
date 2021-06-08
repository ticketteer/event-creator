# Event Creator

Contributors: ticketteer
Tags: tickets, event, manage, show, theatre, sell, credit card
Requires at least: 3.7
Tested up to: 5.7
Stable tag: 2.0.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Enables event management capabilities to list and manage events (shows) on your website

## Description

Event Creator is an event management plugin to list chronological events and
links them with venues, artists and dates. It is especially meant for theatres,
artists.

It allows you to link your Wordpress page to Ticketteer (https://ticketteer.com)

- an online ticket management system. This will sync events and dates and provide
  feedback about booking status of your events from Ticketteer.

## Features

- Events - Manage your events
- Dates - Add dates to events
- Venues - Manage venues and link them to dates
- Artists - Manage artists / ensembles / participants of your event and link them
  to it.

## Installation

1. Unzip event-creator.zip to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

This should give you additional menu entries in your wordpress admin page.

## Usage

#### `get_dates_query()`

loads dates (custom post type) into wordpress' main loop.

Example:

```php
<?php
get_dates_query(); // load dates into wordpress main loop
while( have_posts() ): the_post();
?>
<h1><?php the_title(); ?></h1>
<p>Subtitle: <?php echo get_event_field('subtitle') ?></p>
<p>Venue: <?php echo get_venue_field('name'); ?></p>
<p>Artist: <?php echo get_artist_field('name'); ?></p>

<?php endwhile; ?>
```

`get_the_event([$post_id])` - get an event to an according date post type.
If no `$post_id` is passed, the_post() loop object will be used.

`get_the_venue([$post_id])` - get the associated venue for the date post type.
If no `$post_id` is passed, the_post() loop object will be used.

### checking free tickets

`tt.checkDate(dateId)`
dateId: ticketteer_date_id
You can get ticketteer_date_id by php-ing:
`get_date_field('ticketteer_date_id', $date->ID);`

Example:

```
tt.checkDate('5a0bf7e90b4aa10001fdc93f')
  .then(function(result){
    // example:
    // {
    //   '5a0bf7e90b4aa10001fdc93f': {
    //      available: 5,
    //      rest: 10,
    //   }
    // }
  });
```

== Changelog ==

= 1.0 =

- Initial commit with main functionality.

## Rewrite /artists /events with your own paths

RewriteRule ^kuenstler/(.\*)$ /artists/$1 [NC,P,L]
