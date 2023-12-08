<?php
	wp_enqueue_media();


	$jobs_button_bg_color 		= get_option( 'jobs_button_bg_color' );
	$jobs_button_bg_color_hover = get_option( 'jobs_button_bg_color_hover' );
	$jobs_button_text_color 	= get_option( 'jobs_button_text_color' );
	$jobs_heading_text_color 	= get_option( 'jobs_heading_text_color' );
	$jobs_subheading_text_color = get_option( 'jobs_subheading_text_color' );
	$jobs_list_item_bg 			= get_option( 'jobs_list_item_bg' );
	$jobs_list_item_border 		= get_option( 'jobs_list_item_border' );
	$jobs_content_heading_color = get_option( 'jobs_content_heading_color' );
	$jobs_content_text_color 	= get_option( 'jobs_content_text_color' );

	$jobs_button_roundness 		= get_option( 'jobs_button_roundness' );
	$jobs_box_roundness 		= get_option( 'jobs_box_roundness' );

	$style = '<style>';

		if( $jobs_button_roundness )
			$style .= 'body .job-listing .job-preview .job-cta .apply-btn.preview_apply_btn,
						body .elements_preview .jp-apply-button,
						body .jobs_filters_preview .job-search,
						body .job-listing-categories .job-category,
						body .job-submit,
						body .choose_file,
						body .choose_file_multi,
						body .choose_file_multi_add 	{
							border-radius: '.$jobs_button_roundness.';
						}';


		if( $jobs_box_roundness )
			$style .= 'body .job-listing .job-preview,
						body .elements_preview .job-post .job-side{
							border-radius: '.$jobs_box_roundness.';
						}';


		// button background color
		if( $jobs_button_bg_color )
			$style .= 'body .job-listing .job-preview .job-cta .apply-btn.preview_apply_btn,
						body .elements_preview .jp-apply-button,
						body .job-listing-categories .job-category.active	{
							background-color: '.$jobs_button_bg_color.';
						}';

		if( $jobs_button_bg_color_hover )
			$style .= 'body .job-listing .job-preview .job-cta .apply-btn.preview_apply_btn:hover,
						body .elements_preview .jp-apply-button:hover,
						body .job-listing-categories .job-category.active:hover	{
							background-color: '.$jobs_button_bg_color_hover.';
						}
						body .job-listing-categories .job-category.active	{
							border-color: '.$jobs_button_bg_color_hover.';
						}
						';

		// button text color
		if( $jobs_button_text_color )
			$style .= 'body .job-listing .job-preview .job-cta .apply-btn.preview_apply_btn,
						body .elements_preview .jp-apply-button	{
							color: '.$jobs_button_text_color.';
						}';


		// heading text color
		if( $jobs_heading_text_color )
			$style .= 'body .elements_preview .job-post .jobs-row .jobs-row-label span.job_heading,
						body .job-listing .job-preview .job-content h5 a .job_heading{
							color: '.$jobs_heading_text_color.';
						}';



		// subheading text color
		if( $jobs_subheading_text_color ){
			$style .= 'body .job_subheading{
							color: '.$jobs_subheading_text_color.';
						}';
			$style .= 'body .job_subheading svg path{
							fill: '.$jobs_subheading_text_color.';
						}';
		}


		// box background color
		if( $jobs_list_item_bg )
			$style .= 'body .job-listing .job-preview,
						body .elements_preview .job-post .job-side{
							background-color: '.$jobs_list_item_bg.';
						}';

		// box border color
		if( $jobs_list_item_border )
			$style .= 'body .job-listing .job-preview,
						body .elements_preview .job-post .job-side{
							border-color: '.$jobs_list_item_border.';
						}';


		// content heading text color
		if( $jobs_content_heading_color )
			$style .= 'body .jobs_content_heading{
							color: '.$jobs_content_heading_color.';
						}';

		// content text text color
		if( $jobs_content_text_color )
			$style .= 'body .jobs_content_text{
							color: '.$jobs_content_text_color.';
						}';

	$style .= '</style>';

	echo $style;

	$placehold = "Option 1
Option 2
Option 3";
?>

<div class="wrap jp-help-top">
	<h2 class=""><img src="<?php echo plugins_url( '../images/settings.svg', __FILE__ ); ?>" width="30" alt=""><?php echo esc_html( get_admin_page_title() ); ?></h2>
</div>

<?php
$lang = Job_Postings::$lang;

$tabs = array(
	'jobs_settings' => __('Settings', 'job-postings'),
	'apply_modal' => __('Apply Form', 'job-postings'),
	'jobs_style' => __('Styles', 'job-postings'),
	'jobs_globals' => __('Global Options', 'job-postings'),
	'jobs_fields' => __('Default fields', 'job-postings'),
);
$tabs = apply_filters('job-postings/settings/tabs_array', $tabs);

if ( ! isset( $_REQUEST['settings-updated'] ) )
	$_REQUEST['settings-updated'] = 'false';

?>

