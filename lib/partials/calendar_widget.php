<?php $dates = get_dates(); ?>
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

          $tags = get_date_tags($date->ID, true);

          if ( isset($starts) ) :

            if ( $last_day != $starts->format('d-m-Y') ) :
              if ( $last_day != null ){ ?></div></div> <?php }
              $last_day = $starts->format('d-m-Y');
              ?>
              <div class="event-creator-day-row">
                <div class="event-creator-day-num">
                  <div class="event-creator-day-title<?= $cancelled ? ' cancelled' : '' ?>">
                    <?php echo date_i18n('d', $starts->getTimestamp()); ?>
                  </div>
                  <div class="event-creator-day-subtitle">
                    <?php echo date_i18n('M', $starts->getTimestamp()); ?>
                  </div>
                </div>
                <div class="event-creator-day-events">
              <?php
            endif; ?>
                  <div class="event-creator-day-event">
                    <div class="event-creator-day-details">
                      <div class="ticketteer-sold-out-text date-sold-out-<?= $ticketteer_date_id ?>" hidden>
                        <?= ec_get_sold_out_text($data); ?>
                      </div>
                      <div class="ticketteer-rest-seats-text date-rest-seats-<?= $ticketteer_date_id ?>" hidden>
                        <?= ec_get_rest_seats_text($data); ?>
                      </div>
                      <div class="event-creator-weekday-name<?= $cancelled ? ' cancelled' : '' ?>">
                        <?php echo date_i18n('l', $starts->getTimestamp()); ?>
                      </div>
                      <div class="event-creator-weekday-time<?= $cancelled ? ' cancelled' : '' ?>">
                        <?php echo $starts->format('H:i'); ?>
                      </div>
                      <?php if (isset($tags) && sizeof($tags) > 0 ) : ?>
                        <?php foreach( $tags as $tag ): ?>
                          <span class="event-creator-date-tag">
                            <?php echo $tag->name; ?>
                          </span>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </div>
                    <div class="event-creator-day-title">
                      <?php if (isset($artist)) : ?>
                        <div class="event-creator-artist">
                          <a href="<?php echo get_permalink($artist->ID); ?>">
                            <?php echo $artist->post_title; ?>
                          </a>
                        </div>
                      <?php endif; ?>
                      <h2 class="<?= $cancelled ? ' cancelled' : '' ?>">
                        <a href="<?= $artist ? get_permalink($artist->ID) : '' ?>">
                          <?php echo $event->post_title ?>
                        </a>
                      </h2>
                      <?php if ( isset($note) ): ?>
                        <div class="event-creator-date-note">
                          <?php echo $note; ?>
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
                        <a target="_blank" class="ticketteer-book-btn date-<?= $ticketteer_date_id ?>" data-ticketteer-date-id="<?= $ticketteer_date_id ?>" href="https://book.ticketteer.com/<?php echo get_event_field('ticketteer_event_id', $event->ID); ?>/<?php echo $starts->format('YmdHi'); ?>">
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
    <?php endif; ?>
  <?php endforeach; ?>
</div><!-- close event-creator-day-events -->
</div><!-- close event-creator-day-row -->
<?php endif; ?>
