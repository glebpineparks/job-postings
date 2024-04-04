<?php

if( !class_exists('JobSearch') ){
    class JobSearch
    {
        public static function render_search(){

            $jobs_archive_page = get_option('jobs_archive_page_' . jfw_get_lang());
            $jobs_page_permalink = get_permalink( $jobs_archive_page );
    
            $category = isset($_GET['job-category']) ? sanitize_text_field($_GET['job-category']) : '';
            $search 	= isset($_GET['job-search']) ? sanitize_text_field($_GET['job-search']) : '';
    
            $out = '';
            $out .= '<div class="jobs-search">';
                $out .= '<form method="GET" action="'.$jobs_page_permalink.'">';
                    $out .= '<input type="hidden" value="'.esc_attr($category).'" name="job-category">';
                    $out .= '<input class="job-search" type="text" placeholder="'.__('Vacancy Search', 'job-postings').'" value="'.esc_attr($search).'" name="job-search">';
                    $out .= '<button class="job-search-submit">'.Job_Postings_Helper::getRawSvg('search.svg').'</button>';
    
                $out .= '</form>';
            $out .= '</div>';
    
            return $out;
        }
    
        public static function do_job_search(){
            wp_enqueue_style('jp-front-styles');
            Job_Postings::customStyles();
            $out = '';
            $out .= '<div class="job-postings-shortcode-search">';
                $out .= self::render_search();
            $out .= '</div>';
            return $out;
        }
    }
}
