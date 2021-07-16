<?php $dates = get_artist_dates(); ?>
<?php $last_day = null; ?>
<?php if ( $dates ) : ?>
  <?php foreach ( $dates as $date ) :

    // main day loop
    $starts = get_date_field('starts_at', $date->ID);
    $event = get_the_event($date->ID);
    $artist = get_the_artist($event->ID);
    $cancelled = get_date_field('cancelled', $date->ID);
    $note = get_date_field('note', $date->ID);
    $ticketteer_date_id = get_date_field('ticketteer_date_id', $date->ID);
    $slug = get_option('ticketteer-slug');

    $tags = get_date_tags($date->ID, true);
    $show_tags = false;

    if ( isset($starts) ) : ?>
      <div class="event-creator-artist-row-outer">
        <div class="event-creator-artist-row">
          <div class="event-creator-artist-date<?= $cancelled ? ' cancelled' : '' ?>">
            <?= date_i18n('d. M Y H:i', $starts->getTimestamp()); ?> Uhr
          </div>
          <div class="event-creator-artist-event-title">
            <strong><?= get_the_title($event->ID); ?></strong>
            <?php if ( isset($note) ): ?>
              <div class="event-creator-date-note">
                <?php echo $note; ?>
              </div>
            <?php endif; ?>
          </div>
          <?php if ($show_tags && isset($tags) && sizeof($tags) > 0 ) : ?>
            <?php foreach( $tags as $tag ): ?>
              <span class="event-creator-date-tag">
                <?php echo $tag->name; ?>
              </span>
            <?php endforeach; ?>
          <?php endif; ?>
          <?php $ticketteer_id = get_event_field('ticketteer_event_id', $event->ID); ?>
          <?php if ( isset($ticketteer_id) ) : ?>
            <div class="event-creator-buy">
              <?php if ( $cancelled ) : ?>
                <a class="ticketteer-book-btn cancelled">
                  <?= esc_html__('Cancelled', $data['textdomain'], 'event-creator') ?>
                </a>
              <?php else : ?>
                <a class="ticketteer-book-btn sold-out date-sold-out-<?= $ticketteer_date_id ?>" hidden>
                  <?= ec_get_sold_out_text($data); ?>
                </a>
                <a class="ticketteer-book-btn rest-seats date-rest-seats-<?= $ticketteer_date_id ?>" hidden>
                  <?= ec_get_rest_seats_text($data); ?>
                </a>
                <a target="_blank" class="ticketteer-book-btn" href="https://shop.ticketteer.com/<?= $slug ?>/b/<?= $ticketteer_date_id ?>">
                  <?php
                    $buy_tickets_text = esc_html__('Buy Tickets', $data['textdomain'], 'event-creator');
                    if (empty($data['fields']['buy_tickets_text'])) {
                      $buy_tickets_text = get_option('default_buy_tickets_text');
                    } else {
                      $buy_tickets_text = $data['fields']['buy_tickets_text'];
                    }
                    echo $buy_tickets_text;
                  ?>
                </a>
              <?php endif; ?>
            </div>
          <?php endif; # ticketteer_id ?>
        </div>
      </div>
    <?php endif; # isset($starts) ?>
  <?php endforeach; ?>
<?php endif; ?>
