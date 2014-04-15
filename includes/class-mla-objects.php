<?php
/**
 * Media Library Assistant Custom Taxonomy and Widget objects
 * @package Media Library Assistant
 * @since 0.1
 */

/**
 * Class MLA (Media Library Assistant) Objects defines and manages custom taxonomies for Attachment Categories and Tags
 *
 * @package Media Library Assistant
 * @since 0.20
 */
class MLAObjects {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 0.20
	 *
	 * @return	void
	 */
	public static function initialize() {
		self::_build_taxonomies();
		
		add_action('admin_enqueue_scripts','MLAObjects::my_admin_scripts');
		add_action('delete_term_attachment_category', 'MLAObjects::remove_term_meta');
		add_action('attachment_category_edit_form_fields','MLAObjects::views_count');
		add_action('attachment_category_edit_form_fields','MLAObjects::featured_image');
		add_action('attachment_category_add_form_fields','MLAObjects::featured_image');
		add_action('edit_attachment_category','MLAObjects::save_album_cover');
		
	}
	
	/**
	 *
	 * Adds custom taxonomy form fields to edit screen
	 *
	 **/
	 
	 public static function views_count( $tag ) {
	 	$t_id = $tag->term_id;
	 	$count_key = 'term_views_count';
	 	$term_meta = get_term_meta( $t_id, $count_key, true );
	 	$count = !empty( $term_meta ) ? $term_meta : '0';
		
		include_once('views/templates/view-term-view-count.php');
	 
	 }
	 
	 
	 /**
	  *
	  * Adds a Featured Image Selection To Taxonomy Edit/Add Screens
	  *
	  **/
	  
	  public static function featured_image( $tag ) { 

		  $t_id = $tag->term_id;
		  $count_key = 'term_album_cover';
		  $term_meta = get_term_meta( $t_id, $count_key, true );

          include_once('views/templates/view-featured-image.php');

	  }

    /**
     *
     * Saves the Featured Image Selection
     *
     **/
     
	public static function save_album_cover( $term_id ) {
		
		if ( isset( $_POST['term_meta'] ) ) {
    	    $t_id = $term_id;
    	    $count_key = 'term_album_cover';
        	$term_meta = get_term_meta( $t_id, $count_key, true );
	        
	        if ( $term_meta == '' ) {
				add_term_meta( $t_id, $count_key, $_POST['term_meta'] );
			} else {
				update_term_meta( $t_id, $count_key, $_POST['term_meta'] );
			}
    	}


	}
	
	/**
	 *
	 * Removes any associated meta with a term after deletion
	 *
	 * @param	int	$term_id	gives us the term_id to delete
	 *
	 **/
	public static function remove_term_meta( $term_id ) {

		$album_cover_key = 'term_album_cover';
		$views_count_key = 'term_views_count';
		$album_cover = get_term_meta( $term_id, $album_cover_key, true );
		$views_count = get_term_meta( $term_id, $views_count_key, true );
		
		//if their is an album cover associated with the deleted term let's remove it
		if ( $album_cover )
			delete_term_meta( $term_id, $album_cover_key, $album_cover );
		
		//if their is a view count stored for the associated term let's remove it
		if( $views_count )
			delete_term_meta( $term_id, $views_count_key, $views_count );
			
	}
	 
	/**
	 *
	 *
	 * Adds script to open Media Selection on Custom Taxonomy Edit Screens
	 *
	 *
	 **/
	 
	public static function my_admin_scripts() {
	        wp_enqueue_media();
	        wp_register_script('featured-image-js', WP_PLUGIN_URL.'/media-library-assistant/js/featured-image.js', array('jquery'));
        	wp_enqueue_script('featured-image-js');
	}

