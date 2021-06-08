<div id="event-creator-tabs-wrap">
	<!-- Tabs -->
	<ul id="event-creator-tabs-nav" class="event-creator-tabs-nav" data-container="#event-creator-tabs" data-update-hashbang="1">
		<?php
		// Iterate through the available tabs, outputting them in a list.
	    $i = 0;
		foreach ( $data['tabs'] as $id => $title ) {
			$class = ( 0 === $i ? ' event-creator-active' : '' );
			?>
			<li class="event-creator-<?php echo $id; ?>">
				<a href="#event-creator-tab-<?php echo $id; ?>" title="<?php echo $title; ?>"<?php echo ( ! empty( $class ) ? ' class="' . $class . '"' : '' ); ?>>
					<?php echo $title; ?>
				</a>
			</li>
			<?php

			$i++;
		}
		?>
	</ul>

	<!-- Settings -->
	<div id="event-creator-tabs" data-navigation="#event-creator-tabs-nav">
	    <?php
	    // Iterate through the registered tabs, outputting a panel and calling a tab-specific action,
	    // which renders the settings view for that tab.
	    $i = 0;
	    foreach ( $data['tabs'] as $id => $title ) {
	        $class = ( 0 === $i ? 'event-creator-active' : '' );
	        ?>
	        <div
						id="event-creator-tab-<?php echo $id; ?>"
						class="event-creator-tab event-creator-clear <?php echo $class; ?>"
						data-tab="<?php echo $id ?>"
						data-post-id="<?php echo $data['post']->ID ?>"
					>
						<div class="event-creator-title title" data-orig-title="<?php echo $title; ?>">
						  <?php echo $title; ?>
						</div>
						<div class="notifications-wrap"></div>
	        	<?php do_action( 'event_creator_tab_' . $id, $data['post'] ); ?>
	        </div>
	        <?php
	        $i++;
	    }
	    ?>
	</div>
	<div class="event-creator-clear"></div>
</div>
