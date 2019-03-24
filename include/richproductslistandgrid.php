<?php  
/**
 * Register shortcode and render product data as per shortcode configuration. 
 */ 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! class_exists( 'richproductslistandgridWidget' ) ) { 
	class richproductslistandgridWidget extends richproductslistandgridLib {
	 
	   /**
		* constructor method.
		*
		* Run the following methods when this class is loaded
		*
		* @access  public
		* @since   1.0
		*
		* @return  void
		*/ 
		public function __construct() {
		
			add_action( 'init', array( &$this, 'init' ) ); 
			parent::__construct();
			
		}  
		
	   /**
		* Load required methods on wordpress init action 
		*
		* @access  public
		* @since   1.0
		*
		* @return  void
		*/ 
		public function init() {
		
			add_action( 'wp_ajax_wplg_getTotalProducts',array( &$this, 'wplg_getTotalProducts' ) );
			add_action( 'wp_ajax_wplg_getProducts',array( &$this, 'wplg_getProducts' ) ); 
			add_action( 'wp_ajax_wplg_getMoreProducts',array( &$this, 'wplg_getMoreProducts' ) );
			
			add_action( 'wp_ajax_nopriv_wplg_getTotalProducts', array( &$this, 'wplg_getTotalProducts' ) );
			add_action( 'wp_ajax_nopriv_wplg_getProducts', array( &$this, 'wplg_getProducts' ) ); 
			add_action( 'wp_ajax_nopriv_wplg_getMoreProducts', array( &$this, 'wplg_getMoreProducts' ) ); 
			
			add_shortcode( 'richproductslistandgrid', array( &$this, 'richproductslistandgrid' ) ); 
			
		} 
		
	   /**
		* Get the total numbers of products
		*
		* @access  public
		* @since   1.0
		* 
		* @param   int    $category_id  		Category ID 
		* @param   string $product_search_text  Product name or any search keyword to filter products
		* @param   int    $c_flg  				Whether to fetch whether products by category id or prevent for searching
		* @param   int    $is_default_category_with_hidden  To check settings of default category If it's value is '1'. Default value is '0'
		* @return  int	  Total number of products  	
		*/  
		public function wplg_getTotalProducts( $category_id, $product_search_text, $c_flg, $is_default_category_with_hidden ) { 
		
			global $wpdb;   
			
		   /**
			* Check security token from ajax request
			*/
			check_ajax_referer(  $this->_config["wplg_security_key"]["security_key"], 'security' );

		   /**
			* Fetch products as per search filter
			*/	
			$_res_total = $this->getSqlResult( $category_id, $product_search_text, 0, 0, $c_flg, $is_default_category_with_hidden, 1 );
			
			return $_res_total[0]->total_val;
			 
		}	

		 
	   /**
		* Render category and products view shortcode
		*
		* @access  public
		* @since   1.0
		*
		* @param   array   $params  Shortcode configuration options from admin settings
		* @return  string  Render category and products HTML
		*/
		public function richproductslistandgrid( $params = array() ) { 	
			
			if(isset($params["id"]) && trim($params["id"]) != "" && intval($params["id"]) > 0) {
				$richproductslistandgrid_id = $params["id"]; 
				$wplg_shortcode = get_post_meta( $richproductslistandgrid_id ); 
				
				foreach ( $wplg_shortcode as $sc_key => $sc_val ) {			
					$wplg_shortcode[$sc_key] = $sc_val[0];			
				} 
				
				if(!isset($wplg_shortcode["number_of_product_display"]))	
					$wplg_shortcode["number_of_product_display"] = 0;
				if(!isset($wplg_shortcode["category_id"]))	
					$wplg_shortcode["category_id"] = 0;
						
				$this->_config = shortcode_atts( $this->_config, $wplg_shortcode );  
				$this->_config["vcode"] =  "uid_".md5(md5(json_encode($this->_config)).$this->getUCode());	
				
			} else {

				$this->init_settings();
				
				// default option settings
				foreach($this->_config as $default_options => $default_option_value ){
				  if(!isset($params[$default_options]))
					$params[$default_options] = $default_option_value["default"];
				}

				if(count($params)>0) {
					$this->_config = shortcode_atts( $this->_config, $params ); 
				}
				if(!isset($this->_config["category_id"]))	
					$this->_config["category_id"] = 0;
					
				$this->_config["vcode"] =  "uid_".md5(md5(json_encode($this->_config)).$this->getUCode());
			}
			
			$this->_config["all_selected_categories"] = array( "type" => "none", "in_js" => "yes");	  
			$this->_config["all_selected_categories"]["default"] = "";			
			$_all_selected_categories =  "";
			if( isset($this->_config["category_id"]) && trim($this->_config["category_id"]) != "" ) {
				$_all_selected_categories = $this->_config["category_id"];
			} else {
				$_category_res = $this->getCategories(); 
				$_opt_all_id = array();
				foreach( $_category_res as $_category ) {  
					$_opt_all_id[] = $_category->id; 
				}
				$_all_selected_categories = implode( ",", $_opt_all_id );
			}
			$this->_config["all_selected_categories"]	= $_all_selected_categories;
			
		   /**
			* Load template according to admin settings
			*/
			ob_start();
			
			require( $this->getrichproductslistandgridTemplate( "fronted/front_template.php" ) );
			
			return ob_get_clean();
		
		}   
		
	   /**
		* Load more product via ajax request
		*
		* @access  public
		* @since   1.0
		* 
		* @return  void Displays searched products HTML to load more pagination
		*/	
		public function wplg_getMoreProducts() {
		
			global $wpdb, $wp_query; 
			
		   /**
			* Check security token from ajax request
			*/
			check_ajax_referer($this->_config["wplg_security_key"]["security_key"], 'security' );
			
			$_total = ( isset( $_REQUEST["total"] )?esc_attr( $_REQUEST["total"] ):0 );
			$category_id = ( isset( $_REQUEST["category_id"] )?esc_attr( $_REQUEST["category_id"] ):0 );
			$product_search_text = ( isset( $_REQUEST["product_search_text"] )?esc_attr( $_REQUEST["product_search_text"] ):"" );  
			$_limit_start = ( isset( $_REQUEST["limit_start"])?esc_attr( $_REQUEST["limit_start"] ):0 );
			$_limit_end = ( isset( $_REQUEST["number_of_product_display"])?esc_attr( $_REQUEST["number_of_product_display"] ):wplg_number_of_product_display ); 
			$all_selected_categories = $_REQUEST["all_selected_categories"]; 
		    $category_id_default =( ( isset( $_REQUEST["category_id_default"] ) && trim( $_REQUEST["category_id_default"] ) != ""  ) ? esc_html( $_REQUEST["category_id_default"] ) : esc_html( $all_selected_categories ));	
	
		   /**
			* Fetch products as per search filter
			*/	
			$_result_items = $this->getSqlResult( $category_id_default, $product_search_text, $_limit_start, $_limit_end );
		  
			require( $this->getrichproductslistandgridTemplate( 'fronted/ajax_load_more_products.php' ) );	
			
			wp_die();
		}    
		
	   /**
		* Load more products via ajax request
		*
		* @access  public
		* @since   1.0
		* 
		* @return  object Displays searched products HTML
		*/
		public function wplg_getProducts() {
		
		   global $wpdb; 
			
		   /**
			* Check security token from ajax request
			*/	
		   check_ajax_referer( $this->_config["wplg_security_key"]["security_key"], 'security' );	   
		   
		   require( $this->getrichproductslistandgridTemplate( 'fronted/ajax_load_products.php' ) );	
		   
  		   wp_die();
		
		}
		 
	   /**
		* Get product list with specified limit and filtered by category and search text
		*
		* @access  public
		* @since   1.0 
		*
		* @param   int     $category_id 		 Selected category ID 
		* @param   string  $product_search_text  Product name or any search keyword to filter products
		* @param   int     $_limit_end			 Limit to fetch product ending to given position
		* @return  object  Set of searched product data
		*/
		public function getProductList( $category_id, $product_search_text, $_limit_end ) {
			
		   /**
			* Check security token from ajax request
			*/	
			check_ajax_referer( $this->_config["wplg_security_key"]["security_key"], 'security' );		
			
		   /**
			* Fetch data from database
			*/
			return $this->getSqlResult( $category_id, $product_search_text, 0, $_limit_end );
			 
		} 
		
	}
	
}
new richproductslistandgridWidget();