	/**
	 * Registers Attachment Categories and Attachment Tags custom taxonomies, adds taxonomy-related filters
	 *
	 * @since 0.1
	 *
	 * @return	void
	 */
	private static function _build_taxonomies( ) {
		if ( MLAOptions::mla_taxonomy_support('attachment_category') ) {
			$labels = array(
				'name' => _x( 'Albums', 'taxonomy general name' ),
				'singular_name' => _x( 'Album', 'taxonomy singular name' ),
				'search_items' => __( 'Search Albums' ),
				'all_items' => __( 'All Albums' ),
				'parent_item' => __( 'Parent Album' ),
				'parent_item_colon' => __( 'Parent Album:' ),
				'edit_item' => __( 'Edit Album' ),
				'update_item' => __( 'Update Album' ),
				'add_new_item' => __( 'Add New Album' ),
				'new_item_name' => __( 'New Album Name' ),
				'menu_name' => __( 'Albums' ) 
			);
			
			register_taxonomy(
				'attachment_category', 'attachment',
				array(
				  'hierarchical' => true,
				  'labels' => $labels,
				  'show_ui' => true,
				  'query_var' => true,
				  'rewrite' => array( 'slug'	=>	'albums' ) 
				)
			);
		}
		
		if ( MLAOptions::mla_taxonomy_support('attachment_tag') ) {
			$labels = array(
				'name' => _x( 'Tags', 'taxonomy general name' ),
				'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
				'search_items' => __( 'Search Tags' ),
				'all_items' => __( 'All Tags' ),
				'parent_item' => __( 'Parent Tag' ),
				'parent_item_colon' => __( 'Parent Tag:' ),
				'edit_item' => __( 'Edit Tag' ),
				'update_item' => __( 'Update Tag' ),
				'add_new_item' => __( 'Add New Tag' ),
				'new_item_name' => __( 'New Tag Name' ),
				'menu_name' => __( 'Tags' ) 
			);
			
			register_taxonomy(
				'attachment_tag',
				array( 'attachment' ),
				array(
				  'hierarchical' => false,
				  'labels' => $labels,
				  'show_ui' => true,
				  'update_count_callback' => '_update_post_term_count',
				  'query_var' => true,
				  'rewrite' => true 
				)
			);
		}
		
		$taxonomies = get_taxonomies( array ( 'show_ui' => true ), 'names' );
		foreach ( $taxonomies as $tax_name ) {
			if ( MLAOptions::mla_taxonomy_support( $tax_name ) ) {
				register_taxonomy_for_object_type( $tax_name, 'attachment');
				if (  'checked' == MLAOptions::mla_get_option( 'attachments_column' )
) {

					add_filter( "manage_edit-{$tax_name}_columns", 'MLAObjects::mla_taxonomy_get_columns_filter', 10, 1 ); // $columns
					add_filter( "manage_{$tax_name}_custom_column", 'MLAObjects::mla_taxonomy_column_filter', 10, 3 ); // $place_holder, $column_name, $tag->term_id
				} // option is checked
			} // taxonomy support
		} // foreach
	} // _build_taxonomies
	
	/**
	 * WordPress Filter for edit taxonomy "Attachments" column,
	 * which replaces the "Posts" column with an equivalent "Attachments" column.
	 *
	 * @since 0.30
	 *
	 * @param	array	column definitions for the edit taxonomy list table
	 *
	 * @return	array	updated column definitions for the edit taxonomy list table
	 */
	public static function mla_taxonomy_get_columns_filter( $columns ) {
		/*
		 * Adding or inline-editing a tag is done with AJAX, and there's no current screen object
		 */
		if ( isset( $_POST['action'] ) && in_array( $_POST['action'], array( 'add-tag', 'inline-save-tax' ) ) ) {
			$post_type = !empty($_POST['post_type']) ? $_POST['post_type'] : 'post';
		}
		else {
			$screen = get_current_screen();
			$post_type = !empty( $screen->post_type ) ? $screen->post_type : 'post';
		}

		if ( 'attachment' == $post_type ) {
			if ( isset ( $columns[ 'posts' ] ) )
				unset( $columns[ 'posts' ] );
				
			$columns[ 'attachments' ] = 'Attachments';
		}
		
		return $columns;
	}
	
