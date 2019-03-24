<?php if ( ! defined( 'ABSPATH' ) ) exit; 
	 $params = $_REQUEST;  
	 $all_selected_categories = $params["all_selected_categories"]; 
	 $category_id_default =( ( isset( $params["category_id_default"] ) && trim( $params["category_id_default"] ) != ""  ) ? esc_html( $params["category_id_default"] ) : esc_html( $all_selected_categories ));
	 $category_id_all =( ( isset( $params["category_id"] ) && trim( $params["category_id"] ) != ""  ) ? esc_html( $params["category_id"] ) : "" );	 
	 $wplg_order_category_ids =( ( isset( $params["wplg_order_category_ids"] ) && trim( $params["wplg_order_category_ids"] ) != ""  ) ? ( $params["wplg_order_category_ids"] ) : "" );	 	 
	 $wplg_enable_product_count =( isset( $params["wplg_enable_product_count"] ) ? esc_html( $params["wplg_enable_product_count"] ) : "" ); 
	 $product_search_text =( isset( $params["product_search_text"] ) ? esc_html( $params["product_search_text"] ) : "" ); 
	 $_limit_start =( isset( $params["limit_start"] ) ? intval( $params["limit_start"] ) : 0 );
	 $_limit_end = intval( $params["number_of_product_display"] );
	 $is_default_category_with_hidden = 0; 
	 $static_width = ( ( isset( $params["wplg_image_content_width"] ) && intval( $params["wplg_image_content_width"] ) > 0  ) ? intval($params["wplg_image_content_width"]) : 180 );
	 $final_width = $params["wplg_image_content_width"]; 
	 $wplg_image_height = $params["wplg_image_height"];   
	 $category_type = $params["category_type"];  
	 
	 $wplg_mouse_hover_effect = $params["wplg_mouse_hover_effect"]; 
	 
	     
	if( $this->wplg_getTotalProducts( $all_selected_categories, $product_search_text, 0, $is_default_category_with_hidden ) > 0 ) {
	
		$_category_res = array();
		if( trim($category_type) != "0" ) {
			if( trim($all_selected_categories)=="0" || trim($all_selected_categories) == "" )
				$_category_res = $this->getCategories("",$wplg_order_category_ids);
			else 
				$_category_res = $this->getCategories($all_selected_categories,$wplg_order_category_ids);
		}	
		
		if(  !( sanitize_text_field( $params["hide_searchbox"] ) == 'yes' ) ) { 
			?> 
			<div class="ik-product-category"> 
				<?php if( sanitize_text_field( $params["hide_searchbox"] ) == 'no' ) { ?>
					<div class="ik-search-title" >
					  <input type="text" name="txtSearch" placeholder="<?php echo __( 'Search', 'richproductslistandgrid' ); ?>" value="<?php echo esc_html( htmlspecialchars( stripslashes( $product_search_text ) ) ); ?>" class="ik-product-search-text"  /> 
					</div>	
				<?php }  if( count($_category_res) > 0 ) { 	?>    
						<div class="ik-search-category " >
							  <select name="selSearchCat" class='ik-drp-product-category' id="ik-drp-product-category" >
									<option <?php echo ((count(explode(",", $all_selected_categories )) > 1 && $all_selected_categories == $category_id_default )?"selected='true'":"");?> value="<?php echo $all_selected_categories; ?>"><?php echo __( 'All', 'richproductslistandgrid' ); ?></option>
									<?php
										foreach( $_category_res as $_category ) {  
										
											$_category_name = $_category->category;
											$_category_id = $_category->id; 
											$_product_count = "";
											
											if( trim( $wplg_enable_product_count ) == "yes"  ) {
											
												$_product_count = " (".$_category->count.")";
												
												if( trim( $wplg_hide_empty_category ) == "yes"  && intval( $_category->count ) <= 0 )
													continue;
												
											}  
											if((count(explode(",",$category_id_default)) == 1) && $category_id_default==$_category_id)
												echo '<option selected="true" value="'.$_category_id.'">'.$_category_name.$_product_count.'</option>';
											else
												echo '<option value="'.$_category_id.'">'.$_category_name.$_product_count.'</option>';									
										
										}
									?>
							</select> 
					   </div>
				<?php } ?>	 
					<div class="ik-search-button" onclick='wplg_fillProducts( "<?php echo esc_js( $params["vcode"] ); ?>", "<?php echo esc_js( $category_id_default ); ?>", request_obj_<?php echo esc_js( $params["vcode"] ); ?>, 2)'> <img width="18px" alt="Search" height="18px" src="<?php echo wplg_media.'images/searchicon.png'; ?>" /></div>
					<div class="clrb"></div>
			</div>
		 <?php
		}
	} //else { echo "<input type='hidden' value='".$category_id."' class='ik-drp-product-category' />"; }
	 
	  $_total_products = $this->wplg_getTotalProducts( $category_id_default, $product_search_text, 1, $is_default_category_with_hidden );
	if( $_total_products <= 0 ) {
		?><div class="ik-product-no-items"><?php echo __( 'No products found.', 'richproductslistandgrid' ); ?></div><?php
		die();
	} 
	$post_list = $this->getProductList( $category_id_default, $product_search_text, $_limit_end );	 
	 
	foreach ( $post_list as $_product ) { 
		$image  = $this->getProductImage( $_product->post_image, $final_width, $params["wplg_image_height"] ); 
		$_author_name = esc_html($_product->display_name);
	    $_author_image = get_avatar($_product->post_author,25);
		?>
		<div style="width:<?php echo esc_attr($final_width); ?>px; " class='ikh-product-item-box pid-<?php echo esc_attr( $_product->post_id ); ?>'> 
			<div class="ikh-product-item ikh-simple"> 
			<?php
				ob_start();
				if( $params["wplg_hide_product_image"] == "no" ) { ?>
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
							<?php if( sanitize_text_field( $params["hide_product_title"] ) =='no'){ ?> 
								<a href="<?php echo get_permalink( $_product->post_id ); ?>" style="color:<?php echo esc_attr( $params["title_text_color"] ); ?>" >
									<?php echo esc_html( $_product->post_title ); ?>
								</a>
							<?php } ?>	 
							
							<?php if( sanitize_text_field( $params["wplg_hide_posted_date"] ) =='no'){ ?> 
								<div class='ik-product-date'>
									<i><?php echo date(get_option("date_format"),strtotime($_product->post_date)); ?></i>
								</div>
							<?php } ?>	
						
							<?php  
								if( $params["wplg_hide_product_short_content"] == "no" ) { ?>
								<div class='ik-product-sub-content'>
									<?php
									if( strlen( strip_tags( $_product->post_content ) ) > intval( $params["wplg_hide_product_short_content_length"] ) ) 	
										echo substr( strip_tags( $_product->post_content ), 0, $params["wplg_hide_product_short_content_length"] ).".."; 
									else
										echo trim( strip_tags( $_product->post_content ) );
									?> 
								</div>
							<?php } ?>										
						</div>
						
						<?php if( sanitize_text_field( $params["wplg_hide_comment_count"] ) =='no'){ ?> 
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
						
						<?php if( sanitize_text_field( $params["wplg_hide_product_price"] ) =='no'){ ?> 
							<div class='ik-product-sale-price'>
								<?php echo get_woocommerce_currency_symbol().$_product->sale_price; ?>
							</div> 
						<?php } ?> 
						
						
						<?php if( sanitize_text_field( $params["wplg_show_author_image_and_name"] ) =='yes') { ?> 
							<div class='ik-product-author'>
								<?php echo (($_author_image!==FALSE)?$_author_image:"<img src='".wplg_media."images/user-icon.png' width='25' height='25' />"); ?> <?php echo __( 'By', 'richproductslistandgrid' ); ?> <?php echo $_author_name; ?>
							</div>
						<?php } ?>	 	
						
						<?php if( $params["wplg_read_more_link"] == "no" ) { ?>
							<div class="wplg-read-more-link">
								<a class="lnk-product-content" href="<?php echo get_permalink( $_product->post_id ); ?>" >
									<?php echo __( 'Read More', 'richproductslistandgrid' ); ?>
								</a>
							</div>
						<?php } ?>  
						
						<?php if( sanitize_text_field( $params["wplg_add_to_cart_button"] ) =='no'){ ?> 
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
	
	 
	
	if( $params["wplg_hide_paging"] == "no" && $params["wplg_select_paging_type"] == "load_more_option"   && $_total_products > sanitize_text_field( $params["number_of_product_display"] ) ) {
	
		?>	
		<div class="clr"></div>
		<div style="display:none" class='ik-product-load-more'  align="center" onclick = 'wplg_loadMoreProducts( "<?php echo esc_js( $category_id_default ); ?>", "<?php echo esc_js( $_limit_start+$_limit_end ); ?>", "<?php echo esc_js( $params["vcode"] ); ?>", "<?php echo esc_js( $_total_products ); ?>", request_obj_<?php echo esc_js( $params["vcode"] ); ?> )'>
			<?php echo __('Load More', 'richproductslistandgrid' ); ?>
		</div>
		<?php 
		
	} else if( $params["wplg_hide_paging"] == "no" && $params["wplg_select_paging_type"] == "next_and_previous_links" ) {
	
		?><div class="clr"></div>
		<div style="display:none" class="wplg-simple-paging"><?php
			echo $this->displayPagination(  0, $_total_products, $category_id_default, $_limit_start, $_limit_end, $params["vcode"], 2 );
		?></div><div class="clr"></div><?php
	
	} else if( $params["wplg_hide_paging"] == "no" && $params["wplg_select_paging_type"] == "simple_numeric_pagination" ) {
	
		?><div class="clr"></div>
		<div style="display:none" class="wplg-simple-paging"><?php
			echo $this->displayPagination(  0, $_total_products, $category_id_default, $_limit_start, $_limit_end, $params["vcode"], 1 );
		?></div><div class="clr"></div><?php	
	
	} else {
		?> <div class="clr"></div> <?php
	} 
	?><script type='text/javascript' language='javascript'><?php echo $this->wplg_js_obj( $params ); ?></script>
	