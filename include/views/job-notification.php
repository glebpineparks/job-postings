<?php 

global $pagenow, $typenow;
$post_id 	= $post->ID;


echo '<h3>'.apply_filters('job-postings/addedit_notifications/heading', _x('Notification', 'job-settings', 'job-postings')).'</h3>';

do_action('job-postings/addedit_notifications/before', $post_id);

echo '<div class="jobs-row clearfix">';
	echo '<div class="jobs-row-label">';
	 		echo '<label>'._x('Who receives notification?', 'job-settings', 'job-postings').'</label>';
	 	echo '</div>';
	 	echo '<div class="jobs-row-input">';
	 		$key 	= 'job_confirmation_email';
	 		$value 	= get_post_meta( $post_id, $key, true );
	 		if( isset($defaults['confirmation_email']) ) $value = $defaults['confirmation_email'];

	 		echo '<input class="jp-input" type="text" name="'.$key.'" id="'.$key.'" value="'.$value.'" placeholder="'._x('E-mail address', 'job-settings', 'job-postings').'" required/>';
	 	echo '</div>';

	 	echo '<p class="jfw_hint">' . __( 'You can add multiple emails, separated by <b>comma</b>. Example: <b>email1@mysite.com, email2@mysite.com</b>', 'job-postings' ) . '</p>';
echo '</div>';

echo Job_Postings_Helper::get_onoff_switch( __('Custom Notification', 'job-postings'), 'job_notify_custom_message', '', false, 'job_notify_message_editor' );

echo '<div id="job_notify_message_editor" class="jobs-row clearfix">';
	echo '<div class="jobs-row-label">';
        echo '<label>'._x('Custom Notification Message', 'job-postings').'</label>';
    echo '</div>';
        
    echo '<div class="jobs-row-input">';
        $key 	= 'job_notify_custom_message_editor';
        $value 	= get_post_meta( $post_id, $key, true );
        if( $value == '' && isset($defaults['notification']) ) $value = $defaults['notification'];

        wp_editor( $value, $key, array(
                        'textarea_rows' => 4
                        ) );
		echo '<p class="jfw_hint">' . __( 'You can add any message you want to forwart to the recipient. To add table with all fields, use {all_fields} somewhere in your email.', 'job-postings' ) . '</p><br>';
	echo '</div>';
echo '</div>';


do_action('job-postings/addedit_notifications/after', $post_id);