	/**
	 * WordPress Filter for edit taxonomy "Attachments" column,
	 * which returns a count of the attachments assigned a given term
	 *
	 * @since 0.30
	 *
	 * @param	string	current column value; always ''
	 * @param	array	name of the column
	 * @param	array	ID of the term for which the count is desired
	 *
	 * @return	array	HTML markup for the column content; number of attachments in the category
	 *					and alink to retrieve a list of them
	 */
	public static function mla_taxonomy_column_filter( $place_holder, $column_name, $term_id ) {
		/*
		 * Adding or inline-editing a tag is done with AJAX, and there's no current screen object
		 */
		if ( isset( $_POST['action'] ) && in_array( $_POST['action'], array( 'add-tag', 'inline-save-tax' ) ) ) {
			$taxonomy = !empty($_POST['taxonomy']) ? $_POST['taxonomy'] : 'post_tag';
		}
		else {
			$screen = get_current_screen();
			$taxonomy = !empty( $screen->taxonomy ) ? $screen->taxonomy : 'post_tag';
		}

		$term = get_term( $term_id, $taxonomy );
		
		if ( is_wp_error( $term ) ) {
			error_log( "ERROR: mla_taxonomy_column_filter( {$taxonomy} ) - get_term " . $term->get_error_message(), 0 );
			return 0;
		}
		
		$request = array (
//			'fields' => 'ids',
			'post_type' => 'attachment', 
			'post_status' => 'inherit',
			'orderby' => 'none',
			'nopaging' => true,
			'posts_per_page' => 0,
			'posts_per_archive_page' => 0,
			'update_post_term_cache' => false,
			'tax_query' => array(
				array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'terms' => $term->slug,
					'include_children' => false 
				) )
				);
				
		$results = new WP_Query( $request );
		if ( ! empty( $results->error ) ){
			error_log( "ERROR: mla_taxonomy_column_filter( {$taxonomy} ) - WP_Query " . $results->error, 0 );
			return 0;
		}

		$tax_object = get_taxonomy($taxonomy);

		return sprintf( '<a href="%1$s">%2$s</a>', esc_url( add_query_arg(
				array( 'page' => MLA::ADMIN_PAGE_SLUG, 'mla-tax' => $taxonomy, 'mla-term' => $term->slug, 'heading_suffix' => urlencode( $tax_object->label . ':' . $term->name ) ), 'upload.php' ) ), number_format_i18n( $results->post_count ) );
	}
} //Class MLAObjects


/**
 * Class MLA (Media Library Assistant) Text Widget defines a shortcode-enabled version of the WordPress Text widget
 *
 * @package Media Library Assistant
 * @since 1.60
 */
class MLAGallery extends WP_Widget {

	/**
	 * Provides a unique name for the plugin text domain
	 */
	const MLA_TEXT_DOMAIN = 'media_library_assistant';
	public static $tableName = "terms";
	public static $mime_type = "";
	public static $thumb_content = "";

	/**
	 * Calls the parent constructor to set some defaults.
	 *
	 * @since 1.60
	 *
	 * @return	void
	 */
	function __construct() {
		$widget_args = array(
			'classname' => 'mla_gallery_widget',
			'description' => __( 'Creates a Taxonomy based Photo Gallery', self::MLA_TEXT_DOMAIN )
		);
			
		$control_args = array(
			'width' => 400,
			'height' => 350
		);
		$theme = wp_get_theme();
		
		parent::__construct( 'mla-gallery-widget', __( 'MLA Gallery', self::MLA_TEXT_DOMAIN ), $widget_args, $control_args );
		//Enqueue Public Widget Scripts
		add_action( 'wp_head', 'MLAGallery::enqueue_scripts' );
		add_action( 'wp_head', 'MLAGallery::enqueue_styles' );
		// Include the Ajax library on the front end
		add_action( 'wp_head', 'MLAGallery::add_ajax_library' );
		//ajax actions
		add_action('wp_ajax_get_cat_views', 'MLAGallery::get_cat_views' );
		add_action('wp_ajax_set_cat_views', 'MLAGallery::set_cat_views' );		
		// Remove issues with prefetching adding extra views
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
		// Modify Item Template
		add_filter( 'mla_gallery_item_template', 'MLAGallery::mla_gallery_item_template_filter', 10, 1 );
		add_filter( 'mla_gallery_item_values', 'MLAGallery::mla_gallery_item_values_filter', 10, 1 );
		//adds custom query vars to WP_Query
		add_filter( 'query_vars', 'MLAGallery::add_query_vars_filter' );
		//if theme is headway base hook into the content query and overwrite it otherwise we will just use the_content filter
		if ( $theme->get( 'Name' ) == "Headway Base" ) {
			add_action('headway_block_content_content', 'MLAGallery::change_archive_content'); //Get Custom Content
			add_filter('headway_taxonomy_archive_title', 'MLAGallery::remove_archive_title'); //Removes default Archive Title we have this in our custom content
		} else {
			add_filter('archive_template', 'MLAGallery::get_template'); //Get Custom Content
		}
		//add_filter('get_terms', 'MLAGallery::custom_term_sort', 10, 3);
	}

