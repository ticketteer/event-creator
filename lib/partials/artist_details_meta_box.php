<div class="form details-form">

  <h4><?php _e('Artist details', $data['textdomain'], 'event-creator'); ?></h4>

  <div class="row">
    <div class="control-label">
      <label for="_artists[www]"><?php _e('Website URL', $data['textdomain'], 'event-creator'); ?></label>
    </div>
    <div class="form-control">
      <input type="text" class="control-field" name="_artists[www]" value="<?php echo get_artist_field('www', $data['post']->ID); ?>" />
    </div>
  </div>

  <div class="row">
    <div class="control-label">
      <label for="_artists[type]"><?php _e('Type', $data['textdomain'], 'event-creator'); ?></label>
    </div>
    <div class="form-control">
      <input type="text" class="control-field" name="_artists[type]" value="<?php echo get_artist_field('type', $data['post']->ID); ?>" />
    </div>
  </div>

  <div class="row">
    <div class="control-label">
      <label for="_artists[phone]"><?php _e('Phone Nr.', $data['textdomain'], 'event-creator'); ?></label>
    </div>
    <div class="form-control">
      <input type="text" class="control-field" name="_artists[phone]" value="<?php echo get_artist_field('phone', $data['post']->ID); ?>" />
      <div class="help-block">
        <?php _e('This information will not be published', $data['textdomain'], 'event-creator'); ?>
      </div>
    </div>
  </div>

  <div class="control-label">
    <label for="content"><?php _e('Content', $data['textdomain'], 'event-creator'); ?></label>
  </div>

  <?php wp_editor( $data['post']->post_content, 'content', $settings = array() ); ?>

</div>
