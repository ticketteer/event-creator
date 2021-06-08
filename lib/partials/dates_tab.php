<?php global $ticketteer_enabled; ?>
<div class="dates-wrap">
  <div class="dates-header">

    <!-- list -->
    <div id="event-creator-dates-list" class="dates-list">
      <?php
        $nonce = wp_create_nonce('event_creator_dates_nonce');
      ?>
      <button
        type="button"
        class="button-primary"
        id="event-creator-new-date"
        data-edit-text="<?php _e('New Date', $data['textdomain'], 'event-creator') ?>"
      >
        <?php _e('New Date', $data['textdomain'], 'event-creator') ?>
      </button>

      <p>&nbsp;</p>

      <table class="dates-list">
        <thead>
          <tr>
            <th colspan="3"><?php _e('Starts', $data['textdomain'], 'event-creator') ?></th>
            <th><?php _e('Venue', $data['textdomain'], 'event-creator') ?></th>
            <?php if ( $ticketteer_enabled ) : ?>
              <th><?php _e('Ticketteer', $data['textdomain'], 'event-creator') ?></th>
            <?php else : ?>
              <th></th>
            <?php endif; ?>
            <th><?php _e('Note', $data['textdomain'], 'event-creator') ?></th>
            <th><?php _e('Actions', $data['textdomain'], 'event-creator') ?></th>
          </tr>
        </thead>
        <tbody id="event-creator-dates-list-tbody"
          data-i18n-booking-list="<?php _e('Booking List', $data['textdomain'], 'event-creator'); ?>"
          data-i18n-edit="<?php _e('Edit Date', $data['textdomain'], 'event-creator'); ?>"
          data-i18n-cancelled="<?php _e('Cancelled', $data['textdomain'], 'event-creator') ?>"
          data-i18n-delete="<?php _e('Delete Date', $data['textdomain'], 'event-creator'); ?>"
        ></tbody>
      </table>

    </div>

    <!-- form -->
    <div id="event-creator-date-form" class="date-form" hidden>
      <div class="clear"></div>
      <div class="form" id="event-creator-general-form">

        <div class="row">
          <div class="control-label">
            <label for="_dates[starts_at_date]"><?php _e('Starts at', $data['textdomain'], 'event-creator'); ?></label>
          </div>
          <div class="form-control">
            <input type="hidden" name="_dates[starts_at_date]" id="event-creator-date-starts_at_date" value="<?php echo get_date_field('starts_at_date', $data['post']->ID); ?>" />
            <div class="datepicker" id="event-creator-datepicker"></div>
          </div>
        </div>
        <div class="row">
          <div class="control-label">
            <label for="_dates[starts_at_time]"><?php _e('Time', $data['textdomain'], 'event-creator'); ?></label>
          </div>
          <div class="form-control">
            <?php
              $start_time = get_date_field('starts_at_time', $data['post']->ID);
              if (empty($start_time) ) $start_time = get_option( 'default_start_time' );
            ?>
            <input class="control-field" type="text" name="_dates[starts_at_time]" id="event-creator-date-starts_at_time" value="<?= $start_time ?>" />
          </div>
        </div>
        <div class="row">
          <div class="control-label">
            <label for="_dates[note]"><?php _e('Note', $data['textdomain'], 'event-creator'); ?></label>
          </div>
          <div class="form-control">
            <input class="control-field" type="text" name="_dates[note]" id="event-creator-date-note" value="<?php echo get_date_field('note', $data['post']->ID); ?>" />
          </div>
        </div>

        <div class="row">
          <div class="control-label">
            <label for="_events[artist_name]"><?php _e('Venue', $data['textdomain'], 'event-creator'); ?></label>
          </div>
          <div class="form-control form-dropdown">
            <?php
              $venue_id = get_date_field('venue_id', $data['post']->ID);
              if (empty($venue_id) ) $venue_id = get_option( 'default_venue_id' );
              $venue_args = array(
                'sort_order' => 'ASC',
                'sort_column' => 'post_title',
                'post_type' => 'venues',
                'id' => 'event-creator-date-venue_id',
                'name' => '_dates[venue_id]',
                'selected' => $venue_id,
                'show_option_none' => __('No venue is linked', $data['textdomain'], 'event-creator')
              );
              wp_dropdown_pages( $venue_args );
            ?>
          </div>
        </div>

        <div class="row">
          <div class="control-label">
            <label for="_dates[cancelled]"><?php _e('Cancelled', $data['textdomain'], 'event-creator'); ?></label>
          </div>
          <div class="form-control form-checkbox">
            <?php $cancelled = get_date_field('cancelled', $data['post']->ID); ?>
            <input
              type="checkbox"
              name="_dates[cancelled]"
              value="1"
              id="event-creator-date-cancelled"
            />
          </div>
        </div>

        <div class="row">
          <div class="control-label">
            <label for="_dates[premiere]"><?php _e('Premiere', $data['textdomain'], 'event-creator'); ?></label>
          </div>
          <div class="form-control form-checkbox">
            <?php $cancelled = get_date_field('premiere', $data['post']->ID); ?>
            <input
              type="checkbox"
              name="_dates[premiere]"
              value="1"
              id="event-creator-date-premiere"
            />
          </div>
        </div>

        <div class="row">
          <button
            type="button"
            class="button-primary"
            id="event-creator-save-date-btn"
            data-nonce="<?php echo $nonce ?>"
            data-edit-text="<?php _e('Save Event', $data['textdomain'], 'event-creator') ?>"
            data-create-text="<?php _e('Create Event', $data['textdomain'], 'event-creator') ?>"
            data-post-id="<?php echo $data['post']->ID ?>"
          >
            <?php _e('Create Event', $data['textdomain'], 'event-creator') ?>
          </button>
        </div>

      </div>
    </div>
  </div>
</div>
