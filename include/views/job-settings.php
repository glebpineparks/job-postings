<?php 

global $pagenow, $typenow;
$post_id 	= $post->ID;


echo '<h3>'._x('Settings', 'job-settings', 'job-postings').'</h3>';

echo '<div class="jobs-row clearfix">';
	echo '<div class="jobs-row-label">';
	 		echo '<label>'._x('Custom excerpt', 'job-settings', 'job-postings').'</label>';
	 	echo '</div>';
	 	echo '<div class="jobs-row-input">';
	 		$key 	= 'job_custom_message';
	 		$value 	= get_post_meta( $post_id, $key, true );

	 		echo '<input class="jp-input" type="text" name="'.$key.'" id="'.$key.'" value="'.$value.'" placeholder="'._x('Custom excerpt', 'job-settings', 'job-postings').'"/>';
	 	echo '</div>';
echo '</div>';
