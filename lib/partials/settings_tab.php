<?php
  $fields = apply_filters('event-creator-additional-fields', array());
?>

<div class="clear"></div>

<div class="form" id="event-creator-general-form">

  <?php foreach ( $fields as $field ) : ?>
    <div class="row">
      <div class="control-label">
        <label for="_events[<?= $field['name'] ?>]"><?= $field['title'] ?></label>
      </div>
      <div class="form-control<?= isset($field['relation']) && $field['relation'] ? ' form-dropdown' : '' ?>">
        <?php if ( $field['type'] == 'text' ) : ?>
          <input
            type="text"
            class="control-field"
            name="_events[<?= $field['name'] ?>]"
            <?= empty($field['placeholder']) ? '' : 'placeholder="' . $field['placeholder'] . '"' ?>
            value="<?php echo get_event_field($field['name'], $data['post']->ID); ?>"
          />
        <?php elseif ( $field['type'] == 'editor' ) : ?>
          <?php wp_editor(get_event_field($field['name'], $data['post']->ID),
                  '_events' . $field['name'], array('textarea_name' => '_events[' . $field['name'] . ']')) ?>
        <?php elseif ( $field['type'] == 'relation' ) : ?>
          <select
            name="_events[<?= $field['name'] ?>]"
            class="control-field-select"
          >
            <option value="">-</option>
          <?php $types = empty($field['relation']) ? array('events', 'page') : $field['relation']; ?>
          <?php foreach ( $types as $type ) : ?>
            <?php
              $args = array(
                'orderby' => 'post_title',
                'order' => 'ASC',
                'post_type' => $type,
                'post_status' => 'publish',
                'posts_per_page' => -1,
              );
              $options = get_posts($args);
            ?>
            <option value="" disabled>   ---   <?= $type ?>   ---   </option>
            <?php foreach ( $options as $option ) : ?>
              <option
                value="<?= $option->ID ?>"
                <?= ($option->ID == get_event_field($field['name'], $data['post']->ID)) ? 'selected' : '' ?>
              >
                <?= $option->post_title ?>
              </option>
            <?php endforeach; ?>
          <?php endforeach; ?>
          </select>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>

</div>
