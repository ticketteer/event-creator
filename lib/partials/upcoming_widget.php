<div class='event-creator-upcoming'>
  <?php if (!empty($data['fields']['title_text'])) : ?>
    <p class='event-creator-upcoming-title'>
      <label><?= $data['fields']['title_text'] ?>:</label>
    </p>
  <?php endif; ?>
  <?php
    $limit = 3;
    if ($data['fields'] && $data['fields']['limit_text']) {
      $limit = $data['fields']['limit_text'];
    }
  ?>
  <?php foreach ( get_posts( array(
      'post_type' => 'dates',
      'post_status' => array( 'publish' ),
      'meta_key' => 'starts_at',
      'orderby' => 'meta_value',
      'posts_per_page' => $limit,
      'order' => 'ASC',
      'meta_query' => array(
        array(
          'key' => 'starts_at',
          'value' => strtotime( date('c') ),
          'compare' => '>=',
          'type' => 'CHAR'
        ),
        array(
          'key' => 'cancelled',
          'value' => '1',
          'compare' => '!=',
        ),
      )
    ) ) as $date ):
      $event = get_the_event($date->ID);
      $starts = get_date_field('starts_at', $date->ID);
      $artist = get_the_artist($event->ID);
      $cancelled = get_date_field('cancelled', $date->ID);
      $note = get_date_field('note', $date->ID);
      $ticketteer_date_id = get_date_field('ticketteer_date_id', $date->ID);
  ?>
    <div class='event-creator-upcoming-row'>
      <div class="event-creator-inner-row">
        <div class='event-creator-upcoming-time'>
          <?= date_i18n("d. M.", $starts->getTimestamp())." &nbsp ".date_i18n("H:i", $starts->getTimestamp() ) ?>
        </div>
        <?php if ($artist) : ?>
          <div class="event-creator-upcoming-title">
            <h2>
              <a href="<?php echo get_permalink($artist->ID); ?>">
                <?php echo $artist->post_title; ?>
              </a>
            </h2>
            <div class="subtitle">
              <a href="<?php echo get_permalink($artist->ID); ?>">
                <?= $event->post_title ?>
              </a>
            </div>
            <?php if ($note) : ?>
              <div class="event-creator-upcoming-note">
                <?= $note ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="event-creator-buy">
        <?php if ( $cancelled ) : ?>
          <a class="ticketteer-book-btn cancelled">
            <?= esc_html__('Cancelled', $data['textdomain'], 'event-creator') ?>
          </a>
        <?php elseif ( $ticketteer_date_id ) : ?>
          <a class="ticketteer-book-btn sold-out date-sold-out-<?= $ticketteer_date_id ?>" hidden>
            <?= ec_get_sold_out_text($data); ?>
          </a>
          <a class="ticketteer-book-btn rest-seats date-rest-seats-<?= $ticketteer_date_id ?>" hidden>
            <?= ec_get_rest_seats_text($data); ?>
          </a>
          <a target="_blank"
            class="ticketteer-book-btn date-<?= $ticketteer_date_id ?>"
            data-ticketteer-date-id="<?= $ticketteer_date_id ?>"
            href="https://book.ticketteer.com/<?php echo get_event_field('ticketteer_event_id', $event->ID); ?>/<?php echo $starts->format('YmdHi'); ?>">
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
    </div>
  <?php endforeach; ?>
</div>
