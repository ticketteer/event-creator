<div class="event-creator">

  <?php if (isset($_GET['settings-updated'])) : ?>
    <br />
    <div id="message" class="updated">
      <p><strong><?= __('Settings saved.', $data['textdomain'], 'event-creator') ?></strong></p>
    </div>
  <?php endif; ?>

  <form method="post" class="form options-form" action="options.php">
    <?php settings_fields('event-creator-settings'); ?>
    <?php do_settings_sections('event-creator-settings'); ?>

    <div class="row">
      <div class="control-label">
        <label><?php _e('Ticketteer API Endpoint', $data['textdomain'], 'event-creator'); ?></label>
      </div>
      <div class="form-control">
        <input type="text" class="control-field" name="ticketteer-api-endpoint" value="<?php echo get_option('ticketteer-api-endpoint'); ?>" />
      </div>
    </div>

    <div class="row">
      <div class="control-label">
        <label><?php _e('Ticketteer Private Key', $data['textdomain'], 'event-creator'); ?></label>
      </div>
      <div class="form-control">
        <input type="text" class="control-field" name="ticketteer-key" value="<?php echo get_option('ticketteer-key'); ?>" />
      </div>
    </div>

    <div class="row">
      <div class="control-label">
        <label><?php _e('Default Begin Time', $data['textdomain'], 'event-creator'); ?></label>
      </div>
      <div class="form-control">
        <input type="text" class="control-field" style="width: 8em" name="default_start_time" value="<?php echo get_option('default_start_time'); ?>" />
      </div>
    </div>

    <div class="row">
      <div class="control-label">
        <label><?php _e('Booking until [min]', $data['textdomain'], 'event-creator'); ?></label>
      </div>
      <div class="form-control">
        <input type="number" min="0" max="1440" class="control-field control-field-num" name="default_book_until_min" value="<?php echo get_option('default_book_until_min'); ?>" />
      </div>
    </div>

    <div class="row">
      <div class="control-label">
        <label><?php _e('Booking until [%]', $data['textdomain'], 'event-creator'); ?></label>
      </div>
      <div class="form-control">
        <input type="number" min="0" max="100" class="control-field control-field-num" name="default_book_until_perc" value="<?php echo get_option('default_book_until_perc'); ?>" />
      </div>
    </div>

    <div class="row">
      <div class="control-label">
        <label><?php _e('[Buy-Tickets]-Text', $data['textdomain'], 'event-creator'); ?></label>
      </div>
      <div class="form-control">
        <input type="text" class="control-field" name="default_buy_tickets_text" value="<?php echo get_option('default_buy_tickets_text'); ?>" />
      </div>
    </div>

    <div class="row">
      <div class="control-label">
        <label><?php _e('[Sold-Out]-Text', $data['textdomain'], 'event-creator'); ?></label>
      </div>
      <div class="form-control">
        <input type="text" class="control-field" name="default_sold_out_text" value="<?php echo get_option('default_sold_out_text'); ?>" />
      </div>
    </div>

    <div class="row">
      <div class="control-label">
        <label><?php _e('[Rest-Seats]-Text', $data['textdomain'], 'event-creator'); ?></label>
      </div>
      <div class="form-control">
        <input type="text" class="control-field" name="default_rest_seats_text" value="<?php echo get_option('default_rest_seats_text'); ?>" />
      </div>
    </div>

    <div class="row">
      <div class="control-label">
        <label><?php _e('Price groups', $data['textdomain'], 'event-creator'); ?></label>
      </div>
      <div class="form-control">
        <input type="text" class="control-field" name="price_groups_text" value="<?php echo get_option('price_groups_text'); ?>" />
        <div class="help-block">
          <?php _e('Coma-separated list of names equal to names in Ticketter price category template settings. Watch out spaces after coma (if not on purpose)', $data['textdomain'], 'event-creator'); ?>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="control-label">
        <label><?php _e('Preselect Venue', $data['textdomain'], 'event-creator'); ?></label>
      </div>
      <div class="form-control form-dropdown">
        <?php
        $venue_args = array(
          'sort_order' => 'ASC',
          'sort_column' => 'post_title',
          'post_type' => 'venues',
          'name' => 'default_venue_id',
          'selected' => get_option('default_venue_id'),
          'show_option_none' => __('No default venue is selected', $data['textdomain'], 'event-creator')
        );
        wp_dropdown_pages($venue_args);
        ?>
      </div>
    </div>

    <?php submit_button(); ?>
  </form>

</div>