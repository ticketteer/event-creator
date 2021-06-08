<div class="form details-form">

  <h4><?php _e('Venue details', $data['textdomain'], 'event-creator'); ?></h4>

  <div class="row">
    <div class="control-label">
      <label for="_venues[seats]"><?php _e('Seats', $data['textdomain'], 'event-creator'); ?></label>
    </div>
    <div class="form-control">
      <input type="text" class="control-field" name="_venues[seats]" value="<?php echo get_venue_field('seats', $data['post']->ID); ?>" />
    </div>
  </div>

  <div class="control-label">
    <label for="content"><?php _e('Content', $data['textdomain'], 'event-creator'); ?></label>
  </div>

  <?php wp_editor( $data['post']->post_content, 'content', $settings = array() ); ?>

</div>