<div class="wrap jobs_plugin_settings">

	<?php if ( 'false' !== sanitize_text_field($_REQUEST['settings-updated']) ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Settings updated', 'job-postings' ); ?></strong></p></div>
	<?php endif; ?>

	<div class="job_tabs vertical">
		<div class="wp-filter">
			<ul class="job_tab filter-links">
				<?php if(!empty($tabs)){
					foreach ($tabs as $key => $tab) {
						$current = '';
						if($key == 'jobs_settings') $current = 'current';
						echo '<li class=""><a href="#'.$key.'" class="'.$current.'">'.$tab.'</a></li>';
					}
				} ?>
			</ul>
		</div>

		<div class="job_tabs_content">

			<form method="post" action="options.php">
				<?php
				settings_fields( 'jobs_options_'.$lang );
				do_settings_sections( 'jobs_options' );


				//$last_screen = get_option( 'jobs_settings_last_screen_'.$lang );
				//echo '<input class="jfw_last_screen" type="hidden" name="jobs_settings_last_screen_'.$lang.'" value="'.$last_screen.'">';

				?>
				<div id="jobs_settings" class="job_tab_content clearfix">
					<div class="tabs">
						<div class="tab-wrapper">

							<div class="box">

								<h3><?php _e('Settings', 'job-postings') ?></h3>

								<?php
								if (function_exists('icl_object_id')) {
									echo '<p class="jfw_hint">' . __( 'These settings are compatible with WPML and Polylang Plugins. Switch current language in admin bar above, to update settings of selected language.', 'job-postings') . '</p><br>';
								}
								?>

									<div class="row clearfix">
										<label><?php echo _x('Hiring organization', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_hiring_organization';
											$value = get_option( $option_name.'_'.$lang );
											$value = htmlspecialchars( sanitize_text_field($value) ); // prevent xss
											if(empty($value)) $value = ''; // default
											$name = $option_name.'_'.$lang;
											echo '<input type="text" name="'.$option_name.'_'.$lang.'" value="'.$value.'" placeholder="'._x('Company name', 'job-settings', 'job-postings').'">';
										?>
										</div>
									</div>

									<div class="row clearfix">
										<label><?php echo _x('Currency symbol', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_currency_symbol';
											$value = get_option( $option_name.'_'.$lang );
											$value = htmlspecialchars( sanitize_text_field($value) ); // prevent xss
											if(empty($value)) $value = ''; // default
											$name = $option_name.'_'.$lang;
											echo '<input type="text" name="'.$option_name.'_'.$lang.'" value="'.$value.'">';
										?>
										</div>
									</div>

									<div class="row clearfix">
										<label><?php echo _x('Currency symbol position', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_currency_position';
											$value = get_option( $option_name.'_'.$lang );
											$value = htmlspecialchars( sanitize_text_field($value) ); // prevent xss
											if(empty($value)) $value = ''; // default
											$name = $option_name.'_'.$lang;
										?>
										<select name="<?php echo $name; ?>">
											<?php
												echo '<option value="before" '. selected('before', $value, false) .'>'._x('Before', 'job-settings', 'job-postings').'</option>';
												echo '<option value="after" '. selected('after', $value, false) .'>'._x('After', 'job-settings', 'job-postings').'</option>';
											?>
										</select>
										</div>
									</div>

									<div class="row clearfix">
										<label><?php echo _x('Preview CTA text', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_preview_cta';
											$value = get_option( $option_name.'_'.$lang );
											$value = htmlspecialchars( sanitize_text_field($value) ); // prevent xss
											if(empty($value)) $value = ''; // default
											$name = $option_name.'_'.$lang;
											echo '<input type="text" name="'.$option_name.'_'.$lang.'" value="'.$value.'" placeholder="'._x('View', 'job-postings', 'job-postings').'">';
										?>
										</div>
									</div>


									<div class="row clearfix">
										<label><?php echo _x('Page where you list jobs', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
											<?php
												$option_name = 'jobs_archive_page';
												$value = get_option( $option_name.'_'.$lang );
												$value = htmlspecialchars( sanitize_text_field($value) ); // prevent xss
												if(empty($value)) $value = ''; // default
												$name = $option_name.'_'.$lang;
											?>
											<select name="<?php echo $name; ?>">
												<option value=""><?php echo esc_attr( __( '-- Select page --', 'job-postings' ) ); ?></option>
												<?php

													$pages = get_pages();
													foreach ( $pages as $page ) {
														$sel = '';
														if($value == $page->ID) $sel = 'selected';

													$option = '<option value="' . $page->ID . '" '. $sel .'>';

														$posttitle = $page->post_title;
														if(!$posttitle) $posttitle = '- No title -';

														$option .= $posttitle;

													$option .= '</option>';
													echo $option;
													}

												?>
											</select>
											<p><?php _e('Used for propper linking of category and other functions.', 'job-postings') ?></p>
										</div>
									</div>


									<div class="row clearfix">
										<label><?php echo _x('Permalink slug', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_custom_slug';
											$value = get_option( $option_name.'_'.$lang );
											$value = htmlspecialchars( sanitize_text_field($value) ); // prevent xss
											if(empty($value)) $value = ''; // default
											$name = $option_name.'_'.$lang;
											echo '<input type="text" name="'.$option_name.'_'.$lang.'" value="'.$value.'" placeholder="'._x('Default slug: job', 'job-settings', 'job-postings').'">';
										?>
										</div>
									</div>


									<div class="row clearfix">
										<label><?php echo _x('No offers message', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_no_jobs_message';
											$value = get_option( $option_name.'_'.$lang );
											$value = htmlspecialchars( sanitize_text_field($value) ); // prevent xss
											if(empty($value)) $value = ''; // default
											$name = $option_name.'_'.$lang;
											echo '<input type="text" name="'.$option_name.'_'.$lang.'" value="'.$value.'" placeholder="'._x('Currently no job offers available.', 'job-message', 'job-postings').'">';
										?>
										</div>
									</div>


									<div class="row clearfix">
										<label><?php echo _x('Show "Offer ended" message when offer ends', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_offer_ended_message_enabled';
											$message_enabled = get_option( $option_name.'_'.$lang );
											$message_enabled = htmlspecialchars( sanitize_text_field($message_enabled) ); // prevent xss
											if(empty($message_enabled)) $message_enabled = ''; // default
											$name = $option_name.'_'.$lang;

											echo '<input id="jobs_offer_ended_message_enabled" type="checkbox" name="'.$option_name.'_'.$lang.'" '.checked($message_enabled, 'on', false).' value="on">';
										?>
										</div>
									</div>

									<div id="jobs_offer_ended_message_enabled_text" class="row clearfix">
										<label><?php echo _x('"Offer ended" message', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_offer_ended_message';
											$value = get_option( $option_name.'_'.$lang );
											$value = htmlspecialchars( sanitize_text_field($value) ); // prevent xss
											if(empty($value)) $value = ''; // default
											$name = $option_name.'_'.$lang;

											$placeholder = sprintf(_x('Offer ended on %s', 'job-message', 'job-postings'), date('d.m.Y'));

											echo '<input type="text" name="'.$option_name.'_'.$lang.'" value="'.$value.'" placeholder="'.$placeholder.'">';

											echo '<p class="description jfw_hint">'._x('Use %s where you would like to show offer ending date.', 'job-message', 'job-postings').'</p>';
										?>
										</div>
									</div>



									<div class="row clearfix">
										<label><?php echo __('File size exceeded message', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_filesize_validation';
											$value = get_option( $option_name.'_'.$lang );
											$value = htmlspecialchars( sanitize_text_field($value) ); // prevent xss
											if(empty($value)) $value = ''; // default
											$name = $option_name.'_'.$lang;

											$placeholder = sprintf(__('File %2$s exceeds the allowed file size of %1$s MB.', 'job-postings'), 10, 'example_file.pdf');

											echo '<input type="text" name="'.$option_name.'_'.$lang.'" value="'.$value.'" placeholder="'.$placeholder.'">';

											echo '<p class="description jfw_hint">'.__('Use %1$s where you would like to show allowed file size. Use %2$s where you would like to show file name.<br>You can update allowed file size in "Global options".', 'job-postings').'</p>';
										?>
										</div>
									</div>

							</div>
						</div><!-- .tab-wrapper (end) -->
					</div>

					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e( 'Save settings', 'job-postings' ); ?>" />
					</p>
				</div>

				<?php do_action('job-postings/settings/tabs/multilang'); ?>

			</form>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'jobs_options' );
				do_settings_sections( 'jobs_options' );


				//$last_screen = get_option( 'jobs_settings_last_screen' );
				//echo '<input class="jfw_last_screen" type="hidden" name="jobs_settings_last_screen" value="'.$last_screen.'">';
				?>

				<div id="apply_modal" class="job_tab_content clearfix" style="display: none;">

					<div class="tabs">
						<div class="tab-wrapper">

							<div class="box">

								<h3><?php _e('Apply Form fields', 'job-postings') ?></h3>
								
								<p class="description jfw_hint"><?php _e("Don't forget to input labels of the fields. If the label is left empty, field will not appear in the form.", 'job-postings'); ?></p>
								
								<?php
								// if (function_exists('icl_object_id')) {
								// 	echo '<p class="jfw_hint">'.__('These settings are global and independent to languages.', 'job-postings').'</p><br>';
								// }
								?>


								<?php
									//$options = get_option( 'jobs_apply_advanced_'.$lang );
									$options = get_option( 'jobs_apply_advanced' );
									//print_r($options);

									$languages = Job_Postings::$languages;

								?>

								<div class="jfw_repeater hg_repeater_wrap">
									<!--
										The value given to the data-repeater-list attribute will be used as the
										base of rewritten name attributes.  In this example, the first
										data-repeater-item's name attribute would become group-a[0][text-input],
										and the second data-repeater-item woulc become group-a[1][text-input]
									-->
									<div class="jfw_sortable" data-repeater-list="jobs_apply_advanced[modal]">
										<div class="jfw_repeater_row clearfix heading jfw_repeater_heading">
											<div class="column jfw_col_1"></div>
											<div class="column jfw_col_1"></div>
											<div class="column jfw_col_4">
												<?php _e('Field type', 'job-postings'); ?>
											</div>
											<div class="column jfw_col_2">
												<?php _e('Label', 'job-postings'); ?>
											</div>
											<div class="column jfw_col_2">
												<?php _e('Placeholder', 'job-postings'); ?>
											</div>
											<div class="column jfw_col_4">
												<?php _e('Required field', 'job-postings'); ?>
											</div>
										</div>
										<?php
										if(!empty($options['modal'])){
											//print_r( $options['modal']);

											// $search_name = jfw_find_key($options['modal'], 'field_type', 'name');
											// if( empty($search_name) ){
											// 	$options['modal'][0] = array('field_type' => 'name');
											// }

											//print_r( jfw_find_key($options['modal'], 'field_type', 'phone') );


											foreach ($options['modal'] as $key => $value) {
												$value_type = $options['modal'][$key]['field_type'];
										?>
										<div class="jfw_repeater_row clearfix" data-repeater-item>
											<div class="column jfw_col_1">
												<input data-repeater-delete type="button" class="button button-delete jfw_addRemove" value="-"/>
											</div>
											<div class="column jfw_col_1">
												<div class="button button-drag jfw_dragDrop" ><img src="<?php echo plugins_url( '../images/arrows-v.svg', __FILE__ ); ?>" alt="Drag"></div>
											</div>

											<div class="column jfw_col_4">
												<?php
													echo '<label>Select field type</label>';
													echo jfw_buildSelect( 'field_type', $value_type );
												?>
											</div>

											<div class="column jfw_col_2">
												<?php if($languages){
													foreach ($languages as $lang => $language) {
														$val = isset($options['modal'][$key]['label_'.$lang]) ? $options['modal'][$key]['label_'.$lang] : '';
														echo '<label for="label-field-'.$lang.'">'.__('Label', 'job-postings') . ' <b>' . strtoupper($lang).'</b></label>';
														echo '<input id="label-field-'.$lang.'"type="text" class="hg_label" name="label_'.$lang.'" value="'.htmlentities($val).'"/>';
													}
												} ?>
											</div>
											<div class="column jfw_col_2 jfw_col_placeholders">
												<?php if($languages){
													foreach ($languages as $lang => $language) {
														$val = isset($options['modal'][$key]['placeholder_'.$lang]) ? $options['modal'][$key]['placeholder_'.$lang] : '';
														echo '<label for="placeholder-field-'.$lang.'">'.__('Placeholder', 'job-postings') . ' <b>' . strtoupper($lang).'</b></label>';
														echo '<input id="placeholder-field-'.$lang.'"type="text" class="hg_label" name="placeholder_'.$lang.'" value="'.htmlspecialchars($val).'"/>';
													}
												} ?>
											</div>
											<div class="column jfw_col_4 jfw_col_required">
												<input type="checkbox" name="required" <?php if(isset($options['modal'][$key]['required'])) checked($options['modal'][$key]['required'][0], 'on', true); ?> value="on"/> <?php _e('Required', 'job-postings') ?>
											</div>

											<div class="jfw_options_row hide">
												<div class="column jfw_col_1">&nbsp;</div>
												<div class="column jfw_col_1">&nbsp;</div>
												<div class="column jfw_col_4">&nbsp;</div>
												<div class="column jfw_col_5">
													<b><?php _e('Options', 'job-postings') ?></b>
													<p class="help"><?php _e('One option per line.', 'job-postings') ?></p>

													<?php if($languages){
														foreach ($languages as $lang => $language) {
															echo '<div class="row-separation">';

															$val = isset($options['modal'][$key]['check_options_'.$lang]) ? $options['modal'][$key]['check_options_'.$lang]:'';
															echo '<label for="check-options-field-'.$lang.'">'.__('Options', 'job-postings') . ' <b>'.strtoupper($lang).'</b></label>';
															echo '<textarea id="check-options-field-'.$lang.'" class="hg_label" name="check_options_'.$lang.'"placeholder="'.$placehold.'">'.htmlspecialchars($val).'</textarea>';

															// preselection
															$val = isset($options['modal'][$key]['check_preselected_'.$lang]) ? $options['modal'][$key]['check_preselected_'.$lang]:'';
															echo '<label for="check-preselected-field-'.$lang.'">'.__('Preselected checkboxes indexes, eg.: 1,3,5', 'job-postings').'</label>';
															echo '<input id="check-preselected-field-'.$lang.'"type="text" class="hg_label" name="check_preselected_'.$lang.'" value="'.htmlspecialchars($val).'"/>';

															echo '</div>';
														}
													} ?>
												</div>
											</div>

											<div class="jfw_radio_row hide">
												<div class="column jfw_col_1">&nbsp;</div>
												<div class="column jfw_col_1">&nbsp;</div>
												<div class="column jfw_col_4">&nbsp;</div>
												<div class="column jfw_col_5">
													<b><?php _e('Options', 'job-postings') ?></b>
													<p class="help"><?php _e('One option per line.', 'job-postings') ?></p>

													<?php if($languages){
														foreach ($languages as $lang => $language) {
															echo '<div class="row-separation">';

															$val = isset($options['modal'][$key]['radio_options_'.$lang]) ? $options['modal'][$key]['radio_options_'.$lang]:'';
															echo '<label for="radio-options-field-'.$lang.'">'.__('Options', 'job-postings') . ' <b>'.strtoupper($lang).'</b></label>';
															echo '<textarea id="radio-options-field-'.$lang.'" class="hg_label" name="radio_options_'.$lang.'" placeholder="'.$placehold.'">'.htmlspecialchars($val).'</textarea>';

															// preselection
															$val = isset($options['modal'][$key]['radio_preselected_'.$lang]) ? $options['modal'][$key]['radio_preselected_'.$lang]:'';
															echo '<label for="radio-preselected-field-'.$lang.'">'.__('Preselected radio index, eg.: 3', 'job-postings').'</label>';
															echo '<input id="radio-preselected-field-'.$lang.'"type="text" class="hg_label" name="radio_preselected_'.$lang.'" value="'.htmlspecialchars($val).'"/>';

															echo '</div>';
														}
													} ?>
												</div>
											</div>

											<div class="jfw_select_row hide">
												<div class="column jfw_col_1">&nbsp;</div>
												<div class="column jfw_col_1">&nbsp;</div>
												<div class="column jfw_col_4">&nbsp;</div>
												<div class="column jfw_col_5">
													<b><?php _e('Options', 'job-postings') ?></b>
													<p class="help"><?php _e('One option per line.', 'job-postings') ?></p>

													<?php if($languages){
														foreach ($languages as $lang => $language) {
															echo '<div class="row-separation">';

															$val = isset($options['modal'][$key]['select_options_'.$lang]) ? $options['modal'][$key]['select_options_'.$lang]:'';
															echo '<label for="select-options-field-'.$lang.'">'.__('Options', 'job-postings') . ' <b>'.strtoupper($lang).'</b></label>';
															echo '<textarea id="select-options-field-'.$lang.'" class="hg_label" name="select_options_'.$lang.'" placeholder="'.$placehold.'">'.htmlspecialchars($val).'</textarea>';

															// preselection
															$val = isset($options['modal'][$key]['select_preselected_'.$lang]) ? $options['modal'][$key]['select_preselected_'.$lang]:'';
															echo '<label for="select-preselected-field-'.$lang.'">'.__('Preselected index, eg.: 3', 'job-postings').'</label>';
															echo '<input id="select-preselected-field-'.$lang.'"type="text" class="hg_label" name="select_preselected_'.$lang.'" value="'.htmlspecialchars($val).'"/>';

															echo '</div>';
														}
													} ?>
												</div>
											</div>

											<div class="jfw_file_row hide">
												<div class="column jfw_col_1">&nbsp;</div>
												<div class="column jfw_col_1">&nbsp;</div>
												<div class="column jfw_col_4">&nbsp;</div>
												<div class="column jfw_col_5">
													<b><?php _e('Options', 'job-postings') ?></b>
													<?php if($languages){
														foreach ($languages as $lang => $language) {
															echo '<div class="row-separation">';

																// preselection
																$val = isset($options['modal'][$key]['files_accepted_message_'.$lang]) ? $options['modal'][$key]['files_accepted_message_'.$lang] : '';
																echo '<label for="files-accepted-message-field-'.$lang.'">' .strtoupper($lang). ' '.__('Message', 'job-postings').'</label>';
																echo '<input id="rfiles-accepted-message-field-'.$lang.'"type="text" class="hg_label" name="files_accepted_message_'.$lang.'" value="'.htmlentities($val).'"/>';

															echo '</div>';
														}



														$uniqid = uniqid();
														echo '<div class="row-separation">';

															// preselection
															$val = isset($options['modal'][$key]['files_accepted']) ? $options['modal'][$key]['files_accepted'] : '';
															echo '<label for="files-accepted-field-'.$uniqid.'">'.__('Accepted file extensions. (Example: .jpg, .gif, .png)', 'job-postings').'</label>';
															echo '<input id="rfiles-accepted-field-'.$uniqid.'"type="text" class="hg_label" name="files_accepted" value="'.htmlspecialchars($val).'"/>';

														echo '</div>';


													} ?>
												</div>
											</div>


											<div class="jfw_multi_file_row hide">
												<div class="column jfw_col_1">&nbsp;</div>
												<div class="column jfw_col_1">&nbsp;</div>
												<div class="column jfw_col_4">&nbsp;</div>
												<div class="column jfw_col_5">
													<b><?php _e('Options', 'job-postings') ?></b>
													<?php if($languages){
														foreach ($languages as $lang => $language) {
															echo '<div class="row-separation">';

																// preselection
																$val = isset($options['modal'][$key]['multi_files_accepted_message_'.$lang]) ? $options['modal'][$key]['multi_files_accepted_message_'.$lang] : '';
																echo '<label for="files-accepted-message-field-'.$lang.'">' .strtoupper($lang). ' '.__('Message', 'job-postings').'</label>';
																echo '<input id="rfiles-accepted-message-field-'.$lang.'"type="text" class="hg_label" name="multi_files_accepted_message_'.$lang.'" value="'.htmlspecialchars($val).'"/>';

															echo '</div>';
														}



														$uniqid = uniqid();
														echo '<div class="row-separation">';

															// preselection
															$val = isset($options['modal'][$key]['multi_files_accepted']) ? $options['modal'][$key]['multi_files_accepted'] : '';
															echo '<label for="files-accepted-field-'.$uniqid.'">'.__('Accepted file extensions. (Example: .jpg, .gif, .png)', 'job-postings').'</label>';
															echo '<input id="rfiles-accepted-field-'.$uniqid.'"type="text" class="hg_label" name="multi_files_accepted" value="'.htmlspecialchars($val).'"/>';

														echo '</div>';


													} ?>
												</div>
											</div>

										</div>
										<?php
											}
										}else{ ?>

											<div class="jfw_repeater_row clearfix" data-repeater-item>
												<div class="column jfw_col_1">
													<input data-repeater-delete type="button" class="button button-delete jfw_addRemove" value="-"/>
												</div>
												<div class="column jfw_col_1">
													<div class="button button-drag jfw_dragDrop" ><img src="<?php echo plugins_url( '../images/arrows-v.svg', __FILE__ ); ?>" alt="Drag"></div>
												</div>

												<div class="column jfw_col_4">

													<?php
														echo '<label>' . __( 'Select field type', 'job-postings') . '</label>';
														echo jfw_buildSelect( 'field_type', '' );
													?>
												</div>

												<div class="column jfw_col_2">
													<?php if($languages){
														foreach ($languages as $lang => $language) {
															echo '<label for="label-field-'.$lang.'">'.__('Label', 'job-postings') . ' <b>'.strtoupper($lang).'</b></label>';
															echo '<input id="label-field-'.$lang.'"type="text" class="hg_label" name="label_'.$lang.'" value=""/>';
														}
													} ?>
												</div>
												<div class="column jfw_col_2 jfw_col_placeholders">
													<?php if($languages){
														foreach ($languages as $lang => $language) {
															echo '<label for="placeholder-field-'.$lang.'">'.__('Placeholder', 'job-postings') . ' <b>'.strtoupper($lang).'</b></label>';
															echo '<input id="placeholder-field-'.$lang.'"type="text" class="hg_label" name="placeholder_'.$lang.'" value=""/>';
														}
													} ?>
												</div>
												<div class="column jfw_col_4 jfw_col_required">
													<input type="checkbox" name="required" value="on"/> <?php _e('Required', 'job-postings') ?>
												</div>


												<div class="jfw_options_row hide">
													<div class="column jfw_col_1">&nbsp;</div>
													<div class="column jfw_col_1">&nbsp;</div>
													<div class="column jfw_col_4">&nbsp;</div>
													<div class="column jfw_col_2">
														<b><?php _e('Options', 'job-postings') ?></b>
														<p class="help"><?php _e('One option per line.', 'job-postings') ?></p>

														<?php if($languages){
															foreach ($languages as $lang => $language) {
																echo '<label for="check-options-field-'.$lang.'">'.__('Options', 'job-postings') . ' '.strtoupper($lang).'</label>';
																echo '<textarea id="check-options-field-'.$lang.'" class="hg_label" name="check_options_'.$lang.'" placeholder="'.$placehold.'"></textarea>';
															}
														} ?>
													</div>
													<div class="column jfw_col_2">
														<b><?php _e('Preselected', 'job-postings') ?></b>
														<p class="help"><?php _e('Preselected option', 'job-postings') ?></p>
														<?php if($languages){
															foreach ($languages as $lang => $language) {
																echo '<label for="check-preselected-field-'.$lang.'">'.__('Preselected', 'job-postings') . ' '.strtoupper($lang).'</label>';
																echo '<input id="check-preselected-field-'.$lang.'"type="text" class="hg_label" name="check_preselected_'.$lang.'" value=""/>';
															}
														} ?>
													</div>
												</div>

												<div class="jfw_radio_row hide">
													<div class="column jfw_col_1">&nbsp;</div>
													<div class="column jfw_col_1">&nbsp;</div>
													<div class="column jfw_col_4">&nbsp;</div>
													<div class="column jfw_col_2">
														<b><?php _e('Radio', 'job-postings') ?></b>
														<p class="help"><?php _e('One option per line.', 'job-postings') ?></p>

														<?php if($languages){
															foreach ($languages as $lang => $language) {
																echo '<label for="radio-options-field-'.$lang.'">'.__('Options', 'job-postings') . ' '.strtoupper($lang).'</label>';
																echo '<textarea id="radio-options-field-'.$lang.'" class="hg_label" name="radio_options_'.$lang.'" placeholder="'.$placehold.'"></textarea>';
															}
														} ?>
													</div>
													<div class="column jfw_col_2">
														<b><?php _e('Preselected', 'job-postings') ?></b>
														<p class="help"><?php _e('Preselected option', 'job-postings') ?></p>
														<?php if($languages){
															foreach ($languages as $lang => $language) {
																echo '<label for="radio-preselected-field-'.$lang.'">'.__('Preselected', 'job-postings') . ' '.strtoupper($lang).'</label>';
																echo '<input id="radio-preselected-field-'.$lang.'"type="text" class="hg_label" name="radio_preselected_'.$lang.'" value=""/>';
															}
														} ?>
													</div>
												</div>
												
												<div class="jfw_select_row hide">
													<div class="column jfw_col_1">&nbsp;</div>
													<div class="column jfw_col_1">&nbsp;</div>
													<div class="column jfw_col_4">&nbsp;</div>
													<div class="column jfw_col_5">
														<b><?php _e('Options', 'job-postings') ?></b>
														<p class="help"><?php _e('One option per line.', 'job-postings') ?></p>

														<?php if($languages){
															foreach ($languages as $lang => $language) {
																echo '<div class="row-separation">';

																echo '<label for="select-options-field-'.$lang.'">'.__('Options', 'job-postings') . ' <b>'.strtoupper($lang).'</b></label>';
																echo '<textarea id="select-options-field-'.$lang.'" class="hg_label" name="select_options_'.$lang.'" placeholder="'.$placehold.'"></textarea>';

																// preselection
																echo '<label for="select-preselected-field-'.$lang.'">'.__('Preselected index, eg.: 3', 'job-postings').'</label>';
																echo '<input id="select-preselected-field-'.$lang.'"type="text" class="hg_label" name="select_preselected_'.$lang.'" value=""/>';

																echo '</div>';
															}
														} ?>
													</div>
												</div>
											</div>

											
										<?php } ?>

									</div>
									<input data-repeater-create type="button" class="button button-primary hg_addRemove" value="+"/>
								</div>

							</div>
						</div><!-- .tab-wrapper (end) -->
					</div>


					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e( 'Save settings', 'job-postings' ); ?>" />
					</p>


				</div>


				<div id="jobs_style" class="job_tab_content clearfix" style="display: none;">

					<div class="tabs">
						<div class="tab-wrapper">

							<div class="box">

								<h3><?php _e('Style settings', 'job-postings') ?></h3>
								<?php
								if (function_exists('icl_object_id')) {
									echo '<p class="jfw_hint">'.__('These settings are global and independent to languages.', 'job-postings').'</p><br>';
								}
								?>



								<div class="row clearfix">
									<label><?php echo _x('Sidebar position', 'job-settings', 'job-postings') ?></label>
									<div class="jobs-settings-input job-radios">
									<?php
										$option_name = 'jobs_sidebar_position';
										$value = get_option( $option_name );
										$value = sanitize_text_field($value);
										if(empty($value)) $value = 'right'; // default
										$name = $option_name;

										$radios = '';
										$radios .= '<label for="side_left">';
											$radios .= '<input id="side_left" type="radio" name="'.$option_name.'" value="left" '.checked($value,'left', false).'>';
											$radios .= Job_Postings_Helper::getRawSvg( 'set-left.svg', false);
										$radios .='</label>';

										$radios .= '<label for="side_right">';
											$radios .='<input id="side_right" type="radio" name="'.$option_name.'" value="right" '.checked($value,'right', false).'>';
											$radios .= Job_Postings_Helper::getRawSvg( 'set-right.svg', false );
										$radios .='</label>';

										echo $radios;


									?>
									</div>
								</div>


								<div class="row clearfix row-has-grid">
									<div class="el33">
										<div class="elwrap">
											<label><?php echo _x('Button', 'job-settings', 'job-postings') ?></label>
											<div class="jobs-settings-input">
											<?php
												$option_name = 'jobs_button_bg_color';
												$value = get_option( $option_name );
												$value = sanitize_text_field($value);
												$val = !empty($value) ? $value : '#22c0f1'; // default
												$name = $option_name;
												echo '<div id="jobs_button_bg_color" data-color="'.$val.'" class="colorSelector"><div style="background-color: '.$val.'"></div></div>';
												echo '<input type="text" class="jobs_button_bg_color hidden" name="'.$option_name.'" value="'.$value.'">';
											?>
											</div>
										</div>
									</div>

									<div class="el33">
										<div class="elwrap">
											<label><?php echo _x('Button <b>:hover</b>', 'job-settings', 'job-postings') ?></label>
											<div class="jobs-settings-input">
											<?php
												$option_name = 'jobs_button_bg_color_hover';
												$value = get_option( $option_name );
												$value = sanitize_text_field($value);
												$val = !empty($value) ? $value : '#22c0f1'; // default
												$name = $option_name;
												echo '<div id="jobs_button_bg_color_hover" data-color="'.$val.'" class="colorSelector"><div style="background-color: '.$val.'"></div></div>';
												echo '<input type="text" class="jobs_button_bg_color_hover hidden" name="'.$option_name.'" value="'.$value.'">';
											?>
											</div>
										</div>
									</div>

									<div class="el33">
										<div class="elwrap">
											<label><?php echo _x('Button text color', 'job-settings', 'job-postings') ?></label>
											<div class="jobs-settings-input">
											<?php
												$option_name = 'jobs_button_text_color';
												$value = get_option( $option_name );
												$value = sanitize_text_field($value);
												$val = !empty($value) ? $value : '#ffffff'; // default
												$name = $option_name;
												echo '<div id="jobs_button_text_color" data-color="'.$val.'" class="colorSelector"><div style="background-color: '.$val.'"></div></div>';
												echo '<input type="text" class="jobs_button_text_color hidden" name="'.$option_name.'" value="'.$value.'">';
											?>
											</div>
										</div>
									</div>

									<div class="el33">
										<div class="elwrap">
											<label><?php echo _x('Heading text color', 'job-settings', 'job-postings') ?></label>
											<div class="jobs-settings-input">
											<?php
												$option_name = 'jobs_heading_text_color';
												$value = get_option( $option_name );
												$value = sanitize_text_field($value);
												$val = !empty($value) ? $value : '#000000'; // default
												$name = $option_name;
												echo '<div id="jobs_heading_text_color" data-color="'.$val.'" class="colorSelector"><div style="background-color: '.$val.'"></div></div>';
												echo '<input type="text" class="jobs_heading_text_color hidden" name="'.$option_name.'" value="'.$value.'">';
											?>
											</div>
										</div>
									</div>

									<div class="el33">
										<div class="elwrap">
											<label><?php echo _x('Subheading text color', 'job-settings', 'job-postings') ?></label>
											<div class="jobs-settings-input">
											<?php
												$option_name = 'jobs_subheading_text_color';
												$value = get_option( $option_name );
												$value = sanitize_text_field($value);
												$val = !empty($value) ? $value : '#373737'; // default
												$name = $option_name;
												echo '<div id="jobs_subheading_text_color" data-color="'.$val.'" class="colorSelector"><div style="background-color: '.$val.'"></div></div>';
												echo '<input type="text" class="jobs_subheading_text_color hidden" name="'.$option_name.'" value="'.$value.'">';
											?>
											</div>
										</div>
									</div>

									<div class="el33">
										<div class="elwrap">
											<label><?php echo _x('Box background', 'job-settings', 'job-postings') ?></label>
											<div class="jobs-settings-input">
											<?php
												$option_name = 'jobs_list_item_bg';
												$value = get_option( $option_name );
												$value = sanitize_text_field($value);
												$val = !empty($value) ? $value : '#f0f0f0'; // default
												$name = $option_name;
												echo '<div id="jobs_list_item_bg" data-color="'.$val.'" class="colorSelector"><div style="background-color: '.$val.'"></div></div>';
												echo '<input type="text" class="jobs_list_item_bg hidden" name="'.$option_name.'" value="'.$value.'">';
											?>
											</div>
										</div>
									</div>

									<div class="el33">
										<div class="elwrap">
											<label><?php echo _x('Box border', 'job-settings', 'job-postings') ?></label>
											<div class="jobs-settings-input">
											<?php
												$option_name = 'jobs_list_item_border';
												$value = get_option( $option_name );
												$value = sanitize_text_field($value);
												$val = !empty($value) ? $value : '#e9e9e9'; // default
												$name = $option_name;
												echo '<div id="jobs_list_item_border" data-color="'.$value.'" class="colorSelector"><div style="background-color: '.$value.'"></div></div>';
												echo '<input type="text" class="jobs_list_item_border hidden" name="'.$option_name.'" value="'.$value.'">';
											?>
											</div>
										</div>
									</div>

									<div class="el33">
										<div class="elwrap">
											<label><?php echo _x('Content heading color', 'job-settings', 'job-postings') ?></label>
											<div class="jobs-settings-input">
											<?php
												$option_name = 'jobs_content_heading_color';
												$value = get_option( $option_name );
												$value = sanitize_text_field($value);
												$val = !empty($value) ? $value : '#000000'; // default
												$name = $option_name;
												echo '<div id="jobs_content_heading_color" data-color="'.$val.'" class="colorSelector"><div style="background-color: '.$val.'"></div></div>';
												echo '<input type="text" class="jobs_content_heading_color hidden" name="'.$option_name.'" value="'.$value.'">';
											?>
											</div>
										</div>
									</div>

									<div class="el33">
										<div class="elwrap">
											<label><?php echo _x('Content text color', 'job-settings', 'job-postings') ?></label>
											<div class="jobs-settings-input">
											<?php
												$option_name = 'jobs_content_text_color';
												$value = get_option( $option_name );
												$value = sanitize_text_field($value);
												$val = !empty($value) ? $value : '#000000'; // default
												$name = $option_name;
												echo '<div id="jobs_content_text_color" data-color="'.$val.'" class="colorSelector"><div style="background-color: '.$val.'"></div></div>';
												echo '<input type="text" class="jobs_content_text_color hidden" name="'.$option_name.'" value="'.$value.'">';
											?>
											</div>
										</div>
									</div>
								</div>


								<div class="row clearfix">
									<label><?php echo _x('Button border radius', 'job-settings', 'job-postings') ?></label>
									<div class="jobs-settings-input">
									<?php
										$option_name = 'jobs_button_roundness';
										$value = get_option( $option_name );
										$value = sanitize_text_field($value);
										if(empty($value)) $value = '35px'; // default
										$name = $option_name;
									?>
										<select class="<?php echo $name; ?>" name="<?php echo $name; ?>">
											<?php

												for($i=0;$i<36;$i++){
													echo '<option value="'.$i.'px" '. selected($i.'px', $value, false) .'>'.$i.'px</option>';
												}
											?>
										</select>
									</div>
								</div>

								<div class="row clearfix">
									<label><?php echo _x('Box border radius', 'job-settings', 'job-postings') ?></label>
									<div class="jobs-settings-input">
									<?php
										$option_name = 'jobs_box_roundness';
										$value = get_option( $option_name );
										$value = sanitize_text_field($value);
										if(empty($value)) $value = '4px'; // default
										$name = $option_name;
									?>
										<select class="<?php echo $name; ?>" name="<?php echo $name; ?>">
											<?php

												for($i=0;$i<21;$i++){
													echo '<option value="'.$i.'px" '. selected($i.'px', $value, false) .'>'.$i.'px</option>';
												}
											?>
										</select>
									</div>
								</div>


								<div class="row clearfix">
									<label><?php echo _x('Filters layout', 'job-settings', 'job-postings') ?></label>
									<div class="jobs-settings-input">
									<?php
										$option_name = 'jobs_filters_styles';
										$value = get_option( $option_name );
										$value = sanitize_text_field($value);
										if(empty($value)) $value = 'filter-style-1'; // default
										$name = $option_name;
									?>
										<select class="<?php echo $name; ?>" name="<?php echo $name; ?>">
											<?php
												for($i=1;$i<7;$i++){
													echo '<option value="filter-style-'.$i.'" '. selected('filter-style-'.$i, $value, false) .'>Style '.$i.'</option>';
												}
											?>
										</select>
									</div>
								</div>

								<div class="row clearfix">
									<label><?php echo _x('Hide location in preview', 'job-settings', 'job-postings') ?></label>
									<div class="jobs-settings-input">
									<?php
										$option_name = 'jobs_preview_location';
										$message_enabled = get_option( $option_name );
										$message_enabled = sanitize_text_field($message_enabled);
										if(empty($message_enabled)) $message_enabled = ''; // default
										$name = $option_name;

										echo '<input id="'.$option_name.'" type="checkbox" name="'.$option_name.'" '.checked($message_enabled, 'on', false).' value="on">';
									?>
									</div>
								</div>


								<div class="row clearfix">
									<label><?php echo _x('Hide employment type in preview', 'job-settings', 'job-postings') ?></label>
									<div class="jobs-settings-input">
									<?php
										$option_name = 'jobs_preview_employment_type';
										$message_enabled = get_option( $option_name );
										$message_enabled = sanitize_text_field($message_enabled);
										if(empty($message_enabled)) $message_enabled = ''; // default
										$name = $option_name;

										echo '<input id="'.$option_name.'" type="checkbox" name="'.$option_name.'" '.checked($message_enabled, 'on', false).' value="on">';
									?>
									</div>
								</div>


								<div class="elements_preview">

								<h2><?php echo _x( 'PREVIEW', 'job-settings', 'job-postings' ); ?></h2>
								<p class="description jfw_hint"><?php echo _x( 'Actual layout and some styling can look a bit different on the site, because of the theme used.', 'job-settings', 'job-postings' ); ?></p>
								<br>


								<div class="jobs_filters_preview">
									<h3><?php _e('Filters', 'job-postings') ?>:</h3>
									<p><?php _e('This is the filters that you see when use show_filters="true" on [job-postings] shortcode', 'job-postings') ?></p>
									<div class="job-postings-filters clearfix ">
										<div class="job-listing-categories align-left">
											<a href="#" class="job-category borderRadius active">All</a>
											<a href="#" class="job-category borderRadius">Category 1 <span class="category">(23)</span></a>
											<a href="#" class="job-category borderRadius">Category 2 <span class="category">(15)</span></a>
										</div>
										<div class="jobs-search">
											<div class="form">
												<input class="job-search borderRadius" placeholder="Search for Job" value="" name="job-search" type="text">
												<button class="job-search-submit"><svg width="0px" height="0px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 56.966 56.966" xml:space="preserve"><path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23
													s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92
													c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17
													s-17-7.626-17-17S14.61,6,23.984,6z"></path></svg>
												</button>
											</div>
										</div>
									</div>
								</div>

								<br>
								<br>

								<h3>Listing:</h3>
								<div class="job-listing"><div class="job-preview clearfix" itemscope="" itemtype="http://schema.org/JobPosting" role="main"><div class="job-content"><h5><a href="#"><span class="job_heading" itemprop="title">Front-end developer ads</span></a></h5><div class="job-additional-information"><span itemprop="jobLocation" itemscope="" itemtype="http://schema.org/Place"><span class="job_subheading city" itemprop="address">London</span><span class="job_separator job_subheading">, </span><span class="job_subheading type" itemprop="employmentType">Full time</span><div class="job_subheading custom">Custom job message</div></div></div><div class="job-cta"><a href="#" class="apply-btn local preview_apply_btn" itemprop="url">View</a></div></div></div>

								<br>
								<br>

								<h3>Job posting:</h3>
								<div class="job-post clearfix" role="main"><div class="job-content"><div class="job-content-wrap"><div class="jobs-row position_title type-text position_title"><div class="jobs-row-label"><span class="jobs_content_heading">Position title</span></div><div class="jobs-row-input jobs_content_text">Front-end developer</div></div><div class="jobs-row clearfix position_description type-tinymce "><div class="jobs-row-label"><span class="jobs_content_heading">Description</span></div><div class="jobs-row-input"><p class="jobs_content_text">Jobs for WordPress is a WordPress plugin for easily adding job postings to your company’s website in a structured way.</p>
								</div></div><div class="jobs-row clearfix position_qualifications type-tinymce "><div class="jobs-row-label"><span class="jobs_content_heading">Qualifications</span></div><div class="jobs-row-input"><p class="jobs_content_text">While you can comfortably create and manage job postings in a very user-friendly way, they are also automatically structured with schema.org.</p>
								</div></div></div></div><div class="job-side"><div class="job-content-wrap"><div class="jobs-row clearfix position_employment_type type-text "><div class="jobs-row-label"><span class="job_heading">Employment Type</span></div><div class="jobs-row-input job_subheading">Full time</div></div><div class="jobs-row clearfix position_industry type-text "><div class="jobs-row-label"><span class="job_heading">Industry</span></div><div class="jobs-row-input job_subheading">Information Technology</div></div><div class="jobs-row clearfix type-empty_date "><div class="jobs-row-input"><div class="jobs-row clearfix type-date-posted"><div class="jobs-row-label"><span class="job_heading">Date posted</span></div><div class="jobs-row-input job_subheading"><!-- Generator: Adobe Illustrator 19.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
								<svg width="0px" height="0px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 426.667 426.667" style="enable-background:new 0 0 426.667 426.667;" xml:space="preserve">
								<g>
									<g>
										<g>
											<path d="M362.667,42.667h-21.333V0h-42.667v42.667H128V0H85.333v42.667H64c-23.573,0-42.453,19.093-42.453,42.667L21.333,384
												c0,23.573,19.093,42.667,42.667,42.667h298.667c23.573,0,42.667-19.093,42.667-42.667V85.333
												C405.333,61.76,386.24,42.667,362.667,42.667z M362.667,384H64V149.333h298.667V384z"></path>
										</g>
									</g>
								</g>
								</svg>
								July 5, 2017</div></div></div></div><div class="jobs-row-apply"><a href="#apply-now" class="button jp-apply-button preview_apply_btn">Apply Now</a></div></div></div></div>

							</div>

								<br>


								<div class="row clearfix">
									<label><?php echo _x('Custom CSS', 'job-settings', 'job-postings') ?></label>
									<div class="jobs-settings-input">
									<?php
										$option_name = 'jobs_custom_css';
										$value = get_option( $option_name );
										$value = strip_tags($value);
										if(empty($value)) $value = ''; // default
										$name = $option_name;
										echo '<textarea name="'.$option_name.'" >'.$value.'</textarea>';
									?>
									</div>
								</div>

							</div>
						</div><!-- .tab-wrapper (end) -->
					</div>


					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e( 'Save settings', 'job-postings' ); ?>" />
						<input type="submit" class="resetStyleSettings button-secondary" value="<?php _e( 'Reset style settings', 'job-postings' ); ?>" />
					</p>

				</div>


				<div id="jobs_globals" class="job_tab_content clearfix" style="display: none;">

					<div class="tabs">
						<div class="tab-wrapper">

							<div class="box">

								<h3><?php _e('Global options', 'job-postings') ?></h3>
								<?php
								if (function_exists('icl_object_id')) {
									echo '<p class="jfw_hint">'.__('These settings are global and independent to languages.', 'job-postings').'</p><br>';
								}
								?>

									<div class="row clearfix">
										<label><?php echo _x('Company logo', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_company_logo';
											$value = get_option( $option_name );
											$value = sanitize_text_field($value);
											if(empty($value)) $value = ''; // default
											$name = $option_name;

											if( $value != '' ){
												echo '<img class="company_logo_preview" src="'.$value.'" alt="">';
											}

											echo '<input id="'.$option_name.'_upload_file" type="text" name="'.$option_name.'" value="'.$value.'">';
											echo '<input id="'.$option_name.'_upload_file_button" class="button" type="button" value="'.__('Upload/Select file', 'job-postings').'" />';

										?>
										</div>
									</div>

									<div class="row clearfix">
										<label><?php echo _x('Jobs per page', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_posts_per_page';
											$value = get_option( $option_name );
											$value = sanitize_text_field($value);
											if(empty($value)) $value = ''; // default
											$name = $option_name;
											echo '<input type="text" name="'.$option_name.'" value="'.$value.'">';
										?>
										</div>
									</div>


									<div class="row clearfix">
										<label><?php echo _x('Default email', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_default_email';
											$value = get_option( $option_name );
											$value = sanitize_text_field($value);
											if(empty($value)) $value = get_option('admin_email'); // default
											$name = $option_name;
											echo '<input type="text" name="'.$option_name.'" value="'.$value.'">';
										?>
										</div>
									</div>

									<div class="row clearfix">
										<label><?php echo __("SEO Schema", 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_selected_schema';
											$fs = get_option( $option_name );

											echo '<select name="'.$option_name.'">';
												echo '<option value="default" '.selected($fs, 'default', false).'>'.__('Default', 'job-postings').'</option>';
												if( !class_exists('WPSEO_Admin') ){
													$disabled = "disabled";
												}
												echo '<option '.$disabled.' value="yoast_seo" '.selected($fs, 'yoast_seo', false).'>'.__('Yoast SEO', 'job-postings').'</option>';
													
											echo '</select>';

											echo '<p class="description jfw_hint">'.__("If your site uses Yoast SEO, the structured SEO Schema will follow Yoast's recommendations. Choose 'Default' if you are not sure.", 'job-postings').'</p>';
										?>
										</div>
									</div>


									<br>
									<h3><?php echo __("File storage", 'job-postings') ?></h3>
									<div class="row clearfix">
										<label><?php echo __("Where to store uploaded files?", 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_file_storage';
											$fs = get_option( $option_name );

											echo '<select name="'.$option_name.'">';
												echo '<option value="secure" '.selected($fs, 'secure', false).'>'.__('Secure Location', 'job-postings').'</option>';
												echo '<option value="media" '.selected($fs, 'media', false).'>'.__('WP Media', 'job-postings').'</option>';
											echo '</select>';

											echo '<p class="description jfw_hint">'.__("On some servers it is not possible to use \"Secure Location\" outside of the site's root folder, because of the limited permissions.", 'job-postings').'</p>';
										?>
										</div>
									</div>

									<div class="row clearfix">
										<label><?php echo __('Max allowed file size', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_max_filesize';
											$value = get_option( $option_name );
											$value = sanitize_text_field($value);
											if(empty($value)) $value = ''; // default
											$name = $option_name;
											echo '<input type="number" name="'.$option_name.'" value="'.$value.'" placeholder="10"> <span>MB</span>';

											echo '<p class="description jfw_hint">'.__('For example: <b>12</b> or <b>0.5</b>.', 'job-postings').'</p>';
										?>
										</div>
									</div>

									<br>
									<h3>GDPR</h3>
									<div class="row clearfix">
										<label><?php echo __("Don't store user data", 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_dont_store_user_data';
											$store_user_data = get_option( $option_name );

											echo '<input id="jobs_offer_ended_message_enabled" type="checkbox" name="'.$option_name.'" '.checked($store_user_data, 'on', false).' value="on">';
										
											echo '<p class="description jfw_hint">'.__('With this option enabled, plugin will only process user data to notify you by email and immidiatly deletes everything user has inputed, after email is sent successfully.', 'job-postings').'</p>';

											echo '<p class="description jfw_hint">'.__('If your site has issues sending emails, the user data will be stored on the site in this case. You will have to delete it manually,  as soon you review the entry.', 'job-postings').'</p>';
										?>
										</div>
									</div>

									
									<br>
									<h3>reCaptcha</h3>

									<div class="row clearfix">
										<label><?php echo _x('Site Key', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_recaptcha_site_key';
											$value = get_option( $option_name );
											$value = sanitize_text_field($value);
											echo '<input type="text" name="'.$option_name.'" value="'.$value.'">';
										?>
										</div>
									</div>
									<div class="row clearfix">
										<label><?php echo _x('Secret Key', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_recaptcha_secret_key';
											$value = get_option( $option_name );
											$value = sanitize_text_field($value);
											echo '<input type="text" name="'.$option_name.'" value="'.$value.'">';

											echo '<p class="description jfw_hint">'.__('To get your keys, go to <a href="https://www.google.com/recaptcha/" target="_blank">reCaptcha page</a>.', 'job-postings').'</p>';
										?>
										</div>
									</div>

									<div class="row clearfix">
										<label><?php echo _x('Enable V3', 'job-settings', 'job-postings') ?></label>
										<div class="jobs-settings-input">
										<?php
											$option_name = 'jobs_recaptcha_type';
											$recaptcha_type = get_option( $option_name );
											$recaptcha_type = sanitize_text_field($recaptcha_type);
											if(empty($recaptcha_type)) $recaptcha_type = ''; // default

											echo '<input id="'.$option_name.'" type="checkbox" name="'.$option_name.'" '.checked($recaptcha_type, 'on', false).' value="on">';

											echo '<p class="description jfw_hint">'.__('By default reCaptcha V2 is used. When V3 is enabled, validation will happen in the background, without user interaction.', 'job-postings').'</p>';
										?>
										</div>
									</div>
							</div>
						</div><!-- .tab-wrapper (end) -->
					</div>

					<?php
					$key = 'jobs_company_logo';
					$html = '<script type="text/javascript">
									jQuery(document).ready(function(){
										var '.$key.'_custom_uploader;
											jQuery("#'.$key.'_upload_file_button").click(function(e) {
												e.preventDefault();
												console.log("click");
												if ('.$key.'_custom_uploader) {
													'.$key.'_custom_uploader.open();
													return;
												}
												'.$key.'_custom_uploader = wp.media.frames.file_frame = wp.media({
													title: "Choose Image",
													button: {
														text: "Choose file"
													},
													multiple: false
												});
												'.$key.'_custom_uploader.on("select", function() {
													attachment = '.$key.'_custom_uploader.state().get("selection").first().toJSON();
													jQuery("#'.$key.'_upload_file").val(attachment.url);
												});
												'.$key.'_custom_uploader.open();

											});
									});
								</script>';
					echo $html;
					?>

					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e( 'Save settings', 'job-postings' ); ?>" />
					</p>


				</div>

				<?php include_once(JOBPOSTINGSPATH.'admin/tabs/fields.php'); ?>

				<?php do_action('job-postings/settings/tabs/after'); ?>
			</form>
			
		</div>
	</div>



	<div class="wrap jobs_plugin_ads">
		<a href="https://www.blueglass.ch/websites-applikationen" target="_blank"><img src="<?php echo plugins_url( '../images/backend-settings-banner.png', __FILE__ ); ?>" alt="Plugin developed by Blueglass"></a>

	</div>

</div>
