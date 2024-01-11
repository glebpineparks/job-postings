=== Jobs for WordPress ===
Contributors: blueglassinteractive, cfoellmann
Tags: jobs, work, google, job, recruiter, structured data, json-ld, microdata, postings, employment, career, vacancy, hr, recruitment
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 2.7.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress plugin that make it easy to add job postings to your company’s website in a structured way.


== Description ==
Jobs for WordPress is a powerfull WordPress plugin that make it easy to add job postings to your company’s website in a structured way. While you can comfortably create and manage job postings in a very user-friendly way, they are also automatically structured with schema.org. Thus, they are technically easy to read for Google and have a high chance of being displayed and ranked well in search results and you can save on expensive postings on job platforms.


== Features ==
* Add, manage and categorize job listings using the familiar WordPress UI
* Adjust styles of the job postings to your needs with live preview under settings
* Preview of your job listing before it goes live - the preview matches the appearance of a live job listing
* The job listings are automatically formatted with structured data (JSON-LD)
* Visitors can search in your job postings
* Job postings are easy to implement via shortcodes or PHP function
* Job postings can be saved in PDF format
* Each listing can be customized with drag-and-drop - in terms of modules, structure, paragraph namings and order, etc.
* Applications can be easily clustered and filtered for a comfortable navigation
* Each listing can be tied to a particular application recipient / e-mail address
* Developer friendly — Custom Post Types, Single job template, a lot of hooks and filters implemented.
* Apply Form can be easily modified from Settings page.
* Look and feel of the plugin can be easily modified from Settings page.
* Applications protected from spam with invisible "honey pot" and additionally with Google reCaptcha.
* You can define default fields and sorting for new job postings.


== Installation ==

1. Upload plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use [job-postings] to print jobs listing

== Changelog ==

= 2.7.3 =
* Minor Fixes

= 2.7.2 =
* Minor Fixes (related to attachments)

= 2.7.1 =
* Minor Fixes

= 2.7.0 =
* Fixed a security issue concerning upload of files
* Removed deprecated FILTER_SANITIZE_STRING
* Improved the compatibility with Yoast SEO
* Optimized the Job Postings for Google Jobs

= 2.6.2 =
* Minor fixes

= 2.6.0 =
* Fixed XSS vulnerability (Medium severity)

= 2.5.11.2 =
* Bug fix: Email attachments
* Other minor fixes

= 2.5.11 =
* Fixed XSS vulnerability (Medium severity)
* Other minor fixes

= 2.5.10.2 =
* Large file validation and submittion fix

= 2.5.10.1 =
* SQL Injection vulnerability fix

= 2.5.10 =
* Fixed path in email attachments, that prevented attachments to get attached to the emails on some servers.

= 2.5.9 =

= 2.5.8 =
* Updated TCPDF library to support PHP8
* Fixed deprications to support PHP8

= 2.5.7 =
* Possible fix for missing styling

= 2.5.6 =
* Possible fix for missing styling

= 2.5.5 =
* [New] Merge tags added to Custom Notification messages. See here: Job posting > Notification > Custom Notification
* [Optimisation improvement] Plugin's scripts & styles now inluded only pages, where plugin shortcode is used and on job single page
* [Fix] Categories now show correct number of postings
* Minor improvements

= 2.5.4 =
* Fix for tinyMCE error on job edit screen

= 2.5.3 =
* Fix for not saving HTML in widgets

= 2.5.2 =
* Correction in employmentType

= 2.5.1 =
* UPDATED WPML depricated function.

= 2.5.0 =
* UPDATED employmentType to pass correct values to Google.
* Added "unitText" in Base Salary.
* Added new hook to change "View button" on list shortcode: apply_filters('job-postings/view_button', $view_button, $btn_name, $permalink, $target, $post_id);
* Added new hook to change address output if you need: apply_filters('job-postings/full_address', $icon . implode(', ', $full_address), $full_address, $icon);
* Fix for issue when fields/widgets are not visible after update from older version of the plugin. Fields can be set back from "disabled", content should be preserved.
* Fix for depricated jQuery load function


= 2.4.9 =
* Fix for tinyMCE error on job edit screen

