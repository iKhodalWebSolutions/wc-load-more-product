<?php if ( ! defined( 'ABSPATH' ) ) exit;   $vcode = $this->_config["vcode"];    ?>
<script type='text/javascript' language='javascript'><?php echo $this->wplg_js_obj( $this->_config ); ?></script> 
<?php 
 
$_categories = $this->_config["category_id"];
$category_type = $this->_config["category_type"];
$wplg_default_category_open = $this->_config["wplg_default_category_open"];
$all_selected_categories = $this->_config["all_selected_categories"];
$_is_rtl_enable = $this->_config["wplg_enable_rtl"];
$wplg_enable_product_count = $this->_config["wplg_enable_product_count"];
$wplg_hide_empty_category = $this->_config["wplg_hide_empty_category"];
$wplg_short_category_name_by = $this->_config["wplg_short_category_name_by"];
$wplg_hide_paging = $this->_config["wplg_hide_paging"]; 
$wplg_hide_product_image = $this->_config["wplg_hide_product_image"]; 
$wplg_hide_product_short_content = $this->_config["wplg_hide_product_short_content"]; 
$wplg_select_paging_type = $this->_config["wplg_select_paging_type"]; 
$wplg_hide_product_short_content_length = $this->_config["wplg_hide_product_short_content_length"]; 
$wplg_read_more_link = $this->_config["wplg_read_more_link"]; 
$wplg_order_category_ids = $this->_config["wplg_order_category_ids"]; 
$wplg_image_content_width = $this->_config["wplg_image_content_width"];	
$wplg_image_height = $this->_config["wplg_image_height"]; 
$wplg_shorting_products_by = $this->_config["wplg_shorting_products_by"]; 
$wplg_product_ordering_type = $this->_config["wplg_product_ordering_type"]; 
$_wplg_image_height_class = ""; 
 
if( $wplg_short_category_name_by != "id" ) 
	$wplg_order_category_ids = "";
	
$wplg_space_margin_between_products = $this->_config["wplg_space_margin_between_products"];
$wplg_products_grid_alignment = $this->_config["wplg_products_grid_alignment"];
$wplg_products_loading_effect_on_pagination = $this->_config["wplg_products_loading_effect_on_pagination"];
$wplg_mouse_hover_effect = $this->_config["wplg_mouse_hover_effect"];
$wplg_show_author_image_and_name = $this->_config["wplg_show_author_image_and_name"]; 
$template = $this->_config["template"];

$_u_agent = $_SERVER['HTTP_USER_AGENT'];
$_m_browser = '';  
if(strpos($_u_agent,'MSIE')>-1)
	$_m_browser = 'cls-ie-browser';
	