	/**
 	 * Adds the WordPress Ajax Library to the frontend.
 	 */
	public static function add_ajax_library() {
 
    	$html = '<script type="text/javascript">';
        	$html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '"';
	    $html .= '</script>';
 
	    echo $html;
 
	} // end add_ajax_library
	
	public static function add_query_vars_filter( $vars ){
		$vars[] = "media_search";
		$vars[] = "sort_by";
		$vars[] = "mla_paginate_current";
		return $vars;
	}
	
	/**
	 * Register and enqueue public-facing Javascript.
	 *
	 * @since    1.0.0
	 */
	public static function enqueue_scripts() {
		wp_enqueue_script( self::MLA_TEXT_DOMAIN . 'pretty-plugin-script', plugins_url( '../js/jquery.prettyPhoto.js', __FILE__ ), array( 'jquery' ), true);
		wp_enqueue_script( self::MLA_TEXT_DOMAIN . '-plugin-script', plugins_url( '../js/widget-front-end.js', __FILE__ ), array( 'jquery' ), true);
	}
	
	/**
	 * Register and enqueue public-facing CSS.
	 *
	 * @since    1.0.0
	 */
	public static function enqueue_styles() {
		wp_enqueue_style( self::MLA_TEXT_DOMAIN . '-plugin-styles', plugins_url( '../css/prettyPhoto.css', __FILE__ ));
		wp_enqueue_style( self::MLA_TEXT_DOMAIN . '-widget-styles', plugins_url( '../css/widget.css', __FILE__ ));
	}

	/**
	 * Display the widget content - called from the WordPress "front end"
	 *
	 * @since 1.60
	 *
	 * @param	array	Widget arguments
	 * @param	array	Widget definition, from the database
	 *
	 * @return	void	Echoes widget output
	 */
	function widget( $args, $instance ) {

		$gallery_type = !empty( $instance['gallery-type'] ) ? esc_attr( $instance['gallery-type'] ) : '';
		
		echo $args['before_widget'];
		
		switch ( $gallery_type ) {

			case 'gallery_default':

				include('views/templates/widget-gallery-default.php');

			break;
			
			case 'gallery_tabbed':
			
				include('views/templates/widget-gallery-tabbed.php');

			break;
			
		} //end gallery_type view switch
		
		echo $args['after_widget'];
		
	}
	
	
	/**
	 * Echo the "edit widget" form on the Appearance/Widgets admin screen
	 *
	 * @since 1.60
	 *
	 * @param	array	Previous definition values, from the database
	 *
	 * @return	void	Echoes "edit widget" form
	 */
	function form( $instance ) {
		
		$gallery_type = !empty($instance['gallery-type']) ? esc_attr($instance['gallery-type']) : '';?>
		
		<!-- Selects a Gallery Type -->
		<p>
			<select class="widefat" name="<?php echo $this->get_field_name('gallery-type');?>" id="<?php echo $this->get_field_id('gallery-type');?>">
			  <option value="">-- Select A Gallery Type --</option>
			  <?php
			  	$options = array(
			  				'Gallery Default' => 'gallery_default',
			  				'Gallery Tabbed' => 'gallery_tabbed'
			  				);
			  	foreach ( $options as $key => $value ) { ?>
			  		<option value="<?php echo $value;?>" id="<?php echo $value;?>" <?php selected( $gallery_type, $value );?>><?php echo $key; ?></option>
				<?php } ?>

			</select>
		</p>
	<?php
	}

