<?php

if( !class_exists('JobList') ){
    class JobList
    {

        public static function do_job_list( $atts = array(), $content = '' ){
            extract(
                shortcode_atts(
                    array(
                        'category' => '',
                        'showcategory' => 'false',
                        'aligncategory' => 'left',
                        'hide_empty' => 'true',
                        'show_count' => 'false',
                        'show_filters' => 'false',
                        'limit' => '',
                        'posts_per_page' => apply_filters('jobs/per_page', get_option('jobs_posts_per_page')),
                        'hide_past' => false,
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'target' => '_self',
                    ),
                    $atts
                )
            );

            $out = '';

            wp_enqueue_style('jp-front-styles');
            Job_Postings::customStyles();


            $hide_empty = ($hide_empty == 'true') ? true:false;

            if( $limit == '' && $showcategory == 'true' && $show_filters == 'false' ) {
                $out .= JobCategory::do_job_categories( $aligncategory, $category, $hide_empty, $show_count );
            }

            if( $limit == '' && $show_filters == 'true' ){
                $filter_class = get_option('jobs_filters_styles');
                $filter_class = $filter_class ? $filter_class : 'filter-style-1';
                $out .= '<div class="job-postings-filters clearfix '.$filter_class.'">';
                    $out .= JobCategory::do_job_categories( $aligncategory, $category, $hide_empty, $show_count );

                    $out .= JobSearch::render_search();
                $out .= '</div>';
            }

            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            if($paged == 1) $paged = get_query_var('page') ? get_query_var('page') : 1;

            $args = array(
                    'post_type'      => 'jobs',
                    'orderby'        => $orderby,
                    'order'          => $order,
                    'posts_per_page' => $posts_per_page
                );

            if( strpos($orderby, 'position_') !== false ){
                $args['orderby'] = 'meta_value';
                $args['meta_key'] = $orderby;
            }

            //var_dump( $order );
            // print_r( $args );
            
            if( $limit && $limit != '' ){
                $args['posts_per_page'] = $limit;
                //$args['nopaging'] = true;
            }

            if( $category ){
                $category = Job_Postings_Helper::numbers_separator_only( $category );
                $category = explode(',', $category);

                $args['tax_query'][] = array(
                        'taxonomy' => 'jobs_category',
                        'field'    => 'term_id',
                        'terms'    => $category
                    );
            }

            if( (isset($_GET['job-category']) && $_GET['job-category'] != '' && $_GET['job-category'] != 'all') ){
                $jobCat = $_GET['job-category'];
                $args['tax_query'][] = array(
                        'taxonomy' => 'jobs_category',
                        'field'    => 'slug',
                        'terms'    => $jobCat
                    );
            }



            if( !$category && (isset($_GET['job-search']) && strip_tags($_GET['job-search']) != '') ){
                $search = strip_tags($_GET['job-search']);
                global $wpdb;

                $keyword = strip_tags($_GET['job-search']);
                $keyword = '%' . $wpdb->esc_like( $keyword ) . '%'; // Thanks Manny Fleurmond

                // Search in all custom fields
                $post_ids_meta = $wpdb->get_col( $wpdb->prepare( "
                    SELECT DISTINCT post_id FROM {$wpdb->postmeta}
                    WHERE meta_value LIKE '%s'
                ", $keyword ) );

                // Search in post_title and post_content
                $post_ids_post = $wpdb->get_col( $wpdb->prepare( "
                    SELECT DISTINCT ID FROM {$wpdb->posts}
                    WHERE post_type = 'jobs'
                    AND post_title LIKE '%s'
                    OR post_content LIKE '%s'
                ", $keyword, $keyword ) );

                $post_ids = array_merge( $post_ids_meta, $post_ids_post );

                //'post__in'    => $post_ids,
                $args['post__in'] = $post_ids;
            }


            if( $hide_past == true ){
                $args['meta_query'] = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'position_valid_through_date',
                        'value' => date( 'Y-m-d' ),
                        'compare' => '>=',
                        'type' => 'DATE'
                    ),
                    // Show job posts that dont have date set
                    array(
                        'key' => 'position_valid_through',
                        'value' => '',
                        'compare' => '=',
                    )
                );
            }


            //if( current_user_can('manage_options') ){
            //	print_r('<pre>');
            //	print_r( $args );
            //	print_r('</pre>');
            //}


            $args = apply_filters( 'jobs/listing_query', $args );
            $args['paged'] = $paged;

            // print_r('<pre>');
            // print_r( $args );
            // print_r('</pre>');

            $jobs = new WP_Query($args);


            // we use foundation and bootstraps column class by default
            $class = apply_filters('jobs-listing/grid_class', 'column medium-12 col-md-12');
            $class = sanitize_text_field( $class );

            if ( $jobs->have_posts() ) {


                $out .= '<div class="job-listing row clearfix">';
                    $out .= '<div class="'.$class.'">';

                        while ( $jobs->have_posts() ) {
                            $jobs->the_post();

                            $post_id = $jobs->post->ID;

                            $out .= self::job_preview( $post_id, $target );
                        }

                    $out .= '</div>';
                $out .= '</div>';

                if($limit == ''){
                    $out .= self::jobs_corenavi( $jobs );
                }

                $return = $out;

                /* Restore original Post Data */
                wp_reset_postdata();

            }else{

                $message = _x('Currently no job offers available.', 'job-message', 'job-postings');
                if( get_option('jobs_no_jobs_message'.'_'.Job_Postings::$lang) != '' ) $message = get_option('jobs_no_jobs_message'.'_'.Job_Postings::$lang);

                $out .= '<div class="job-listing row clearfix">';
                    $out .= '<div class="'.$class.'">';
                        $out .= '<div class="no-jobs-available">';
                            $out .= '<p>'. $message .'</p>';
                        $out .= '</div>';
                    $out .= '</div>';
                $out .= '</div>';

                $return = $out;
            }

            return $return;
        }

        public static function job_preview( $post_id, $target ){

            $btn_name 			= get_option('jobs_preview_cta'.'_'.Job_Postings::getLang() );
            if(!$btn_name)
                $btn_name = _x('View', 'job-postings', 'job-postings');

            $position_title 	= get_post_meta($post_id, 'position_title', true);
            $job_location 		= get_post_meta($post_id, 'position_job_location', true);
            $employment_type 	= get_post_meta($post_id, 'position_employment_type', true);
            $permalink 			= get_permalink($post_id);

            $custom_message 	= get_post_meta($post_id, 'job_custom_message', true);

            $preview_location 			= get_option( 'jobs_preview_location' );
            $preview_employment_type 	= get_option( 'jobs_preview_employment_type' );

            $fields 	        = Job_Postings::$fields;
            $list               = array();
            if( is_array($employment_type) && !empty($fields) && isset($fields['position_employment_type']['options']) ){
                $options = $fields['position_employment_type']['options'];
                foreach ($employment_type as $vk => $value_key) {
                    if( isset($options[$value_key]) && $value_key != 'OTHER' ) {
                        $list[] = $options[$value_key];
                    }
                }
                if( isset($employment_type['other_input']) ){
                    $list[] = $employment_type['other_input'];
                }
            }else if( !is_array($employment_type) && $employment_type != '' ){
                $list[] = $employment_type;
            }

            $employment_type = apply_filters('job-postings/format_list', implode(', ', $list), $list);


            // We write output to this variable
            $out = '';

            // Check for the file in theme, if no file to override, use default from the plugin
            if ( $overridden_template = locate_template( 'jobs/preview/job-preview.php' ) ) {
                include( $overridden_template );
            } else {
                include( JOBPOSTINGSPATH . 'templates/preview/job-preview.php' );
            }

            return $out;
        }
        

        public static function jobs_corenavi($loop_arr = null) {
            global $wp_query;
            
            $or_query = $wp_query;

            if($loop_arr) $wp_query = $loop_arr;

            $max = $wp_query->max_num_pages ? $wp_query->max_num_pages : get_query_var('paged');
            //if ($current != get_query_var('paged')) $current = 1;

            $args = array(
                'base' => str_replace(999999999, '%#%', get_pagenum_link(999999999)),
                'total' => $max,
                'current' => get_query_var('paged') ? get_query_var('paged') : 1,
                'mid_side' => 3,
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;'
            );

            $return = '';

            if($max > 1){
                $return .= '<div class="pagination column medium-12">' . paginate_links($args) . '</div>';
            }

            if($loop_arr) $wp_query = $or_query;

            return $return;
    }


    }
}