= 2.4.8 =
* NEW: Added new hook to disable structured data output, use: add_filter('job-postings/disable_json_ld','__return_true');
* NEW: Added parameter "target" to [job-postings] shortcode, to define how links are opening (_self or _blank)
* Fixes reported by users

= 2.4.7 = 
* Fix error that preventer files upload

= 2.4.6 = 
* Fix for reCAPTCHA not validating propperly on some servers

= 2.4.5 = 
* Fixed error that removed attachments from emails

= 2.4.4 = 
* Fixed "Reply-To" value in email headers
* Fixed some erroras and warning in W3C Validator
* ReCaptcha validation improvement
* Now showshiring organisation name if added only name without logo.
* Adde filter hook "job-postings/json_ld" for updating JSON-LD fro the code. Use add_filter('job-postings/json_ld', function( $json_ld, $post_id ){ 
    // Manipulate wiht JSON-LD array here
    return $json_ld; 
})
* Adde filter hook "job-postings/validate_phone" for disabling phone validation. Use add_filter('job-postings/validate_phone', function(){
	return false;
});

= 2.4.3 = 
* Added better special characters handling

= 2.4.1 = 
* Updating settings now updates job posts parent, if archive page setting is changed. 
* Added HTML tag selector for field title.

= 2.4.1 = 
* Added support for WP Auto Embed in field content.

= 2.4.0 = 
* Indicator of completeness added. Available on job offer edit page.
* Umlauts (äöüõ etc) in uploaded file names now supported

= 2.3.11 = 
* Corrected date format on "Offer ended" message

= 2.3.10 = 
* Shortcodes in fields now rendered again

= 2.3.9 = 
* Fix for ValidThrough saved format.

= 2.3.8 =
* Adjustments in form submittion
* Adjustments in styles
* Fixed issue when WP not deleted files with GDPR setting enabled
* ValidThrough date now takes format from WP settings

= 2.3.7 =
* Inline form submition fixed
* Included simple UL and OL list styling


= 2.3.6 =
* Fix for pdf not showing cyrilic characters
* Fix for pdf language error 

= 2.3.5 =
* Updated form submition/confirmation method.
* Added class "hide_in_pdf" that excludes field from generatd PDF. Useful when you use external form and PDF generates error, this way we can awoid the form from PDF.
* Added job posting fields to WP JSOn API.
* Fixed email attachments when WP Media is used for files location.


= 2.3.4 =
* Fix for Block Editor categories

= 2.3.4 =
* Fix for inline form

= 2.3.2 =
* Added option to switch file storage location. On some shared hostings "Secure Location" not accessible due to limited server permissions.
* Datalists now excludes duplicates

= 2.3.1 =
* Added Hook to disable datalist's, use: add_filter('job-postings/datalists', '__return_false'); to disable all datalists, OR add_filter('job-postings/datalists/FIELD_KEY', '__return_false'); to disable datalist by key.

= 2.3.0 =
* Files now moved to secure location on the server after upload and accessible only from wp admin.
* Now possible to change hiring organization per job offer.
* Now possible to duplicate job offers out-of-the-box.
* New entries are now highlighted in the list.
* Fixed language parameter issue.
* Fixed some issue in PDF.
* Fixed "All" filter link.

= 2.2.9 =
* Fixed issue when selected frontpage were not used for some users when plugin is activated

= 2.2.8 =
* Removed some unused fonts from TCPDF

= 2.2.7.1 =
* Fixes

= 2.2.7 =
* Fixed validation on empty fields

= 2.2.6 =
* JS Error fix

= 2.2.5 =
* PDF generator library update.
* Added Hook "job-postings/pdf/font" pass it TCPDF font family name to change font inside pdf file.
* Added custom JavaScript events "application_success" and "application_error". You can use them to add tracking code to success or error submitions.
* Improved phone and email validation.
* Added Hiring organisation logo to JSON-LD output, thanks to @cfoellmann
* Added order and orderby parameters to shortcode, thanks to @cfoellmann
* Fixed date format for hidded ValidThrough field.
* Allowed H1-H6 in WP Editor fields.

= 2.2.4 =
* Added field setting to select html tag of the field
* Added field setting to place custom css class on the field
* Hook added to change output field html tag

= 2.2.3 =
* Included missing select2 script

= 2.2.2.1 =
* Small php typo fix