	/**
	 * Sanitize widget definition as it is saved to the database
	 *
	 * @since 1.60
	 *
	 * @param	array	Current definition values, to be saved in the database
	 * @param	array	Previous definition values, from the database
	 *
	 * @return	array	Updated definition values to be saved in the database
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['gallery-type'] = strip_tags($new_instance['gallery-type']);
		return $instance;
	}

	/**
	 * Register the widget with WordPress
	 * 
	 * Defined as public because it's an action.
	 *
	 * @since 1.60
	 *
	 * @return	void
	 */
	public static function mla_gallery_widget_widgets_init_action(){
		register_widget('MLAGallery');
	}

	/**
	 * Load a plugin text domain
	 * 
	 * Defined as public because it's an action.
	 *
	 * @since 1.60
	 *
	 * @return	void
	 */
	public static function mla_gallery_widget_plugins_loaded_action(){
		load_plugin_textdomain( self::MLA_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	/**
	 * Creates a album click counter by storing a click count in the DB each time the user clicks an album
	 * 
	 * Dependant on: Taxonomy Meta Plugin
	 *
	 **/
	
	public static function get_cat_views() {
		if ( empty( $_POST['term_ids'] ) ) {
     	 die( json_encode( array( 'status' => 'error', 'error' => 'Term Ids Not Found') ) );
	    }
	    
	    $termIds = explode( ',', $_POST['term_ids'] );
	    $views = array();
		foreach( $termIds as $key => $value ) {
			$terms = get_term_by('id', $value, 'attachment_category');
			//error_log( print_r($terms, true ));
			$count_key = 'term_views_count';
			$count = get_term_meta( $value, $count_key, true );
			if ( $count == '' ) {
				delete_term_meta( $value, $count_key );
				add_term_meta( $value, $count_key, 0 );
			}
			$views[$terms->term_id] = $count;
		}

		die( json_encode( $views ) );
		
	}
	
	public static function set_cat_views() {
		global $wpdb;
		
		if ( empty( $_POST['term_id'] ) ) {
     	 die( json_encode( array( 'status' => 'error', 'error' => 'Term Id Not Found') ) );
	    }
	    
	    $tableName = $wpdb->get_blog_prefix( 1 ) . self::$tableName;
	    
		$count_key = 'term_views_count';
		$count = get_term_meta( $_POST['term_id'], $count_key, true );

		if ( $count == '' ) {
			$count = 0;
			delete_term_meta( $_POST['term_id'], $count_key );
			add_term_meta( $_POST['term_id'], $count_key, 0 );
		} else {
			$count++;
			update_term_meta( $_POST['term_id'], $count_key, $count );
			//todo:: figure out proper table call below...
			$wpdb->update($tableName, array('term_group' => $count), array('term_id'=> $_POST['term_id']));
			self::get_cat_views(); //update the front end
		}
	}
	
	/**
 	 *
	 * Checks to see if we are on a archive page within the media tax
	 * and loads the content if we are.
	 *
	 **/
 
	public static function change_archive_content() {
		
		global $wp_query, $post;
		
    	$queried_object = get_queried_object();
    	
    	$postMimeType = self::$mime_type; //gets the mime type of each item
		
		if( property_exists($queried_object, 'taxonomy') ) {
		
    		if ( is_archive() && $queried_object->taxonomy == 'attachment_category' ) {
		
				include('views/templates/headway-archive-content.php');
			
			}
		}	
		
	} //end change_archive_content function
	
	/**
	 *
	 * Gets Front End Templates
	 *
	 **/
 
	public static function get_template( $template ) {
	  
    	global $wp_query, $post;
    	
    	$queried_object = get_queried_object();
    	
    	$postMimeType = self::$mime_type; //gets the mime type of each item
    
	    if( property_exists($queried_object, 'taxonomy') ) {
		
    		if ( is_archive() && $queried_object->taxonomy == 'attachment_category' ) {

		      	$template = plugin_dir_path(__FILE__) . 'views/templates' . strrchr( $template, '/' );
	      	
	      	}
		}
		  
	    return $template;
		  
	} //end get_MY_CUSTOM_POST_TYPE_template function 
	
	/**
	 *
	 * Remove default archive title so we can customize it's placement in our content function
	 *
	 **/
	 
	 public static function remove_archive_title( $title ) {
	 	
	 	$title = '';
	 	return $title;
	 	
	 }

	/**
	 * MLA Gallery Item Values
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_gallery_item_values_filter( $item_values ) {
		// Set mimetype for later use (hopefully)
		self::$mime_type = $item_values['mime_type'];
		self::$thumb_content = $item_values['thumbnail_content'];
		
		return $item_values;
	}
	 
	/**
	 * MLA Gallery Item Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_gallery_item_template_filter( $item_template ) {
		$postMimeType = self::$mime_type;
		$thumb = self::$thumb_content;
		
		//might need to revisit this once transcoding is in to make sure this is working properly.
		
		switch( $postMimeType ) {
		
			case 'video/mp4':
			//video mime_types
			$item_template = '<[+itemtag+] class=\'gallery-item [+last_in_row+] clearing-thumbs\' data-clearing>
								<[+icontag+] class=\'gallery-icon [+orientation+]\'>
									<a rel="prettyPhoto" href="#inline-[+attachment_ID+]">'.$thumb.'</a>
				                <div id="inline-[+attachment_ID+]" class="hide">
									<div style="width: 480px; max-width: 100%;" class="wp-video">
										<video class="wp-video-shortcode" id="video-[+attachment_ID+]" width="480" height="360" preload="metadata" controls="controls">
											<source type="[+mime_type+]" src="[+file_url+]" />
											<object width="320" height="240" type="application/x-shockwave-flash" data="flashmediaelement.swf">
										        <param name="movie" value="flashmediaelement.swf" />
										        <param name="flashvars" value="controls=true&file=[+file_url+]" />
										        <!-- Image as a last resort -->
										    </object>
											<a href="[+file_url+]">[+file_url+]</a>
										</video>
									</div>
				                </div>
							</[+icontag+]>
								<[+captiontag+] class=\'wp-caption-text gallery-caption\'>
										[+caption+]
								</[+captiontag+]>
							</[+itemtag+]>';
		
			break;
			case 'audio/mp3':
			//audio mime_types
			$item_template = '<[+itemtag+] class=\'gallery-item [+last_in_row+] clearing-thumbs\' data-clearing>
								<[+icontag+] class=\'gallery-icon [+orientation+]\'>
									<a rel="prettyPhoto" href="#inline-[+attachment_ID+]">[+title+]</a>
				                <div id="inline-[+attachment_ID+]" class="hide">
									<div style="width: 480px; max-width: 100%;" class="wp-audio">
										<audio class="wp-audio-shortcode" id="audio-[+attachment_ID+]" width="480" height="360" preload="none" controls="controls">
											<source type="[+mime_type+]" src="[+file_url+]" />
											<object width="320" height="240" type="application/x-shockwave-flash" data="flashmediaelement.swf">
											        <param name="movie" value="flashmediaelement.swf" />
											        <param name="flashvars" value="controls=true&file=[+file_url+]" />
											        <!-- Image as a last resort -->
										    </object>
											<a href="[+file_url+]">[+file_url+]</a>
										</audio>
									</div>
				                </div>
								</[+icontag+]>
								<[+captiontag+] class=\'wp-caption-text gallery-caption\'>
										[+caption+]
								</[+captiontag+]>
							</[+itemtag+]>';
							
			break;
			default:
			$item_template = '<[+itemtag+] class=\'gallery-item [+last_in_row+]\'>
								<[+icontag+] class=\'gallery-icon [+orientation+]\'>
										[+link+]
									</[+icontag+]>
									<[+captiontag+] class=\'wp-caption-text gallery-caption\'>
											[+caption+]
									</[+captiontag+]>
							 </[+itemtag+]>';
			break;
		}
		
		return $item_template;
		
	} // mla_gallery_item_template_filter
	
} // Class MLAGallery

/*
 * Actions are added here, when the source file is loaded, because the MLAGalleryWidget
 * object(s) are created too late to be useful.
 */

add_action('widgets_init','MLAGallery::mla_gallery_widget_widgets_init_action');
add_action('plugins_loaded','MLAGallery::mla_gallery_widget_plugins_loaded_action');
?>