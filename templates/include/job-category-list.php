<?php
/*
	Categories list template

*/

$active_all = '';

if( isset($_GET['job-category']) && strip_tags($_GET['job-category']) != ''  && strip_tags($_GET['job-category']) == 'all' ){
	$active_all = 'active';
}

$jobs_archive_page = get_option('jobs_archive_page_' . jfw_get_lang());
$jobs_page_permalink = get_permalink( $jobs_archive_page );


function jobs_custom_taxonomy_walker($taxonomy, $parent = 0, $jobs_page_permalink, $show_count = false){

    $terms = get_terms($taxonomy, array('parent' => $parent, 'hide_empty' => true));

    if(count($terms) > 0){
    	$class = 'job-tree-categories';
    	if( $parent != 0 ) $class = 'sub-menu';

        //Displaying as a list
        $out = '<ul class="'.$class.'">';

        //Cycle though the terms
        foreach ($terms as $term){
					$count = '';

					if( $show_count ){
						$args = array(
							'post_type' 	=> 'jobs',
							'post_status' 	=> 'publish',
							'posts_per_page' 	=> -1,
							'supress_filters' 	=> false,
							'tax_query' 		=> array(
								array(
									'taxonomy' => 'jobs_category',
									'field'    => 'term_id',
									'terms'    => array( $term->term_id ),
								),
							),
						);

						$args = apply_filters( 'jobs/listing_query', $args );

						$args['posts_per_page'] = -1;

						$count = get_posts( $args );
						
						//print_r( $count );
						$count = count($count);
						$count = apply_filters('job-postings/category_tree_count', ' <span class="category">('.$count.')</span>');
					}

          $out .='<li><a href="' . $jobs_page_permalink . '?job-category='.esc_attr( $term->slug ).'">' . $term->name . $count . jobs_custom_taxonomy_walker($taxonomy, $term->term_id, $jobs_page_permalink, $show_count) . '</a></li>';
        }
        $out .= "</ul>";
        return $out;
    }
    return;
}


$out .= jobs_custom_taxonomy_walker('jobs_category', 0, $jobs_page_permalink, $show_count);


//
// NB! No ooutput/write here. We return output ($out) from the function.
//
