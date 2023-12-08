<?php
	wp_enqueue_media();

	$placehold = "Option 1
Option 2
Option 3";
?>

<div class="wrap jp-help-top">
	<h2 class=""><img src="<?php echo plugins_url( '../images/help.svg', __FILE__ ); ?>" width="30" alt=""><?php echo esc_html( get_admin_page_title() ); ?></h2>
</div>

<?php
$lang = Job_Postings::$lang;


if ( ! isset( $_REQUEST['settings-updated'] ) )
	$_REQUEST['settings-updated'] = 'false';

?>

<div class="wrap jobs_plugin_settings">

	<?php if ( 'false' !== sanitize_text_field($_REQUEST['settings-updated']) ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Settings updated', 'job-postings' ); ?></strong></p></div>
	<?php endif; ?>

	<div class="job_tabs">



		<div id="jobs_help" class="job_tab_content clearfix">
			<div class="wrap jp-help-wrap">

				<h2><?php echo _x('Shortcodes', 'job-settings', 'job-postings') ?></h2>
				<p><?php echo _x('Place this shortcode on the page, where you want to show your job postings or ui elements.', 'job-settings', 'job-postings') ?></p>

				<h3><?php echo _x('Jobs listing:', 'job-settings', 'job-postings') ?> (default)</h3>
				<p>
					<code>[job-postings]</code>
				</p>

				<h3><?php echo _x('Parameters:', 'job-settings', 'job-postings') ?></h3>
				<p>
					<code>[job-postings
						category="1,2"
						showcategory="false|true"
						aligncategory="left|center|right"
						hide_empty="true|false"
						show_count="false|true"
						show_filters="false|true"
						limit="number"
						posts_per_page="number"
						hide_past="false|true"
						target="_self|_blank"
						]</code>
				</p>
				<ul>
					<li><b>category</b> - <?php _ex('With this parameter you can show only job posting from defined categories. Add multiple categories separated by coma. If this parameter used, all others are ignored.', 'shortcode parameter "category"', 'job-postings'); ?></li>
					<li><b>showcategory</b> - <?php _ex('With this parameter you can show category filter above the job posts listing  (Default: false).', 'shortcode parameter "showcategory"', 'job-postings'); ?></li>
					<li><b>aligncategory</b> - <?php _ex('Category filter alignment (Default: left).', 'shortcode parameter "aligncategory"', 'job-postings'); ?></li>
					<li><b>hide_empty</b> - <?php _ex('To show or hide empty categories (Default: true).', 'shortcode parameter "hide_empty"', 'job-postings'); ?></li>
					<li><b>show_count</b> - <?php _ex('To show/hide count of job postings in each category.', 'shortcode parameter "show_count"', 'job-postings'); ?></li>
					<li><b>show_filters</b> - <?php _ex('To show/hide filters (categories and search field). This parameter overwrites all other and can be configured ftom "Settings > Styles"', 'shortcode parameter "show_filters"', 'job-postings'); ?></li>
					<li><b>limit</b> - <?php _ex('To limit the output of job posts. If limit is used, filters and pagination are disabled', 'shortcode parameter "limit"', 'job-postings'); ?></li>
					<li><b>posts_per_page</b> - <?php _ex('To limit the output of job posts per page.', 'shortcode parameter "posts_per_page"', 'job-postings'); ?></li>
					<li><b>hide_past</b> - <?php _ex('To exclude job postings which "Valid Through" date is past.', 'shortcode parameter "hide_past"', 'job-postings'); ?></li>
					<li><b>orderby</b> - <?php _ex('Choose the field to sort the job postings by (Default: date).', 'shortcode parameter "orderby"', 'job-postings'); ?></li>
					<li><b>order</b> - <?php _ex('Select between ascending "ASC" and descending "DESC" (Default: DESC).', 'shortcode parameter "order"', 'job-postings'); ?></li>
					<li><b>target</b> - <?php _ex('Link target attribute (Default: _self).', 'shortcode parameter "order"', 'job-postings'); ?></li>
				
				</ul>
				<br>

				<h3><?php echo _x('Jobs single:', 'job-settings', 'job-postings') ?> (default)</h3>
				<p>
					<code>[job-single id="JOB_ID"]</code>
				</p>
				<ul>
					<li><b>id</b> - <?php _ex('ID of the job to show.', 'shortcode parameter "id"', 'job-postings'); ?></li>
				</ul>
				<br>


				<h3><?php echo _x('Categories', 'job-settings', 'job-postings') ?></h3>
				<p>
					<code>[job-categories]</code>
				</p>
				<br>

				<h3><?php echo _x('Categories tree', 'job-settings', 'job-postings') ?></h3>
				<p>
					<code>[job-categories-tree show_count="false|true"]</code>
				</p>
				<ul>
					<li><b>show_count</b> - <?php _ex('To show/hide count of job postings in each category.', 'shortcode parameter "show_count"', 'job-postings'); ?></li>
				</ul>
				<br>


				<h3><?php echo _x('Search', 'job-settings', 'job-postings') ?></h3>
				<p>
					<code>[job-search]</code>
				</p>
				<br>

				<h3>PHP</h3>
				<p>
					<b><?php echo _x('To show jobs list somewhere in your template, use function below:', 'job-settings', 'job-postings') ?></b> <br>
					<code>&lt;?php if(function_exists('jobs_list')) jobs_list(); ?&gt;</code>
				</p>
				<br>


				<h3><?php echo _x('FAQ', 'job-settings', 'job-postings') ?></h3>
				<div class="toggle">
					<a class="trigger" href="#">
						<img src="<?php echo plugins_url( '../images/sort-down.svg', __FILE__ ); ?>" width="12" alt="">
						<?php echo _x('Why I dont receive email notifications?', 'job-settings', 'job-postings') ?>
					</a>
					<div class="toggle-box" style="display: none;">
						<?php echo _x('Check if your site is able to send any emails out. On some servers PHP mail() function is disabled. You will have to install one of the SMTP plugins and use it instead to allow your site to send emails out.', 'job-settings', 'job-postings') ?>
					</div>
				</div>
				<div class="toggle">
					<a class="trigger" href="#">
						<img src="<?php echo plugins_url( '../images/sort-down.svg', __FILE__ ); ?>" width="12" alt="">
						<?php echo _x('Where can I change the date format?', 'job-settings', 'job-postings') ?>
					</a>
					<div class="toggle-box" style="display: none;">
						<?php echo _x('The date format of the field is taken from the WordPress settings page', 'job-settings', 'job-postings') ?><br>
						<img class="box-img" src="<?php echo plugins_url( 'faq/faq-date.png', __FILE__ ); ?>" alt=""><br>
						<img class="box-img" src="<?php echo plugins_url( 'faq/faq-date-settings.png', __FILE__ ); ?>" alt="">
					</div>
				</div>

				<div class="toggle">
					<a class="trigger" href="#">
						<img src="<?php echo plugins_url( '../images/sort-down.svg', __FILE__ ); ?>" width="12" alt="">
						<?php echo _x('How can I change a field title?', 'job-settings', 'job-postings') ?>
					</a>
					<div class="toggle-box" style="display: none;">
						<?php echo _x("Every field has a \"gear\" icon in the top right corner, it opens the field's settings, where you can input your custom title", 'job-settings', 'job-postings') ?><br>
						<img class="box-img" src="<?php echo plugins_url( 'faq/faq-field-settings.png', __FILE__ ); ?>" alt="">
					</div>
				</div>

				<div class="toggle">
					<a class="trigger" href="#">
						<img src="<?php echo plugins_url( '../images/sort-down.svg', __FILE__ ); ?>" width="12" alt="">
						<?php echo _x('How can I hide/remove a title from appearing on the front-end?', 'job-settings', 'job-postings') ?>
					</a>
					<div class="toggle-box" style="display: none;">
						<?php echo _x('Every field has a "gear" icon in the top right corner, it opens a field settings, where you can hide a title by selecting a option', 'job-settings', 'job-postings') ?><br>
						<img class="box-img" src="<?php echo plugins_url( 'faq/faq-field-settings.png', __FILE__ ); ?>" alt="">
					</div>
				</div>

				<div class="toggle">
					<a class="trigger" href="#">
						<img src="<?php echo plugins_url( '../images/sort-down.svg', __FILE__ ); ?>" width="12" alt="">
						<?php echo _x('I want to hide/remove some fields, how can I do this?', 'job-settings', 'job-postings') ?>
					</a>
					<div class="toggle-box" style="display: none;">
						<?php echo _x('Every field has a "trash can" icon in the top right corner, by clicking it, the field is moved to the "Inactive widgets" section. You can also just drag and drop any field there. This will prevent it from appearing on the front-end.', 'job-settings', 'job-postings') ?><br><br>
						<?php echo _x('If a field is empty, it will also not appear on the front-end.', 'job-settings', 'job-postings') ?><br><br>
						<img class="box-img" src="<?php echo plugins_url( 'faq/faq-date.png', __FILE__ ); ?>" alt=""><br>
						<img class="box-img" src="<?php echo plugins_url( 'faq/faq-inactive.png', __FILE__ ); ?>" alt="">
					</div>
				</div>

				<div class="toggle">
					<a class="trigger" href="#">
						<img src="<?php echo plugins_url( '../images/sort-down.svg', __FILE__ ); ?>" width="12" alt="">
						<?php echo _x('How can I edit the "Thank you" message?', 'job-settings', 'job-postings') ?>
					</a>
					<div class="toggle-box" style="display: none;">
						<?php echo _x('The notification message can be edited on the job edit page, under the "Settings" tab', 'job-settings', 'job-postings') ?><br>
						<img class="box-img" src="<?php echo plugins_url( 'faq/faq-job-settings.png', __FILE__ ); ?>" alt="">
					</div>
				</div>

				<div class="toggle">
					<a class="trigger" href="#">
						<img src="<?php echo plugins_url( '../images/sort-down.svg', __FILE__ ); ?>" width="12" alt="">
						<?php echo _x('How can I change the e-mail address of the person, who receives confirmation emails?', 'job-settings', 'job-postings') ?>
					</a>
					<div class="toggle-box" style="display: none;">
						<?php echo _x('The confirmation e-mail address can be edited on the job edit page, under the "Settings" tab.', 'job-settings', 'job-postings') ?><br>
						<?php echo _x('By default, the recipient is the site admin.', 'job-settings', 'job-postings') ?><br>
						<img class="box-img" src="<?php echo plugins_url( 'faq/faq-job-settings.png', __FILE__ ); ?>" alt="">
					</div>
				</div>

				<div class="toggle">
					<a class="trigger" href="#">
						<img src="<?php echo plugins_url( '../images/sort-down.svg', __FILE__ ); ?>" width="12" alt="">
						<?php echo _x('Where are all the entries saved?', 'job-settings', 'job-postings') ?>
					</a>
					<div class="toggle-box" style="display: none;">
						<?php echo _x('All of the entries are saved in the "Job entries" subpage.', 'job-settings', 'job-postings') ?><br>
						<img class="box-img" src="<?php echo plugins_url( 'faq/faq-submits.png', __FILE__ ); ?>" alt="">
					</div>
				</div>

				<div class="toggle">
					<a class="trigger" href="#">
						<img src="<?php echo plugins_url( '../images/sort-down.svg', __FILE__ ); ?>" width="12" alt="">
						<?php echo _x('I have so much entries already, how can I navigate easier there?', 'job-settings', 'job-postings') ?>
					</a>
					<div class="toggle-box" style="display: none;">
						<?php echo _x('You can filter the entries by position.', 'job-settings', 'job-postings') ?><br>
						<img class="box-img" src="<?php echo plugins_url( 'faq/faq-filter.png', __FILE__ ); ?>" alt="">
						<br><br>
						<?php echo _x('You also can search entries by name, email or phone number.', 'job-settings', 'job-postings') ?><br>
						<img class="box-img" src="<?php echo plugins_url( 'faq/faq-search.png', __FILE__ ); ?>" alt="">
					</div>
				</div>

				<div class="toggle">
					<a class="trigger" href="#">
						<img src="<?php echo plugins_url( '../images/sort-down.svg', __FILE__ ); ?>" width="12" alt="">
						<?php echo _x('How can I personalize the PDF?', 'job-settings', 'job-postings') ?>
					</a>
					<div class="toggle-box" style="display: none;">
						<?php echo _x('You can add your logo in the "Settings" section under "Global options".', 'job-settings', 'job-postings') ?>
					</div>
				</div>

				<div class="toggle">
					<a class="trigger" href="#">
						<img src="<?php echo plugins_url( '../images/sort-down.svg', __FILE__ ); ?>" width="12" alt="">
						<?php echo _x('How can I add Structured Data to the fields?', 'job-settings', 'job-postings') ?>
					</a>
					<div class="toggle-box" style="display: none;">
						<?php echo _x("You don't have to, we already took care of structured data and added it in the right place.", 'job-settings', 'job-postings') ?><br>
						<?php echo _x('To test it, use this <a href="https://search.google.com/structured-data/testing-tool/" target="_blank">tool</a> and paste there a job url from your site there.', 'job-settings', 'job-postings') ?>
					</div>
				</div>




			</div>
		</div>
	</div>


	<div class="wrap jobs_plugin_ads">
		<a href="https://www.blueglass.ch/" target="_blank"><img src="<?php echo plugins_url( '../images/blueglass.jpg', __FILE__ ); ?>" alt="Plugin developed by Blueglass"></a>
		
	</div>

</div>
