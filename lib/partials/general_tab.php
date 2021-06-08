<?php
  global $ticketteer_enabled;
  $categories = get_the_terms($data['post']->ID, 'event-type');
  $cat_names = [];
  if ($categories) :
    $cat_names = array_map(create_function('$o', 'return $o->slug;'), $categories);
  endif;
?>

<div class="clear"></div>

<div class="form" id="event-creator-general-form">

  <div class="row">
    <div class="control-label">
      <label for="_events[subtitle]"><?php _e('Event Subtitle', $data['textdomain'], 'event-creator'); ?></label>
    </div>
    <div class="form-control">
      <input type="text" class="control-field" name="_events[subtitle]" placeholder="<?php _e('Enter your event\'s Subtitle', $data['textdomain'], 'event-creator'); ?>" value="<?php echo get_event_field('subtitle', $data['post']->ID); ?>" />
    </div>
  </div>

  <?php if (in_array('theatre', $cat_names)) : ?>

    <div class="row">
      <div class="control-label">
        <label for="_events[directing]"><?php _e('Directing', $data['textdomain'], 'event-creator'); ?></label>
      </div>
      <div class="form-control">
        <input type="text" class="control-field" name="_events[directing]" value="<?php echo get_event_field('directing', $data['post']->ID); ?>" />
      </div>
    </div>

  <?php endif; ?>

  <div class="row">
    <div class="control-label">
      <label for="_events[artist_name]"><?php _e('Artist / Ensemble', $data['textdomain'], 'event-creator'); ?></label>
    </div>
    <div class="form-control form-dropdown">
      <?php
        $artist_args = array(
          'sort_order' => 'ASC',
          'sort_column' => 'post_title',
          'post_type' => 'artists',
          'name' => '_events[artist_id]',
          'selected' => get_event_field('artist_id', $data['post']->ID),
          'show_option_none' => __('No artist is linked', $data['textdomain'], 'event-creator')
        );
        wp_dropdown_pages( $artist_args );
      ?>
    </div>
  </div>

  <?php if ($ticketteer_enabled) : ?>

    <div class="row">
      <div class="control-label">
        <label for="_events[price_group]"><?php _e('Preisgruppe', $data['textdomain'], 'event-creator'); ?></label>
      </div>
      <div class="form-control">
        <select name="_events[price_group]">
          <?php
            $price_groups = get_option('price_groups_text');
            $cur_price_group = get_event_field('price_group', $data['post']->ID);
          ?>
          <option value="">-</option>
          <?php foreach( explode(',', $price_groups) as $groupname ) : ?>
            <option
              <?= $groupname == $cur_price_group ? 'selected="selected"' : '' ?>
              value="<?= $groupname ?>">
              <?= $groupname ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="help-block">
          <?= __('Automatically create ticket prices from one of the selected template group.', $data['textdomain'], 'event-creator') ?>
        </div>
      </div>
    </div>

  <?php endif; ?>


  <div class="control-label">
    <label for="content"><?php _e('Content', $data['textdomain'], 'event-creator'); ?></label>
  </div>
  <?php wp_editor( $data['post']->post_content, 'content' ); ?>

  <br />
  <hr />

  <div class="control-label">
    <label for="content">
      <?php _e('Excerpt', $data['textdomain'], 'event-creator'); ?>
      <?php global $ticketteer_enabled; ?>
      <?php if ($ticketteer_enabled) { ?>
        <p>
          <small>
            <?php _e('This will be used by ticketteer', $data['textdomain'], 'event-creator'); ?>
          </small>
        </p>
      <?php } ?>
    </label>
  </div>
  <?php wp_editor( get_the_excerpt($data['post']->ID), 'excerpt', array('media_buttons' => false, 'teeny' => true, 'quicktags' => false) ); ?>

</div>