= 2.2.2 =
* Optimised loading of reCaptcha script. Now loads only on job single page and only when enabled
* Fixed buttons color

= 2.2.1 =
* Added Google reCaptcha V2 and invisible V3, configurable from plugin settings
* Added settings to define default fields and sorting for new job postings
* Added "dropdown" field for apply form
* Added "section" field for apply form 
* Updated file validation
* Some style adjustments

= 2.1.8 =
* Fixed: Submit error

= 2.1.7 =
* Fixed: Gutenberg Categories save issue

= 2.1.6 =
* Fixed: Missing Job Benefits field

= 2.1.5 =
* Fixed: Legacy email missing in email  

= 2.1.4 =
* Multilanguage slug base fixed
* Some improvements under the hood

= 2.1.3 =
* Fixed hidden fields in PDF

= 2.1.2 =
* Added Polylang support.
* Fixed email address in entry email to reply directly to sender.
* Plugin settings small visual changes.

= 2.1.1 =
* Fixed applicant email appearance in notification email, when default form fields used.
* Fixed issue for some themes that uses dynamic page loading.


= 2.1.0 =
* Added Single Job shortcode [job-single id="JOB_ID"].
* Added parameter "hide_past" to exclude job postings which "Valid Through" date is past.
* Added parameter "limit" to [job-postings] shortcode. Limits the output of job posts. If limit is used, filters and pagination are disabled.
* Added parameter "posts_per_page" to [job-postings] shortcode. Limits the number of jobs per page. By default set in settings, but can be rewriten from shortcode now.
* Fixed category job postings count.
* Small text changes.

= 2.0.4 =
* Entry fields small fixes
* Fixed pagination if job posting shortcode used on front page
* Fix for returning classes if no global $post exists in function related on "nav_menu_css_class"

= 2.0.3 =
* Some fixes

= 2.0.2 =
* Woocommerce php warning fixed

= 2.0.1 =
* Type correction in job listing shortcode.

= 2.0.0 =
* Fully refactored code.
* Added: Entries count bubble to Jobs top level menu.
* Added: Hide field option. Make fiel hidden on your site, but still available to validate by google.
* Added: Custom email text per job offer.
* Added: Custom confirmation page (redirect can be added after user submits apply form).
* Added: "Required" and "Recommended" next to the widget title, to let you know how Google treats them.
* Added: Remote job location. New setting in Job Location widget.



= 1.9.10 =
* Fix for hiringOrganisation been missed on inline apply form

= 1.9.9 =
* Fix for multiple checkboxes been saved under one
* Fix for multiple radio buttons been saved under one

= 1.9.8 =
* Fixed woocommerce (probably some other) search issue 
* Tested compatibility with WP5
* Fixed editors appearance on WP5

= 1.9.6 =
* Modal close button css fix
* Fixed issue with TCDPF, that prevented genaration of pdf if TCDPF was included in other plugin on theme already

= 1.9.5 =
* Fix for tabs

= 1.9.4 =
* Fix for double modal window apperance

= 1.9.3 =
* CSS corrections

= 1.9.2 =
* Fix for radio and checkbox fields
* Fix for apply button border


= 1.9.1 =
* Added upload file size validation and related settings fields.
* Added Search option for job postings.
* Added shortcode for Search field.
* Added shortcode for Categories listing.
* Added "show_filters" parameter to [job-postings] shortcode. Activates Search and Category filters.
* Added "show_count" parameter to  [job-postings] shortcode. Now possible to show how much job postings in each category.
* Placed Help as separate page.
* Translations added: Russian, Estonian, Finnish (in progress)
* Styling fixes.

= 1.8.3 =
* Entry Add/Update serialize error fix.

= 1.8.2 =
* Small adjustments in settings.

= 1.8.1 =
* Content editor now has full customization options enabled.
* Added hook to enable/disable Teeny editor. To enable teeny editor place this line to functions.php: add_filter('jobs-postings/tinymce_teeny', __return_true);

= 1.8.0 =
* Added widget for inline Apply now form. Notice: Only one widget can be used at a time, either "Apply now" or "Inline "Apply now" form".
* Added accepted file extensions to Apply modal single and multi file input.
* Added better required fields highlightning in Apply modal.
* Text editor now possible to resize on job edit page.
* Styling correcions.

