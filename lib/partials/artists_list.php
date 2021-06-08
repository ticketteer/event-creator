<a href="/wp-admin/post-new.php?post_type=artist" class="button-primary">
  <?php _e('New Artist', $data['textdomain'], 'event-creator') ?>
</a>
<h1>artists</h1>

<?php wp_list_pages('post_type=artists'); ?>
