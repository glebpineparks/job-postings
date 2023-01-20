<?php 

global $pagenow, $typenow;
$post_id 	= $post->ID;

$defaults = array();
if (in_array( $pagenow, array( 'post-new.php' ) ) && "jobs" == $typenow){

	$defaults['notification'] = "{all_fields}";

	$defaults['confirmation'] = '<p style="text-align: center;">' . _x( 'Thank you for submitting your application. We will contact you shortly!', 'apply-now-confirmation', 'job-postings' ) . '</p>';

	$jobs_default_email = get_option('jobs_default_email');
	$defaults['confirmation_email'] = $jobs_default_email ? $jobs_default_email : get_option('admin_email');

}


echo '<h3>'._x('Confirmation', 'job-settings', 'job-postings').'</h3>';

echo '<div class="jobs-row clearfix">';
	echo '<div class="jobs-row-label">';
	 		echo '<label>'._x('Notification message', 'job-settings', 'job-postings').'</label>';
	 	echo '</div>';
	 	echo '<div class="jobs-row-input">';
	 		$key 	= 'job_notification_message';
	 		$value 	= get_post_meta( $post_id, $key, true );
	 		if( $value == '' && isset($defaults['confirmation']) ) $value = $defaults['confirmation'];

	 		wp_editor( 
				$value, 
				$key, 
				array(
					'textarea_rows' => 4
				) 
			);
	 	echo '</div>';
echo '</div>';