= 1.7.9 =
* Shortcode [job-postings] options extended. Now showing child categories in the filer and also possible to show/hide empty categories with parameter.
* Style generator update.
* Translation update.

= 1.7.8 =
* On some WebKit browsers form were unable to submit properly. This release includes possible fix for form data submition and notification messages.

= 1.7.7 =
* Possible fix for PHP Notice in the log's on line 2511. Related to attachments redirection.

= 1.7.6 =
* Structured data removed from listing shortcodes, to avoid google errors for missing data.

= 1.7.5 =
* Bug fix

= 1.7.4 =
* Added column to the Jobs Category list, to see the category ID.

= 1.7.3 =
* Fix of bug that caused "Thank you" message to not appear after submit on some servers.

= 1.7.2 =
* Small cosmetic bug fix

= 1.7.1 =
* Added anonymous metrics collection

= 1.7.0 =
* Added Checkboxes to the apply now form (Now possible to add GDPR confirmations)
* Added Radio inputs to the apply now form
* New shortcode parameter "category", use as: [job-postings category="1,2"]
* Fixed issue for not showing user inputed data with default apply now form

= 1.6.16 =
* Small appearance issue fixed

= 1.6.15 =
* Made job posting to appear faster on page load
* Fixed translation issue for "Add file" string
* Added new filter "job-modal/add_file_text" to overwrite "Add file" text

= 1.6.14 =
* SVN version issue update

= 1.6.13 =
* Fix of Notice for missing value when it's not filled in posting

= 1.6.12 =
* Fix for flush rewrites that caused a server 500 error on some systems.

= 1.6.11 =
* Apply now settings correction

= 1.6.10 =
* Fixed template redirect issue, finally :)
* Job postings now appear in search. You can use "add_filter( 'jobs_post_type/exclude_from_search', '__return_true', 10, 2);" to exclude job posting from wp default search if needed.

= 1.6.9 =
* Fixed email issue
* Fixed template redirect issue

= 1.6.8 =
* New filter hook "job-entry/disable_featured_image"

= 1.6.7 =
* Maintenance release

= 1.6.6 =
* Maintenance release

= 1.6.5 =
* Fixed bug of "Valid thrue" field.

= 1.6.4 =
* Fixed issue when default "Apply Now" fields were not present in the email.

= 1.6.3 =
* Fixed issue preventing the appearance of Open Graph meta added by Yoast SEO Plugin

= 1.6.2 =
* Removed missing enqueing of modernizer file, used for experimenting.

= 1.6.1 =
* Fixed a bug in Styles setings

= 1.6.0 =
* Fixed the appearance of the uploaded files on the WP default attachment page. Now all new job entry uploads redirect to job posting. All older uploads redirect to homepage as they miss required ID (can be disabled with hook if needed).
* New filter hook "job-entry/noparent_attachment_page_redirect", return true or false, true by default
* New filter hook "job-entry/attachment_page_redirect", return true or false, true by default

= 1.5.10 =
* Recipient email sanitize function update, now allows to use multiple recipients separated by coma

= 1.5.9 =
* Small bug fix

= 1.5.8 =
* Fixed bug that prevented appearance of the Custom Text title in job posting and pdf
* Fixed appearance bug of Base Salary in PDF
* Fixed job featured image positioning

= 1.5.7 =
* Bug fix

= 1.5.6 =
* Added TCPDF files that got missed in latest release

= 1.5.5 =
* Added more templates that can be overriden from the theme: job-preview, job-categories
* Added option to hide Location and/or Employment Type from job preview. Options added to Style setting
* New filter hook "jobs/preview_details", usage: add_filter( 'jobs/preview_details', 'my_jobs_preview_details', 10, 2);
* New filter hook "jobs/preview_details_separator"
* New filter hook "jobs/preview_details_jobLocation"
* New filter hook "jobs/preview_details_employmentType"
* New filter hook "jobs/preview_details"
* New filter hook "jobs/categories_args"
* "Add new position" screen style updates
* Job post sidebar margin corrections

= 1.5.4 =
* Small "Add new position" screen design updates

= 1.5.3 =
* Fixed bug with Custom Button url

= 1.5.2 =
* Added "Name" field for specifiend name of the applicant. Required for better E-mail notifications.
* Adjusted email notifications to include new Entry fields.
* Added logo preview in settings Global options tab.
* Fixed email localisation.
* Fixed bug of validating empty multiple upload field.

