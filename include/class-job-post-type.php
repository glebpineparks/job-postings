<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class JobPostType
{
    public static $query_instance;
    public static $count_entries = 0;

    public static function init(){

    	add_filter('jobs_post_type/slug', array('JobPostType', 'change_the_slug'));

    	add_action('init', array('JobPostType', 'register') );

    	add_action('restrict_manage_posts', array('JobPostType', 'restrict_listings_by_post_type') );
        add_filter('parse_query', array('JobPostType', 'parse_jobs_query') );
        

        add_filter( 'posts_join', array( 'JobPostType', 'search_metadata_join' ) );
        add_filter( 'posts_search', array( 'JobPostType', 'search_where' ), 10, 2 );
        add_filter( 'posts_request', array( 'JobPostType', 'distinct' ) );


        add_filter( 'manage_edit-jobs_category_columns', array('JobPostType', 'category_edit_columns') );
        add_filter( 'manage_jobs_category_custom_column', array('JobPostType', 'category_custom_columns'), 10, 3 );
        add_action( 'admin_head-edit-tags.php', array('JobPostType', 'category_column_width') );
        
        add_action('admin_menu', array('JobPostType','pending_posts_bubble') );

    }


    public static function search_where( $where, $wp_query ) {
        if ( !$wp_query->is_search() || !is_admin() )
            return $where;

        // Added in v1.9.8
        if ( $wp_query->query['post_type'] != 'job-entry' )
            return $where;


        self::$query_instance = &$wp_query;
        global $wpdb;

        $searchQuery = self::search_default();

        $searchQuery .= self::build_search_metadata();


        if ( $searchQuery != '' ) {
            // lets use _OUR_ query instead of WP's, as we have posts already included in our query as well(assuming it's not empty which we check for)
            $where = " AND ((" . $searchQuery . ")) ";
        }

        //print_r( $searchQuery );
        return $where;
    }

    //fix provided by Tiago.Pocinho
    public static function distinct( $query ) {
        if (!is_admin() )
            return $query;

        global $wpdb;
        if ( !empty( self::$query_instance->query_vars['s'] ) ) {
            if ( strstr( $query, 'DISTINCT' ) ) {}
            else {
                $query = str_replace( 'SELECT', 'SELECT DISTINCT', $query );
            }
        }
        return $query;
    }

    public static function search_metadata_join( $join ) {
        if ( !is_admin() )
            return $join;

        global $wpdb;

        if ( !empty( self::$query_instance->query_vars['s'] ) ) {
                $join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
        }
        return $join;
    }

    public static function build_search_metadata() {
        global $wpdb;
        $s = self::$query_instance->query_vars['s'];

        $search_terms = self::get_search_terms();
        $exact = ( isset( self::$query_instance->query_vars['exact'] ) && self::$query_instance->query_vars['exact'] ) ? true : false;
        $search = '';

        if ( !empty( $search_terms ) ) {
            // Building search query
            $searchand = '';
            foreach ( $search_terms as $term ) {
                $term = $wpdb->prepare("%s", $exact ? $term : "%$term%");

                $search .= "{$searchand}(meta_value LIKE $term)";

                $searchand = ' AND ';
            }
            $sentence_term = $wpdb->prepare("%s", $s);
            if ( count( $search_terms ) > 1 && $search_terms[0] != $sentence_term ) {

                $search = "($search) OR (meta_value LIKE $sentence_term)";

            }

            if ( !empty( $search ) )
                $search = " OR ({$search}) ";

        }

        return $search;
    }

    public static function build_search_tag() {
        global $wpdb;
        $vars = self::$query_instance->query_vars;

        $s = $vars['s'];
        $search_terms = self::get_search_terms();
        $exact = isset( $vars['exact'] ) ? $vars['exact'] : '';
        $search = '';

        if ( !empty( $search_terms ) ) {
            // Building search query
            $searchand = '';
            foreach ( $search_terms as $term ) {
                $term = $wpdb->prepare("%s", $exact ? $term : "%$term%");

                $searchand = ' OR ';
            }
            $sentence_term = $wpdb->prepare("%s", $s);

            if ( !empty( $search ) )
                $search = " OR ({$search}) ";
        }
        return $search;
    }

    public static function search_default(){
        global $wpdb;
        $not_exact = empty(self::$query_instance->query_vars['exact']);
        $search_sql_query = '';
        $seperator = '';
        $terms = self::get_search_terms();


        // if it's not a sentance add other terms
        $search_sql_query .= '(';

        foreach ( $terms as $term ) {
            $search_sql_query .= $seperator;

            $esc_term = $wpdb->prepare("%s", $not_exact ? "%".$term."%" : $term);

            $like_title = "($wpdb->posts.post_title LIKE $esc_term)";
            $like_post = "($wpdb->posts.post_content LIKE $esc_term)";

            $search_sql_query .= "($like_title OR $like_post)";

            $seperator = ' AND ';

            //$seperator = " AND ($wpdb->posts.post_type == 'job-entry') AND ";
        }

        $search_sql_query .= ')';
        return $search_sql_query;
    }

    public static function get_search_terms() {
        global $wpdb;
        $s = isset( self::$query_instance->query_vars['s'] ) ? self::$query_instance->query_vars['s'] : '';
        $sentence = isset( self::$query_instance->query_vars['sentence'] ) ? self::$query_instance->query_vars['sentence'] : false;
        $search_terms = array();

        if ( !empty( $s ) ) {
            // added slashes screw with quote grouping when done early, so done later
            $s = stripslashes( $s );
            if ( $sentence ) {
                $search_terms = array( $s );
            } else {
                preg_match_all( '/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $s, $matches );
                $search_terms = array_filter(array_map( create_function( '$a', 'return trim($a, "\\"\'\\n\\r ");' ), $matches[0] ));
            }
        }

        return $search_terms;
    }

    public static function change_the_slug( $slug ){

    	//print_r( Job_Postings::$lang );
    	//die();
		$custom_slug = get_option('jobs_custom_slug_'.Job_Postings::$lang );
		if( !empty($custom_slug) ) $slug = sanitize_key($custom_slug);

    	return $slug;
    }

    public static function pending_posts_bubble() {
        global $menu;

        $suffix = "?post_type=jobs";

        // Locate the key of 
        $key = self::recursive_array_search( "edit.php$suffix", $menu );
        
        if( !$key )
            return;

        if ( self::$count_entries ) {
            // Modify menu item
            $menu[$key][0] .= sprintf(
                '&nbsp;<span class="update-plugins count-%1$s"><span class="plugin-count">%1$s</span></span>',
                self::$count_entries
            );
        } 
    }

    public static function recursive_array_search( $needle, $haystack ) {
        foreach( $haystack as $key => $value ) 
        {
            $current_key = $key;
            if( 
                $needle === $value 
                OR ( 
                    is_array( $value )
                    && self::recursive_array_search( $needle, $value ) !== false 
                )
            ) 
            {
                return $current_key;
            }
        }
        return false;
    }

    public static function register(){
        global $wp_rewrite;


        Job_Postings::init_current_language();
        
        self::$count_entries = Job_Postings_Helper::get_new_entries();
        $count_entries_bubble = '';

        if ( self::$count_entries ) {
            // Modify menu item
            $count_entries_bubble = sprintf(
                '&nbsp;<span class="update-plugins count-%1$s"><span class="plugin-count">%1$s</span></span>',
                self::$count_entries 
            );
        }

    	$labels = array(
            'menu_name'          => __('Jobs','job-postings'),
			'name'               => __('Jobs','job-postings'),
			'singular_name'      => __('Job','job-postings'),
			'add_new'            => __('Add new position','job-postings'),
			'add_new_item'       => __('Add new position','job-postings'),
			'new_item'           => __('New job position','job-postings'),
			'edit_item'          => __('Update job','job-postings'),
			'view_item'          => __('View job','job-postings'),
			'all_items'          => __('All positions','job-postings'),
		);

    	$labels = apply_filters('jobs_post_type/labels', $labels);

    	$post_type_slug = apply_filters('jobs_post_type/slug', _x('job', 'jobs_slug', 'job-postings'));

		register_post_type( 'jobs',
			array(
				'labels' => $labels,
				'menu_position' => apply_filters('jobs_post_type/menu_position', 21),
				'_builtin' => false,
				'exclude_from_search' => apply_filters('jobs_post_type/exclude_from_search', false), // Exclude from Search Results
				'capability_type' => 'post',
				'public' => apply_filters('jobs_post_type/public', true),
				'show_ui' => true,
				'show_in_nav_menus' => false,
				'rewrite' => array(
					'slug' => $post_type_slug,
					'with_front' => FALSE,
				),
				'query_var' => "jobs", // This goes to the WP_Query schema
				'menu_icon' => apply_filters('jobs_post_type/menu_icon', 'dashicons-businessman'),
				'supports' => array(
					'title',
					'editor' => false,
					'author',
					'thumbnail'

                ),
                'show_in_rest'       => true,
                //'rest_base'          => 'jobs-api',
                'rest_controller_class' => 'WP_REST_Posts_Controller'
			)
		);

		register_taxonomy('jobs_category', 'jobs', array(
				'hierarchical' => true,
				'label' => _x('Categories', 'jobs_categories', 'job-postings'),
				'singular_name' => _x('Category', 'jobs_category', 'job-postings'),
				"rewrite" => true, "query_var" => true
		));



		$labels = array(
			'menu_name'          => __('Job entry','job-postings'),
			'name'               => __('Job entry','job-postings'),
			'singular_name'      => __('Job entry','job-postings'),
			'add_new'            => __('Add new entry','job-postings'),
			'add_new_item'       => __('Add new entry','job-postings'),
			'new_item'           => __('New job entry','job-postings'),
			'edit_item'          => __('Update entry','job-postings'),
			'all_items'          => __('Job entries','job-postings') . $count_entries_bubble
		);

    	$labels = apply_filters('job-entry_post_type/lables', $labels);

        $post_type_slug = apply_filters('job-entry_post_type/slug', _x('job-entry', 'jobs_entry_slug', 'job-postings'));
        
        $supports = apply_filters('job-entry_post_type/supports', array(
            'title',
            'editor' => false,
            'author' => false,
            'thumbnail' => false,
            'comments' => false
        ));

		register_post_type( 'job-entry',
			array(
				'labels' => $labels,
				'menu_position' => apply_filters('job-entry_post_type/menu_position', 10),
				'_builtin' => false,
				'exclude_from_search' => apply_filters('job-entry_post_type/exclude_from_search', true), // Exclude from Search Results
				'capability_type' => 'post',
				'public' => apply_filters('job-entry_post_type/public', false),
				'show_ui' => true,
				'show_in_nav_menus' => false,
				'rewrite' => array(
					'slug' => $post_type_slug,
					'with_front' => false,
				),
				'query_var' => "job-entry", // This goes to the WP_Query schema
				'menu_icon' => apply_filters('job-entry_post_type/menu_icon', 'dashicons-businessman'),
		    	'show_in_menu' => 'edit.php?post_type=jobs',
                'supports' => $supports,
			)
		);

        //Flush only on front end, as otherwice gutenberg categories in combination with yoast are broken
        if( !is_admin() && !self::jobs_is_wp_rest() ){
            $wp_rewrite->flush_rules( false );
        }

    }

    public static function jobs_is_wp_rest() {
        $query = $_SERVER['REQUEST_URI'];
        if ( strpos($query, '/wp-json/') !== false ) {
            return true;
        }
        return false;
    }


	public static function parse_jobs_query($query) {
	    global $pagenow;

	    if ($pagenow=='edit.php' && isset($_GET['by_position']) && $_GET['by_position']!='') {
	    	//$qv['post_parent'] = $_GET['by_position'];
	    	$query->query_vars['post_parent'] = $_GET['by_position'];
	       //print_r( $query );
	    }

	}

    public static function restrict_listings_by_post_type() {
	    global $typenow, $wp_query;

	    if ( $typenow == 'job-entry' || isset($_GET['post_type']) && $_GET['post_type'] == 'job-entry' ) {

	    	remove_filter('parse_query', array('JobPostType', 'parse_jobs_query') );

	    	$jobs = get_posts(array(
	    		'post_type' => 'jobs',
	    		'post_status' => 'publish',
	    		'post_parent' => ''
	    		));

	    	$active = isset($_GET['by_position'])? $_GET['by_position'] : '';

	    	if( $jobs ){
	    		$out = '<select name="by_position">';
	    		$out .= '<option value="">'.__('Filter by position', 'job-postings').'</option>';
	    		foreach ($jobs as $key => $job) {
	    			$post_id = $job->ID;
	    			$position = get_post_meta($post_id, 'position_title', true);
	    			$out .= '<option value="'.$post_id.'" '.selected($active, $post_id, false).'>'.$position.'</option>';
	    		}
	    		$out .= '</select>';
	    		echo $out;
	    	}
	    }
    }
    

    /**
     * Register the ID column
     */
    public static function category_edit_columns( $columns ){
        $in = array( "cat_id" => "ID" );
        $columns = self::category_array_push_after( $columns, $in, 0 );
        return $columns;
    }

    /**
     * Print the ID column
     */
    public static function category_custom_columns( $value, $name, $cat_id ){
        if( 'cat_id' == $name )
            echo $cat_id;
    }

    /**
     * CSS to reduce the column width
     */
    public static function category_column_width(){
        // Tags page, exit earlier
        if( 'jobs_category' != $_GET['taxonomy'] )
            return;

        echo '<style>.column-cat_id {width:5%}</style>';
    }

    /**
     * Insert an element at the beggining of the array
     */
    public static function category_array_push_after( $src, $in, $pos ){
        if ( is_int( $pos ) )
            $R = array_merge( array_slice( $src, 0, $pos + 1 ), $in, array_slice( $src, $pos + 1 ) );
        else
        {
            foreach ( $src as $k => $v )
            {
                $R[$k] = $v;
                if ( $k == $pos )
                    $R = array_merge( $R, $in );
            }
        }
        return $R;
    }
}