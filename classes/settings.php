<?php
/*
 * Creates a menu page "Batches & Promocodes"
 *  
 * *  
 * */
 
 if(!class_exists('group_buying_promocode_settings')) : 
	
	class group_buying_promocode_settings{
		
		//all hooks are here
		function __construct(){
						
			//menu page
			add_action('admin_menu', array($this,'create_the_menu'));
			
			//css and js adding
			add_action('admin_enqueue_scripts',array($this,'css_js_adding'));
			
			//if the form data is submitted
			add_action('init',array($this,'init'));
			
		}
		
		//init function
		function init(){
			//if the form is submitted
			if($_REQUEST['promocode_add_new_submit'] == 'Y'):
				include PromoDir . '/core/admin/add_new.php';
			endif;
			
			//download the csv
			if($_REQUEST['promocode-csv-down'] == 'Y') :
				include PromoDir . '/core/admin/csv.php';
			endif;
		}

		function css_js_adding(){
			if($_REQUEST['page'] == 'promocode_addition') :
				wp_register_style('promo_datepickder_css', PromoLink.'/assets/jquery-ui-1.8.16.custom/css/ui-lightness/jquery-ui-1.8.16.custom.css');
				wp_enqueue_style('promo_datepickder_css');
				wp_enqueue_script('jquery');
				wp_register_script('promo_datepickder_js',PromoLink.'/assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js',array('jquery'));
				wp_enqueue_script('promo_datepickder_js');
			endif;
		}
		
		//create the menupage
		function create_the_menu(){
			add_menu_page(__('promocode management'),__('Batches & Promocodes'),'activate_plugins','gb_promo_code',array($this,'batch_page'));
			add_submenu_page('gb_promo_code',__('add new batches with promocodes'),__('Add New'),'activate_plugins','promocode_addition',array($this,'batch_add'));
			add_submenu_page('gb_promo_code',__('promocode settings'),__('Settings'),'activate_plugins','promocode_settings',array($this,'promocode_settings'));
		}
			
		// promocode settings
		function promocode_settings(){
			if($_POST['optons_for_promocode'] == 'Y' ){
				include PromoDir . '/core/admin/options.php';
			}
			
			$options = get_option('promocode_settings');
			
			?>
			
			<div class="wrap">
			
				<?php screen_icon('options-general'); ?>
				<h2>Settings for PromoCOdes</h2>
				
				<?php if($message == 1){
					echo '<div style="height:30px;" class="updated"><p>Settings saved</p></div>' ;
				} ?>
				
				<form action="" method="post" class="form-table">
					<input type="hidden" name="optons_for_promocode" value="Y" />
					<table>
						<tr>
							<td> Code Length: </td>
							<td>
								<select name="length">
									<?php 
										for($i=5;$i<21;$i++){
											echo "<option value='$i' ".selected($i,$options['length'])." >$i</option>";
										}
									?>
								</select>
							</td>
						</tr>
												
						<tr>
							<td>Support Special Characters <code> !@#$%^&*() </code></td>
							<td><input name="sc" type="checkbox" value="y" <?php checked('y',$options['sc']); ?> /></td>
						</tr>
						<tr>
							<td>Support Extra Special Characters <code> -_ []{}<>~`+=,.;:/?| </code></td>
							<td><input name="esc" type="checkbox" value="y" <?php checked('y',$options['esc']); ?> /></td>
						</tr>						
					</table>
										
					<input type="submit" value="save" class="button-primary" />					
				</form>
				
			</div>
						
			
			<?php 
		}
		
		
		//menu page for batch page
		function batch_page(){
			//if deleting button is pressed
			if($_REQUEST['delete'] == 'yes' && $_REQUEST['id']){
				include PromoDir . '/core/admin/single-delete.php';
			}
			
			//if bulk action is made
			if($_POST['promo_delete_button'] == 'Apply'){
				include PromoDir . '/core/admin/bulk-delete.php';
			}
					
		?>
			<div class="wrap">
				<?php screen_icon('link-manager'); ?>
				<h2>Batches</h2>
				<?php 
					if($_REQUEST['delete'] == 'yes' && $_REQUEST['id']){
						echo '<div class="updated"><p>Deleted!</p></div>';
					}
					if($del_msg == 1){
						echo '<div class="updated"><p>Selected are Deleted!</p></div>';
					}
				?>			
				<form action="" method="post">
					
					<!-- the bulk action button -->
					<div class="tablenav top">				
						<div class="alignleft actions">					
							<select name="action">
								<option selected="selected" value="-1">Bulk Actions</option>
								<option value="delete">Delete</option>
							</select>
							<input id="doaction" class="button-secondary action" type="submit" value="Apply" name="promo_delete_button" />
						</div>
						<div class="alignleft actions">						
						</div>
						<br class="clear">
					</div>
					
					
					<!-- table view of all the batches -->
					<table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
						<thead>
							<tr>
								<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
									<input type="checkbox">
								</th>
								<th id="name" class="manage-column column-name sortable desc" style="" scope="col">
									<a href="#">
										<span>Name</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
								<th id="url" class="manage-column column-url sortable desc" style="" scope="col">
									<a href="#">
										<span>PromoCode Name</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
								<th id="url_cloaked" class="manage-column column-url sortable desc" style="" scope="col">
									<a href="#">
										<span>% Discount</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
								<th class="manage-column column-visible sortable desc" style="" scope="col">
									<a href="#">
										<span>Discount Limit</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
								<th class="manage-column column-visible sortable desc" style="" scope="col">
									<a href="#">
										<span>User Limit</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>			
								
								<th class="manage-column column-visible sortable desc" style="" scope="col">
									<a href="#">
										<span>Status</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
								<th class="manage-column column-visible sortable desc" style="" scope="col">
									<a href="#">
										<span>Expires on</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>							
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
									<input type="checkbox">
								</th>
								<th id="name" class="manage-column column-name sortable desc" style="" scope="col">
									<a href="#">
										<span>Name</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
								<th id="url" class="manage-column column-url sortable desc" style="" scope="col">
									<a href="#">
										<span>PromoCode Name</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
								<th id="url_cloaked" class="manage-column column-url sortable desc" style="" scope="col">
									<a href="#">
										<span>% Discount</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
								<th class="manage-column column-visible sortable desc" style="" scope="col">
									<a href="#">
										<span>Discount Limit</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
								<th class="manage-column column-visible sortable desc" style="" scope="col">
									<a href="#">
										<span>User Limit</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>															
								<th class="manage-column column-visible sortable desc" style="" scope="col">
									<a href="#">
										<span>Status</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
								<th class="manage-column column-visible sortable desc" style="" scope="col">
									<a href="#">
										<span>Expires on</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>							
							</tr>
						</tfoot>
						<tbody>
						<?php 
							//main content goes here
							global $wpdb;
							$table_1 = $wpdb->prefix . 'gb_batch';
							$table_2 = $wpdb->prefix . 'gb_promocode';
							$batchs = $wpdb->get_results("SELECT * FROM $table_1 ORDER BY `id` DESC");
							$home = get_option('siteurl');
							$edit_link = $home . '/wp-admin/admin.php?page=promocode_addition&edit=yes&id=';
							$del_link = $home . '/wp-admin/admin.php?page=gb_promo_code&delete=yes&id=';
							foreach($batchs as $batch){
								
								
							?>
								<tr>
									<th class='check-column' scope='row'>
										<input type='checkbox' value="<?php echo $batch->id; ?>" name='check[]'>
									</th>
									<td> 
										<?php echo $batch->batch; ?>
										<div class='row-actions'>
											<a href="<?php echo $edit_link . $batch->id; ?>">Edit</a>&nbsp| 
											<a style='color:red' href="<?php echo $del_link . $batch->id; ?>">Delete</a>
										</div>
									</td>
									<td>
										<?php echo $batch->name; ?>
									</td>									
									<td>
										<?php echo $batch->discount; ?>
									</td>
									<td>
										<?php echo $batch->min_price;  ?>
									</td>
									<td>
										<?php echo $batch->per_user; ?>
									</td>
									<td>
										<?php echo ($batch->status == 'off')?'inactive':'active';  ?>
									</td>
									<td>
										<?php echo date("m/d/Y",$batch->expire); ?>
									</td>
								</tr>
							<?php 
							}
						?>						
						</tbody>						
					
				</form> <!-- form type -->
			
			</div> <!-- wrap -->		
					
		<?php			
		} //batch-page
		
		//submenu page
		function batch_add(){
			$names = array();
			$codes = array();
			
			$action = get_option('siteurl') . '/wp-admin/admin.php?page=promocode_addition';			
			
			//if someone edits the batch
			if($_REQUEST['edit'] == 'yes' && !empty($_REQUEST['id'])){				
				include PromoDir . '/core/admin/edit_batch.php';
			}
			
		?>
		
			<div class="wrap">
				<?php screen_icon('link-manager'); ?>
				<h2>Add New</h2>
				<?php 			
					if($_REQUEST['msg'] == 'y'):					
						echo '<div class="updated"><p>Saved</p></div>';
					endif;
					
				?>
				<form name="addpromo" id="promoadd" method="post" action="<?php echo $action; ?>">				
					<input type="hidden" name="promocode_add_new_submit" value="Y" />
					<input type="hidden" name="batch_edit" value="<?php echo $batch_data->id; ?>" />				
					<div id="poststuff" class="metabox-holder has-right-sidebar">
					
						<div id="post-body">
							<div id="post-body-content">
							
								<div id="namediv" class="stuffbox">
									<h3> <level for="link_name" >Add new Batch</level> </h3>
									<div class="inside">
										<table class="form-table">
											<tbody>
												<tr class="site_id_row">
													<th valign="top" scope="row">
														Batch Name : 
													</th>
													<td valign="top">
														<input type="text" name="batch_name" value="<?php echo $batch_data->batch ; ?>" />
													</td>
												</tr>
												
												<tr class="site_id_row">
													<th valign="top" scope="row">
														Promo Code Name : 
													</th>
													<td valign="top">
														<input type="text" name="promo_name" value="<?php echo $batch_data->name ; ?>" />
													</td>
												</tr>
												
												<tr class="site_id_row">
													<th valign="top" scope="row">
														Discount (%) : 
													</th>
													<td valign="top">
														<input type="text" name="batch_discount" value="<?php echo $batch_data->discount ; ?>" />
													</td>
												</tr>
												
												<tr class="site_id_row">
													<th valign="top" scope="row">
														Total discount should not exceed : 
													</th>
													<td valign="top">
														<input type="text" name="batch_discount_limit" value="<?php echo $batch_data->min_price ; ?>" />
													</td>
												</tr>
												
												
												<tr class="site_id_row">
													<th valign="top" scope="row">
														No of PromoCodes : 
													</th>
													<td valign="top">
														<input type="text" name="promocode_amount" value="<?php echo (is_array($s_codes)) ? count($s_codes) : '' ; ?>" />
													</td>
												</tr>
												
												<tr class="site_id_row">
													<th valign="top" scope="row">
														How many codes from this batch may any single user redeem : 
													</th>
													<td valign="top">
														<input type="text" name="max_per_user" value="<?php echo $batch_data->per_user; ?>" />
													</td>
												</tr>
												 
											<?php if($_REQUEST['edit'] == 'yes' && !empty($_REQUEST['id'])) : ?>												 												 
												<tr class="site_id_row">
													<th valign="top" scope="row">
														Choose an Option
													</th>
													<td>
														<p><input type="radio" name="promocode_radio" value="1" checked="checked" /> Want to keep the existing codes </p>
														<p><input type="radio" name="promocode_radio" value="2" /> Want to add more codes</p>
														<p><input type="radio" name="promocode_radio" value="3" /> Replace the Exiting codes</p>
													</td>
												</tr>
											<?php endif; ?>
												<tr class="site_id_row">
													<th valign="top" scope="row">
														Expires on : mm/dd/yyyy
													</th>
													<td valign="top">
														<input type="text" name="promoexpire" value="<?php echo ($batch_data->expire) ? date("m/d/Y",$batch_data->expire) : '' ; ?>" id="promocode_expire_date" />
													</td>
												</tr>
											</tbody>
										</table>																			
									</div>
								</div> <!-- stuffbox -->																				
								
							</div> <!-- post-body-content -->
						</div> <!-- post-body -->
					
					<div class="inner-sidebar">
							<div id="linkgoaldiv" class="postbox ">
								<div class="handlediv" title="Click to toggle"><br/></div>
								<h3 class="hndle"><span> Advanced Settings </span></h3>
								<div class="inside">
									<input type="checkbox" name="batch_status" value='off' <?php checked('off',$batch_data->status); ?> /> Disable the Batch
								</div>
							</div>
					</div> <!-- innder sidebar -->
					
					<div id="side-info-column" class="inner-sidebar">
							<div id="linkgoaldiv" class="postbox ">
								<div class="handlediv" title="Click to toggle"><br/></div>
								<h3 class="hndle"><span> Generate/Update </span></h3>
								<div class="inside">
									<input type="submit" value="<?php echo ($batch_data->id == '')? 'GENERATE &nbsp BATCH' : 'UPDATE &nbsp BATCH' ?>" class="button-primary" />
								</div>
							</div>
					</div> <!-- innder sidebar -->					
				</div> <!-- poststuff -->
				</form>
			</div> <!-- wrap -->
				
				<div style="clear:both"></div>
				<div class="wrap">
						<?php if(!empty($codes)) : ?>			
						
							<?php foreach($codes as $key=>$code) : ?>
							
							<?php 
								//some special changes
								$batch_name = preg_replace('/[ ]/','-',$batch_data->batch);
								$code_name = preg_replace('/[ ]/','-',$batch_data->name);
								$date_name = preg_replace('/[\/]/','-',$key);
								$total_name = $batch_name . '_' . $code_name . '_' . $date_name;
							?>
								<div name="created_promocode" style="width:100%" >												
									<form method="post" action="">
										<input type="hidden" name="promodate" value="<?php echo $key; ?>" />
										<input type="hidden" name="promocode-csv-down" value='Y' />
										<input type="hidden" name="promo-code-batch" value="<?php echo $batch_data->id; ?>" />
										<br/>
										<h4 style="text-align:center;"> <?php echo $total_name; ?> 
											<select name="type-of-code">
												<option value="n">unused</option>
												<option value="">used</option>
												<option value="all">all</option>
											</select>														
											<input type="submit" name="csv-download" value="Export" class="button-primary" />
										</h4>
									</form>
									<table class="widefat">
									<tr>
									<?php foreach ($code as $k=>$c) :
										if(fmod($k,15) == 0) echo "</tr><tr>";
											echo "<td>$c</td>";
										 endforeach;
									 ?>
									</tr>																									
								</table>																					
							</div>
						<?php endforeach; ?>
						
					<?php else:
						echo "<h2> PromoCode Not yet generated!</h2>";
						endif;
					?>					
				</div>	<!-- wrap -->		
			

			
			<!-- datepicker system incorporating -->
			<script type="text/javascript">
				jQuery('#promocode_expire_date').datepicker({'dateFormat':'mm/dd/yy'});
			</script>
				
		<?php 
		}		
		
	}//class
	
	$PromoCodeAdmin = new group_buying_promocode_settings();
	
 endif;
?>