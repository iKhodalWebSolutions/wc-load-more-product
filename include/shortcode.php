<?php 
/** 
 * Register custom product type to manage shortcode
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! class_exists( 'richproductslistandgridShortcode_Admin' ) ) {
	class richproductslistandgridShortcode_Admin extends richproductslistandgridLib {
	
		public $_shortcode_config = array();
		 
		/**
		 * constructor method.
		 *
		 * Register product type for category and products view shortcode
		 * 
		 * @access    public
		 * @since     1.0
		 *
		 * @return    void
		 */
		public function __construct() {
			
			parent::__construct();
			
	       /**
		    * Register hooks to manage custom product type for category and products view
		    */
			add_action( 'init', array( &$this, 'wplg_registerProductType' ) );   
			add_action( 'add_meta_boxes', array( &$this, 'add_richproductslistandgrid_metaboxes' ) );
			add_action( 'save_post', array(&$this, 'wp_save_richproductslistandgrid_meta' ), 1, 2 ); 
			add_action( 'admin_enqueue_scripts', array( $this, 'wplg_admin_enqueue' ) ); 
			
		   /* Register hooks for displaying shortcode column. */ 
			if( isset( $_REQUEST["post_type"] ) && !empty( $_REQUEST["post_type"] ) && trim($_REQUEST["post_type"]) == "wplg_view" ) {
				add_action( "manage_posts_custom_column", array( $this, 'richproductslistandgridShortcodeColumns' ), 10, 2 );
				add_filter( 'manage_posts_columns', array( $this, 'wplg_shortcodeNewColumn' ) );
			}
			
			add_action( 'wp_ajax_wplg_getCategoriesOnTypes',array( &$this, 'wplg_getCategoriesOnTypes' ) ); 
			add_action( 'wp_ajax_nopriv_wplg_getCategoriesOnTypes', array( &$this, 'wplg_getCategoriesOnTypes' ) );
			add_action( 'wp_ajax_wplg_getCategoriesRadioOnTypes',array( &$this, 'wplg_getCategoriesRadioOnTypes' ) ); 
			add_action( 'wp_ajax_nopriv_wplg_getCategoriesRadioOnTypes', array( &$this, 'wplg_getCategoriesRadioOnTypes' ) ); 
			 
		}    
		
 	   /**
		* Register and load JS/CSS for admin widget configuration 
		*
		* @access  private
		* @since   1.0
		*
		* @return  bool|void It returns false if not valid page or display HTML for JS/CSS
		*/  
		public function wplg_admin_enqueue() {

			if ( ! $this->validate_page() )
				return FALSE;
			
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'admin-richproductslistandgrid.css', wplg_media."css/admin-richproductslistandgrid.css" );
			wp_enqueue_script( 'admin-richproductslistandgrid.js', wplg_media."js/admin-richproductslistandgrid.js" ); 
			
		}		
		 
	   /**
		* Add meta boxes to display shortcode
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/ 
		public function add_richproductslistandgrid_metaboxes() {
			
			/**
			 * Add custom fields for shortcode settings
		     */
			add_meta_box( 'wp_richproductslistandgrid_fields', __( 'Product List and Grid View', 'richproductslistandgrid' ),
				array( &$this, 'wp_richproductslistandgrid_fields' ), 'wplg_view', 'normal', 'high' );
			
			/**
			 * Display shortcode of category and products tab
		     */
			add_meta_box( 'wp_richproductslistandgrid_shortcode', __( 'Shortcode', 'richproductslistandgrid' ),
				array( &$this, 'shortcode_meta_box' ), 'wplg_view', 'side' );	
		
		}  
		
	   /**
		* Validate widget or shortcode product type page
		*
		* @access  private
		* @since   1.0
		*
		* @return  bool It returns true if page is post.php or widget otherwise returns false
		*/ 
		private function validate_page() {

			if ( ( isset( $_GET['post_type'] )  && $_GET['post_type'] == 'wplg_view' ) || strpos($_SERVER["REQUEST_URI"],"widgets.php") > 0  || strpos($_SERVER["REQUEST_URI"],"post.php" ) > 0 || strpos($_SERVER["REQUEST_URI"], "richproductslistandgrid_settings" ) > 0  )
				return TRUE;
		
		} 			
 
	   /**
		* Display richproductslistandgrid block configuration fields
		*
		* @access  private
		* @since   1.0
		*
		* @return  void Returns HTML for configuration fields 
		*/  
		public function wp_richproductslistandgrid_fields() {
			
			global $post; 
			 
			foreach( $this->_config as $kw => $kw_val ) {
				$this->_shortcode_config[$kw] = get_post_meta( $post->ID, $kw, true ); 
			}
			 
			foreach ( $this->_shortcode_config as $sc_key => $sc_val ) {
				if( trim( $sc_val ) == "" )
					unset( $this->_shortcode_config[ $sc_key ] );
				else {
					if(!is_array($sc_val) && trim($sc_val) != "" ) 
						$this->_shortcode_config[ $sc_key ] = htmlspecialchars( $sc_val, ENT_QUOTES );
					else 
						$this->_shortcode_config[ $sc_key ] = $sc_val;
				}	
			}
			
			foreach( $this->_config as $kw => $kw_val ) {
				if( isset($this->_shortcode_config[$kw]) && !is_array($this->_shortcode_config[$kw]) && trim($this->_shortcode_config[$kw]) == "" ) {
					$this->_shortcode_config[$kw] = $this->_config[$kw]["default"];
				} 
			}
			
			$this->_shortcode_config["vcode"] = get_post_meta( $post->ID, 'vcode', true );    
			 
			require( $this->getrichproductslistandgridTemplate( "admin/admin_shortcode_product_type.php" ) );
			
		}
		
	   /**
		* Display shortcode in edit mode
		*
		* @access  private
		* @since   1.0
		*
		* @param   object  $post Set of configuration data.
		* @return  void	   Displays HTML of shortcode
		*/
		public function shortcode_meta_box( $post ) {

			$richproductslistandgrid_id = $post->ID;

			if ( get_post_status( $richproductslistandgrid_id ) !== 'publish' ) {

				echo '<p>'.__( 'Please make the publish status to get the shortcode', 'richproductslistandgrid' ).'</p>';

				return;

			}

			$richproductslistandgrid_title = get_the_title( $richproductslistandgrid_id );

			$shortcode = sprintf( "[%s id='%s']", 'richproductslistandgrid', $richproductslistandgrid_id );
			
			echo "<p class='tpp-code'>".$shortcode."</p>";
		}
				  
	   /**
		* Save category and products view shortcode fields
		*
		* @access  private
		* @since   1.0 
		*
		* @param   int    	$post_id product id
		* @param   object   $post    product data object
		* @return  void
		*/ 
		function wp_save_richproductslistandgrid_meta( $post_id, $post ) {
			
		/*	if( !isset($_POST['richproductslistandgrid_nonce']) ) {
				return $post->ID;
			} 
			if( !wp_verify_nonce( $_POST['richproductslistandgrid_nonce'], plugin_basename(__FILE__) ) ) {
				return $post->ID;
			}
			*/
			
		   /**
			* Check current user permission to edit post
			*/
			if(!current_user_can( 'edit_post', $post->ID ))
				return $post->ID;
				
			 /**
			* sanitize text fields 
			*/
			$wplg_meta = array(); 
			
			foreach( $this->_config as $kw => $kw_val ) { 
				$_save_value =  $_POST["nm_".$kw];
				if($kw_val["type"]=="boolean"){
					$_save_value = $_POST["nm_".$kw][0];
				}
				if( $kw_val["type"]=="checkbox" && count($_POST["nm_".$kw]) > 0 ) {
					$_save_value = implode( ",", $_POST["nm_".$kw] );
				}
				$wplg_meta[$kw] =  sanitize_text_field( $_save_value );
			}     
			 
			foreach ( $wplg_meta as $key => $value ) {
			
			   if( $post->post_type == 'revision' ) return;
				$value = implode( ',', (array)$value );
				
				if( trim($value) == "Array" || is_array($value) )
					$value = "";
					
			   /**
				* Add or update posted data 
				*/
				if( get_post_meta( $post->ID, $key, FALSE ) ) { 
					update_post_meta( $post->ID, $key, $value );
				} else { 
					add_post_meta( $post->ID, $key, $value );
				} 
			
			}		
			
		  
		}
		
			 
	   /**
		* Register product type category and products shortcode view
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/  
		function wplg_registerProductType() { 
			
		   /**
			* Product type and menu labels 
			*/
			$labels = array(
				'name' => __('Rich Woocommerce Product List & Grid View Shortcode', 'richproductslistandgrid' ),
				'singular_name' => __( 'Rich Woocommerce Product List & Grid View Shortcode', 'richproductslistandgrid' ),
				'add_new' => __( 'Add New Shortcode', 'richproductslistandgrid' ),
				'add_new_item' => __( 'Add New Shortcode', 'richproductslistandgrid' ),
				'edit_item' => __( 'Edit', 'richproductslistandgrid'  ),
				'new_item' => __( 'New', 'richproductslistandgrid'  ),
				'all_items' => __( 'All', 'richproductslistandgrid'  ),
				'view_item' => __( 'View', 'richproductslistandgrid'  ),
				'search_items' => __( 'Search', 'richproductslistandgrid'  ),
				'not_found' =>  __( 'No item found', 'richproductslistandgrid'  ),
				'not_found_in_trash' => __( 'No item found in Trash', 'richproductslistandgrid'  ),
				'parent_item_colon' => '',
				'menu_name' => __( 'WCLM', 'richproductslistandgrid'  ) 
			);
			
		   /**
			* Custom products posttype registration options
			*/
			$args = array(
				'labels' => $labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => false,
				'rewrite' => false,
				'capability_type' => 'post',
				'menu_icon' => 'dashicons-list-view',
				'has_archive' => false,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title' )
			);
			 
		   /**
			* Register new product type
			*/
			register_post_type( 'wplg_view', $args ); 

		}
		
	   /**
		* Display shortcode column in category and products list
		*
		* @access  private
		* @since   1.0
		*
		* @param   string  $column  Column name
		* @param   int     $post_id Product ID
		* @return  void	   Display shortcode in column	
		*/
		public function richproductslistandgridShortcodeColumns( $column, $post_id ) { 
		
			if( $column == "shortcode" ) {
				 echo sprintf( "[%s id='%s']", 'richproductslistandgrid', $post_id ); 
			}  
		
		}
		
	   /**
		* Register shortcode column
		*
		* @access  private
		* @since   1.0
		*
		* @param   array  $columns  Column list 
		* @return  array  Returns column list
		*/
		public function wplg_shortcodeNewColumn( $columns ) {
			
			$_edit_column_list = array();	
			$_i = 0;
			
			foreach( $columns as $__key => $__value) {
					
					if($_i==2){
						$_edit_column_list['shortcode'] = __( 'Shortcode', 'richproductslistandgrid' );
					}
					$_edit_column_list[$__key] = $__value;
					
					$_i++;
			}
			
			return $_edit_column_list;
		
		}
		
	} 

}

new richproductslistandgridShortcode_Admin();
 
?>