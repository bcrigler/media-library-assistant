<?php
/**
 * Media Library Assistant Edit Media screen enhancements
 *
 * @package Media Library Assistant
 * @since 0.80
 */

/**
 * Class MLA (Media Library Assistant) Edit contains meta boxes for the Edit Media (advanced-form-edit.php) screen
 *
 * @package Media Library Assistant
 * @since 0.80
 */
class MLAEdit {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 0.80
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * WordPress 3.5 uses the advanced-form-edit.php function for the Edit Media
		 * page. This supports all the standard meta-boxes for post types.
		 */
		if ( MLATest::$wordpress_3point5_plus ) {
			add_post_type_support( 'attachment', 'custom-fields' );
			add_action( 'add_meta_boxes', 'MLAEdit::mla_add_meta_boxes_action', 10, 2 );

			// do_action in wp-admin/includes/meta-boxes.php function attachment_submit_meta_box
			add_action( 'attachment_submitbox_misc_actions', 'MLAEdit::mla_last_modified_action' );
			
			// do_action in wp-includes/post.php function wp_insert_post
			add_action( 'edit_attachment', 'MLAEdit::mla_edit_attachment_action', 10, 1 );
		} // $wordpress_3point5_plus
	}

	/**
	 * Adds Last Modified date to the Submit box on the Edit Media screen.
	 * Declared public because it is an action.
	 *
	 * @since 0.80
	 *
	 * @return	void	echoes the HTML markup for the label and value
	 */
	public static function mla_last_modified_action( ) {
		global $post;

		$datef = __( 'M j, Y @ G:i' );
		$stamp = __('Last modified: <b>%1$s</b>');
		$date = date_i18n( $datef, strtotime( $post->post_modified ) );
		echo '<div class="misc-pub-section curtime">' . "\r\n";
		echo '<span id="timestamp">' . sprintf($stamp, $date) . "</span>\r\n";
		echo "</div><!-- .misc-pub-section -->\r\n";
	} // mla_last_modified_action
	
	/**
	 * Registers meta boxes for the Edit Media screen.
	 * Declared public because it is an action.
	 *
	 * @since 0.80
	 *
	 * @param	string	type of the current post, e.g., 'attachment'
	 * @param	object	current post
	 *
	 * @return	void
	 */
	public static function mla_add_meta_boxes_action( $post_type, $post ) {
		if ( 'attachment' == $post_type ) {
			add_meta_box( 'mla-parent-info', 'Parent Info', 'MLAEdit::mla_parent_info_handler', 'attachment', 'normal', 'core' );
			add_meta_box( 'mla-menu-order', 'Menu Order', 'MLAEdit::mla_menu_order_handler', 'attachment', 'normal', 'core' );
			
			$image_metadata = get_metadata( 'post', $post->ID, '_wp_attachment_metadata', true );
			if ( !empty( $image_metadata ) )
				add_meta_box( 'mla-image-metadata', 'Image Metadata', 'MLAEdit::mla_image_metadata_handler', 'attachment', 'normal', 'core' );

			add_meta_box( 'mla-featured-in', 'Featured in', 'MLAEdit::mla_featured_in_handler', 'attachment', 'normal', 'core' );
			add_meta_box( 'mla-inserted-in', 'Inserted in', 'MLAEdit::mla_inserted_in_handler', 'attachment', 'normal', 'core' );
			add_meta_box( 'mla-gallery-in', 'Gallery in', 'MLAEdit::mla_gallery_in_handler', 'attachment', 'normal', 'core' );
			add_meta_box( 'mla-mla-gallery-in', 'MLA Gallery in', 'MLAEdit::mla_mla_gallery_in_handler', 'attachment', 'normal', 'core' );
		} // 'attachment'
	} // mla_add_meta_boxes_action
	
	/**
	 * Where-used values for the current item
	 *
	 * This array contains the Featured/Inserted/Gallery/MLA Gallery references for the item.
	 * The array is built once each page load and cached for subsequent calls.
	 *
	 * @since 0.80
	 *
	 * @var	array
	 */
	private static $mla_references = null;

	/**
	 * Renders the Parent Info meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_parent_info_handler( $post ) {
		if ( is_null( self::$mla_references ) )
			self::$mla_references = MLAData::mla_fetch_attachment_references( $post->ID, $post->post_parent );
			
		if ( is_array( self::$mla_references ) ) {
			if ( self::$mla_references['found_parent'] ) {
				$parent_info = sprintf( '(%1$s) %2$s', self::$mla_references['parent_type'], self::$mla_references['parent_title'] );
			} else {
				$parent_info = '';
				if ( !self::$mla_references['found_reference'] )
					$parent_info .= '(ORPHAN) ';
				
				if ( self::$mla_references['is_unattached'] )
					$parent_info .= '(UNATTACHED) ';
				else {
					if ( !self::$mla_references['found_parent'] ) {
						if ( isset( self::$mla_references['parent_title'] ) )
							$parent_info .= '(BAD PARENT) ';
						else
							$parent_info .= '(INVALID PARENT) ';
					}
				}
			} // no parent
		} // is_array

		echo '<label class="screen-reader-text" for="mla_post_parent">Post Parent</label><input name="mla_post_parent" type="text" size="4" id="mla_post_parent" value="' . $post->post_parent . "\" />\r\n";
		echo '<label class="screen-reader-text" for="mla_parent_info">Parent Info</label><input name="mla_parent_info" type="text" readonly="readonly" size="60" id="mla_parent_info" value="' . esc_attr( $parent_info ) . "\" />\r\n";
	}
	
	/**
	 * Renders the Menu Order meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_menu_order_handler( $post ) {

		echo '<label class="screen-reader-text" for="mla_menu_order">Menu Order</label><input name="mla_menu_order" type="text" size="4" id="mla_menu_order" value="' . $post->menu_order . "\" />\r\n";
	}
	
	/**
	 * Renders the Image Metadata meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_image_metadata_handler( $post ) {
		$metadata = MLAData::mla_fetch_attachment_metadata( $post->ID );

		if ( isset( $metadata['mla_wp_attachment_metadata'] ) )
			$value = var_export( $metadata['mla_wp_attachment_metadata'], true );
		else
			$value = '';

		echo '<label class="screen-reader-text" for="mla_image_metadata">Image Metadata</label><textarea id="mla_image_metadata" rows="5" cols="80" readonly="readonly" name="mla_image_metadata" >' . esc_textarea( $value ) . "</textarea>\r\n";
	}
	
	/**
	 * Renders the Featured in meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_featured_in_handler( $post ) {
		if ( is_null( self::$mla_references ) )
			self::$mla_references = MLAData::mla_fetch_attachment_references( $post->ID, $post->post_parent );
			
		if ( is_array( self::$mla_references ) ) {
			$features = '';
			
			foreach ( self::$mla_references['features'] as $feature_id => $feature ) {
				if ( $feature_id == $post->post_parent )
					$parent = 'PARENT ';
				else
					$parent = '';
				
				$features .= sprintf( '%1$s (%2$s %3$s), %4$s', /*$1%s*/ $parent, /*$2%s*/ $feature->post_type, /*$3%s*/ $feature_id, /*$4%s*/ $feature->post_title ) . "\r\n";
			} // foreach $feature
		}

		echo '<label class="screen-reader-text" for="mla_featured_in">Featured in</label><textarea id="mla_featured_in" rows="5" cols="80" readonly="readonly" name="mla_featured_in" >' . esc_textarea( $features ) . "</textarea>\r\n";
	}
	
	/**
	 * Renders the Inserted in meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_inserted_in_handler( $post ) {
		if ( is_null( self::$mla_references ) )
			self::$mla_references = MLAData::mla_fetch_attachment_references( $post->ID, $post->post_parent );
			
		if ( is_array( self::$mla_references ) ) {
			$inserts = '';
			
			foreach ( self::$mla_references['inserts'] as $file => $insert_array ) {
				$inserts .= $file . "\r\n";
					
				foreach ( $insert_array as $insert ) {
					if ( $insert->ID == $post->post_parent )
						$parent = '  PARENT ';
					else
						$parent = '  ';
		
					$inserts .= sprintf( '%1$s (%2$s %3$s), %4$s', /*$1%s*/ $parent, /*$2%s*/ $insert->post_type, /*$3%s*/ $insert->ID, /*$4%s*/ $insert->post_title ) . "\r\n";
				} // foreach $insert
			} // foreach $file
		} // is_array

		echo '<label class="screen-reader-text" for="mla_inserted_in">Inserted in</label><textarea id="mla_inserted_in" rows="5" cols="80" readonly="readonly" name="mla_inserted_in" >' . esc_textarea( $inserts ) . "</textarea>\r\n";
	}
	
	/**
	 * Renders the Gallery in meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_gallery_in_handler( $post ) {
		if ( is_null( self::$mla_references ) )
			self::$mla_references = MLAData::mla_fetch_attachment_references( $post->ID, $post->post_parent );
			
		$galleries = '';
			
		if ( is_array( self::$mla_references ) ) {
			foreach ( self::$mla_references['galleries'] as $gallery_id => $gallery ) {
				if ( $gallery_id == $post->post_parent )
					$parent = 'PARENT ';
				else
					$parent = '';
				
				$galleries .= sprintf( '%1$s (%2$s %3$s), %4$s', /*$1%s*/ $parent, /*$2%s*/ $gallery['post_type'], /*$3%s*/ $gallery_id, /*$4%s*/ $gallery['post_title'] ) . "\r\n";
			} // foreach $feature
		}

		echo '<label class="screen-reader-text" for="mla_gallery_in">Gallery in</label><textarea id="mla_gallery_in" rows="5" cols="80" readonly="readonly" name="mla_gallery_in" >' . esc_textarea( $galleries ) . "</textarea>\r\n";
	}
	
	/**
	 * Renders the Gallery in meta box on the Edit Media page.
	 * Declared public because it is a callback function.
	 *
	 * @since 0.80
	 *
	 * @param	object	current post
	 *
	 * @return	void	echoes the HTML markup for the meta box content
	 */
	public static function mla_mla_gallery_in_handler( $post ) {
		if ( is_null( self::$mla_references ) )
			self::$mla_references = MLAData::mla_fetch_attachment_references( $post->ID, $post->post_parent );
			
		$galleries = '';
			
		if ( is_array( self::$mla_references ) ) {
			foreach ( self::$mla_references['mla_galleries'] as $gallery_id => $gallery ) {
				if ( $gallery_id == $post->post_parent )
					$parent = 'PARENT ';
				else
					$parent = '';
				
				$galleries .= sprintf( '%1$s (%2$s %3$s), %4$s', /*$1%s*/ $parent, /*$2%s*/ $gallery['post_type'], /*$3%s*/ $gallery_id, /*$4%s*/ $gallery['post_title'] ) . "\r\n";
			} // foreach $feature
		}

		echo '<label class="screen-reader-text" for="mla_mla_gallery_in">MLA Gallery in</label><textarea id="mla_mla_gallery_in" rows="5" cols="80" readonly="readonly" name="mla_mla_gallery_in" >' . esc_textarea( $galleries ) . "</textarea>\r\n";
	}
	
	/**
	 * Saves updates from the Edit Media screen.
	 * Declared public because it is an action.
	 *
	 * @since 0.80
	 *
	 * @param	integer	ID of the current post
	 *
	 * @return	void
	 */
	public static function mla_edit_attachment_action( $post_ID ) {
		$new_data = array();
		if ( isset( $_REQUEST['mla_post_parent'] ) )
			$new_data['post_parent'] = $_REQUEST['mla_post_parent'];
			
		if ( isset( $_REQUEST['mla_menu_order'] ) )
			$new_data['menu_order'] = $_REQUEST['mla_menu_order'];
			
		if ( !empty( $new_data ) ) {
			MLAData::mla_update_single_item( $post_ID, $new_data );
		}
	} // mla_edit_attachment_action
} //Class MLAEdit
?>