= 1.5.1 =
* Fixed bug when tabs in settings dont work

= 1.5.0 =
* Added "Apply now" editor. You can now construct your own apply form.
* Some style corrections

= 1.4.5 =
* Microdata support is removed. Only JSON-LD now and by default.

= 1.4.4 =
* Minor update. jQuery UI style added

= 1.4.2 =
* Minor update. PDF now have smaller gaps between text areas

= 1.4.1 =
* Fixed issue that caused sending of multiple submitions when clicked on submit multiple times.

= 1.4.0 =
* Added category selector for the listing. Use [job-postings showcategory="true" aligncategory="left"] for showing categories above lthe list.
* Added shortcode parameters.
* Updated PDF output.
* Fixed some styling issues that appeared on some theme's

= 1.3.3 =
* Fix for saving pdf on iOS and macOS Safari.

= 1.3.2 =
* Position details display issue fixed.

= 1.3.1 =
* Added button and box roundnes option to the styles settings.
* Added "Custom CSS" area to styles settings, for adding any custom css you need.

= 1.3.0 =
* Added "Styles settings" with live preview. Collors now can be adapted to your needs easily.

= 1.2.8 =
* Readme update.

= 1.2.7 =
* Readme update.

= 1.2.6 =
* Added plugin version to the css and js files to force update cache.

= 1.2.5 =
* CSS typo fix.

= 1.2.4 =
* Added max width for job post. As appeared fullwidth on some themes.

= 1.2.3 =
* Currency symbol positioning fixed

= 1.2.2 =
* Modal appearance fixed

= 1.2.1 =
* Added Base Salary range.
* Loading glitches and short appearing of submit form fixed now.
* New filter added "job-postings/salary-range-separator". To change separater to any desired, if needed.

= 1.2.0 =
* Address field includes now includes "addressLocality", "addressRegion", "postalCode" and "streetAddress". Also shows more details on front end.
* Added "validThrough" field.
* Added "experienceRequirements", "educationRequirements" and "skills" fields.
* Improved Attachment field.
* Added option to replace submit button with "offer ended" text.

= 1.1.9 =
* Added message that visible when no job offers available. Can be changed in settings
* Some styling improvements

= 1.1.8 =
* Fixed language association from WPML

= 1.1.7 =
* Added default notification email to plugin settings
* Added permalink slug to plugin settings

= 1.1.6 =
* Some CSS fixes

= 1.1.5 =
* JSON-LD Updated

= 1.1.4 =
* Enqueueing jQuery

= 1.1.3 =
* Fix for file upload field. Now shows file name propperly

= 1.1.2 =
* Fixed listing shortcode, now printing positions in the right place

= 1.1.1 =
* Added 'jobs-listing/grid_class' filter for changing grid classes of the listing element.

= 1.1.0 =
* Added 'job-entry/after_submit' action for third party software integration and forwarding of submited data.
* Added new field "Button". You can use it for linking job position to third party job listing site or for adding CTA button to you ad.
* Added 'job-entry/notification' and 'job-entry/notification_{POST_ID_HERE}' filter for disabling email notification for all or some specific ad's

= 1.0.1 =
Entry search update

= 1.0.0 =
Initial release



== Upgrade Notice ==
= 1.6.3 =
* Critical bug fix

= 1.6.0 =
* Major update. You should update
* Fixes the entry uploads public appearance

= 1.5.0 =
* As we integrated "Apply now" editor, on all job entries page disapears the applicant data preview (name, email, phone, etc), because of structure change. This field's there now related to the new "Apply now" editor. On entry details page all the data still in place, no worries :)

= 1.3.0 =
* Major update. Added "Styles settings" with live preview.

= 1.1.8 =
Fixed language association from WPML

= 1.1.5 =
JSON-LD Specification updated, should update

= 1.1.4 =
Jquery loaded to frontend, please update if apply now button didnt worked for you

= 1.1.2 =
Should update as maijor fix added

= 1.1.0 =
New features added, should update

= 1.0.1 =
Search is now more accurate, you sould update

= 1.0.0 =
Initial release

== FAQ ==

FAQs on how to work with the plugin and where to change settings can be found within the plugin in the section Settings > Help.