?> 
<div id="richproductslistandgrid" style="width:<?php echo esc_attr($this->_config["tp_widget_width"]); ?>"  class="<?php echo ((trim($_is_rtl_enable)=="yes")?"wplg-rtl-enabled":""); ?>   cls-<?php echo $wplg_products_grid_alignment; ?> <?php echo $template; ?> ">
	<?php if($this->_config["hide_widget_title"]=="no"){ ?>
		<div class="ik-pst-tab-title-head" style="background-color:<?php echo esc_attr( $this->_config["header_background_color"] ); ?>;color:<?php echo esc_attr( $this->_config["header_text_color"] ); ?>"  >
			<?php echo esc_html( $this->_config["widget_title"] ); ?>   
		</div>
	<?php } ?> 
	<span class='wp-load-icon'>
		<img width="18px" height="18px" src="<?php echo wplg_media.'images/loader.gif'; ?>" />
	</span>
	<div  id="<?php echo esc_attr($vcode); ?>"  class="wea_content <?php echo $_m_browser; ?>  lt-tab <?php echo esc_attr($wplg_select_paging_type); ?>">
		
		<?php
			$_image_width_item = 0;
			if(   intval($wplg_image_content_width) > 0 ) {
				$_image_width_item = intval($wplg_image_content_width); 
			}	 
		?>
		<input type="hidden" class="imgwidth" value = "<?php echo $_image_width_item; ?>" />
		 
		<div class="clr"></div>
		<div class="item-products <?php echo $wplg_mouse_hover_effect; ?>">
			<input type="hidden" class="ikh_templates" value="<?php echo $wplg_products_grid_alignment; ?>" />
			<input type="hidden" class="ikh_products_loads_from" value="<?php echo $wplg_products_loading_effect_on_pagination; ?>" />
			<input type="hidden" class="ikh_border_difference" value="0" />
			<input type="hidden" class="ikh_margin_bottom" value="<?php echo $wplg_space_margin_between_products; ?>" />
			<input type="hidden" class="ikh_margin_left" value="<?php echo $wplg_space_margin_between_products; ?>" />
			<input type="hidden" class="ikh_image_height" value="<?php echo $wplg_image_height; ?>" />
			<input type="hidden" class="ikh_item_area_width" value="<?php echo $_image_width_item; ?>" /> 
			<div class="item-products-wrap">
			<?php   
					 $product_search_text = ""; 
					 $category_id = $wplg_default_category_open;
					 $_limit_start = 0;
					 $_limit_end = $this->_config["number_of_product_display"];
					 $is_default_category_with_hidden = 0;  
					
					// Category and search text field start ==== 
					 $_category_res = array();
					 $_total_product_count = 0;
					 $_category_res_n = array(); 
					 
					 if( trim($category_type) != "0" ) {
					 
							if( trim($all_selected_categories)=="0" || trim($all_selected_categories) == "" )
								$_category_res = $this->getCategories("",$wplg_order_category_ids);
							else 
								$_category_res = $this->getCategories($all_selected_categories,$wplg_order_category_ids); 

							
							if( count( $_category_res ) > 0 ) {  
						
								foreach( $_category_res as $_category ) { 
									$_total_product_count = $_total_product_count + $_category->count;
								} 
								
							} 
					 }
					 ?>  
						<div class="ik-product-category">  
							<?php if( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'no' ) { ?>
							<div class="ik-search-title" >
								 <input type="text" name="txtSearch" placeholder="<?php echo __( 'Search', 'richproductslistandgrid' ); ?>" value="<?php echo esc_html( htmlspecialchars( stripslashes( $product_search_text ) ) ); ?>" class="ik-product-search-text"  /> 
							</div>
							<?php }  
							if( count($_category_res) > 0 ) { 	?>    
								<div class="ik-search-category "  style="display:<?php echo (( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'yes' )?"none":"block"); ?>">
									<select name="selSearchCat" class='ik-drp-product-category' id="ik-drp-product-category" >
											
											<?php
												$_opt_arr = array();
												$_opt_all_id = array();
												foreach( $_category_res as $_category ) {  
												
													$_category_name = $_category->category;
													$_opt_all_id[] = $_category_id = $_category->id; 
													$_product_count = "";
													
													if( trim( $wplg_enable_product_count ) == "yes" ||  trim( $wplg_hide_empty_category ) == "yes" ) {
													
														$_product_count = " (".$_category->count.")";
														
														if( trim( $wplg_hide_empty_category ) == "yes"  && intval( $_category->count ) <= 0 )
															continue;
														
													} 
													
													$_opt_arr[] = '<option value="'.$_category_id.'">'.$_category_name.$_product_count.'</option>';
												
												}
											?>
											<option value="<?php echo implode(",",$_opt_all_id); ?>"><?php echo __( 'All', 'richproductslistandgrid' ); ?></option>
											<?php echo implode("",$_opt_arr); ?>
									</select> 
								</div> 	
							<?php } ?>	
							<div style="display:<?php echo (( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'yes' )?"none":"block"); ?>" class="ik-search-button" onclick='wplg_fillProducts( "<?php echo esc_js( $this->_config["vcode"] ); ?>", "<?php echo esc_js($all_selected_categories); ?>", request_obj_<?php echo esc_js( $this->_config["vcode"] ); ?>, 2)'> <img width="18px" alt="Search" height="18px" src="<?php echo wplg_media.'images/searchicon.png'; ?>" /> </div>
							
							<?php if( count($_category_res) <= 0 ) {
								  echo "<input type='hidden' value='0' id='ik-drp-product-category' class='ik-drp-product-category' />"; 
							} ?>
							<div class="clrb"></div>
						</div> 
						<?php 
							
						
						// Category and search text field end ==== 
						$__current_term_count = $this->getSqlResult( $all_selected_categories, $product_search_text, 0, 0, 1, $is_default_category_with_hidden, 1 );
						$__current_term_count = $__current_term_count[0]->total_val;
						$_total_products =  $__current_term_count; 
						 
						$post_list = $this->getSqlResult( $all_selected_categories, $product_search_text, 0, $_limit_end ); 
						if( count($post_list) > 0 ) {
							foreach ( $post_list as $_product ) { 
						
							$image  = $this->getProductImage( $_product->post_image, $wplg_image_content_width, $this->_config["wplg_image_height"] ); 
							$_author_name = esc_html($_product->display_name);
							$_author_image = get_avatar($_product->post_author,25);
							?> 
							<div style="<?php echo "width:".esc_attr($wplg_image_content_width)."px"; ?>" class='ikh-product-item-box pid-<?php echo esc_attr( $_product->post_id ); ?>'> 
								<div class="ikh-product-item ikh-simple"> 
								<?php 
									ob_start();
									if( $wplg_hide_product_image == "no" ) { ?> 	
										<div  class='ikh-image'  > 
											<a href="<?php echo get_permalink( $_product->post_id ); ?>"> 
												<?php echo $image; ?>
											</a>     
										</div>  
									<?php } 
									$_ob_image = ob_get_clean();  

									ob_start();
									?>   
								 	<div class='ikh-content'> 
									   <div class="ikh-content-data">
										
											<div class='ik-product-name'>												
												<?php if( sanitize_text_field( $this->_config["hide_product_title"] ) =='no'){ ?>  
													<a href="<?php echo get_permalink( $_product->post_id ); ?>" style="color:<?php echo esc_attr( $this->_config["title_text_color"] ); ?>" >
														<?php echo esc_html( $_product->post_title ); ?>
													</a>
												<?php } ?>	 
												
												<?php if( sanitize_text_field( $this->_config["wplg_hide_posted_date"] ) =='no'){ ?> 
														<div class='ik-product-date'>
															<i><?php echo date(get_option("date_format"),strtotime($_product->post_date)); ?></i>
														</div>
												<?php } ?>		
												
												 <?php if( $wplg_hide_product_short_content == "no" ) { ?>
													<div class='ik-product-sub-content'>
														<?php																		
														 if( strlen( strip_tags( $_product->post_content ) ) > intval( $wplg_hide_product_short_content_length ) ) 	
															echo substr( strip_tags( $_product->post_content ), 0, $wplg_hide_product_short_content_length )."..";  
														 else
															echo trim( strip_tags( $_product->post_content ) );																			
														?> 
													</div>
												<?php } ?> 
											</div>
											
											<?php if( sanitize_text_field( $this->_config["wplg_hide_comment_count"] ) =='no'){ ?> 
												<div class='ik-product-comment'>
													<?php 
														$_total_comments = (get_comment_count($_product->post_id)); 			
														if($_total_comments["total_comments"] > 0) {
															echo $_total_comments["total_comments"]; 
															?> <?php echo (($_total_comments["total_comments"]>1)?__( 'Comments', 'richproductslistandgrid' ):__( 'Comment', 'richproductslistandgrid' )); 
														}
													?>
												</div>
											<?php } ?>	
											
											<?php if( sanitize_text_field( $this->_config["wplg_hide_product_price"] ) =='no'){ ?> 
												<div class='ik-product-sale-price'>
													<?php echo get_woocommerce_currency_symbol().$_product->sale_price; ?>
												</div> 
											<?php } ?> 
												
											<?php if( sanitize_text_field( $this->_config["wplg_show_author_image_and_name"] ) =='yes') { ?> 
												<div class='ik-product-author'>
													<?php echo (($_author_image!==FALSE)?$_author_image:"<img src='".wplg_media."images/user-icon.png' width='25' height='25' />"); ?> <?php echo __( 'By', 'richproductslistandgrid' ); ?> <?php echo $_author_name; ?>
												</div>
											<?php } ?>	 		
											
											<?php if( $wplg_read_more_link == "no" ) { ?>
												<div class="wplg-read-more-link">
													<a class="lnk-product-content" href="<?php echo get_permalink( $_product->post_id ); ?>" >
														<?php echo __( 'Read More', 'richproductslistandgrid' ); ?>
													</a>
												</div>
											<?php } ?>  
											
											<?php if( sanitize_text_field( $this->_config["wplg_add_to_cart_button"] ) =='no') { ?> 
												<div class='ik-product-sale-btn-price' >
													<?php echo do_shortcode("[add_to_cart show_price='false' style='' id = '".$_product->post_id."']"); ?> 
												</div>
											<?php } ?>
											
											</div> 
									</div>	
								 <?php
								$_ob_content = ob_get_clean(); 
							
								if($wplg_mouse_hover_effect=='ikh-image-style-40'|| $wplg_mouse_hover_effect=='ikh-image-style-41' ){
									echo $_ob_content;
									echo $_ob_image;
								} else {
									echo $_ob_image;
									echo $_ob_content;														
								}	
								 ?>
								<div class="clr1"></div>
								</div> 
							</div> 
							<?php 
						} 
						 
						/******PAGING*******/
						if( $wplg_hide_paging == "no" &&  $wplg_select_paging_type == "load_more_option" && $_total_products > sanitize_text_field( $this->_config["number_of_product_display"] ) ) { 
		
									?>
									<div class="clr"></div>
									<div class='ik-product-load-more'  align="center" onclick='wplg_loadMoreProducts( "<?php echo esc_js( $all_selected_categories ); ?>", "<?php echo esc_js( $_limit_start+$_limit_end ); ?>", "<?php echo esc_js( $this->_config["vcode"] ); ?>", "<?php echo esc_js( $_total_products ); ?>", request_obj_<?php echo esc_js( $this->_config["vcode"] ); ?> )'>
										<?php echo __('Load More', 'richproductslistandgrid' ); ?>
									</div>
									<?php   
								 
						} else if( $wplg_hide_paging == "no" &&  $wplg_select_paging_type == "next_and_previous_links" ) { 
							
								?><div class="clr"></div>
								<div class="wplg-simple-paging"><?php
								echo $this->displayPagination(  0, $_total_products, $all_selected_categories, $_limit_start, $_limit_end, $this->_config["vcode"], 2 );
								?></div><div class="clr"></div><?php
							
						} else if( $wplg_hide_paging == "no" &&  $wplg_select_paging_type == "simple_numeric_pagination" ) { 
							
								?><div class="clr"></div>
								<div class="wplg-simple-paging"><?php
								echo $this->displayPagination(  0, $_total_products, $all_selected_categories, $_limit_start, $_limit_end, $this->_config["vcode"], 1 );
								?></div><div class="clr"></div><?php
							
						} else {
								?><div class="clr"></div><?php
						}
						/******PAGING END*********/
					} else {
						?><div class="ik-product-no-items"><?php echo __( 'No products found.', 'richproductslistandgrid' ); ?></div><?php 										
					}
					
					?><script type='text/javascript' language='javascript'><?php echo $this->wplg_js_obj( $this->_config ); ?></script><?php
					
				  
			?> 
			</div>
		</div>
		<div class="clr"></div>
	</div>
</div>