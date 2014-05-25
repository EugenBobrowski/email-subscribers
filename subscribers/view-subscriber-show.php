<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
// Form submitted, check the data
$search = isset($_GET['search']) ? $_GET['search'] : 'A,B,C';
if (isset($_POST['frm_es_display']) && $_POST['frm_es_display'] == 'yes')
{
	$did = isset($_GET['did']) ? $_GET['did'] : '0';
	
	$es_success = '';
	$es_success_msg = FALSE;
	if (isset($_POST['frm_es_bulkaction']) && $_POST['frm_es_bulkaction'] != 'delete' && $_POST['frm_es_bulkaction'] != 'resend')
	{	
		// First check if ID exist with requested ID
		$result = es_cls_dbquery::es_view_subscriber_count($did);
		if ($result != '1')
		{
			?>
			<div class="error fade">
			  <p><strong><?php _e('Oops, selected details doesnt exist.', ES_TDOMAIN); ?></strong></p>
			</div>
			<?php
		}
		else
		{
			// Form submitted, check the action
			if (isset($_GET['ac']) && $_GET['ac'] == 'del' && isset($_GET['did']) && $_GET['did'] != '')
			{
				//	Just security thingy that wordpress offers us
				check_admin_referer('es_form_show');
				
				//	Delete selected record from the table
				es_cls_dbquery::es_view_subscriber_delete($did);
				
				//	Set success message
				$es_success_msg = TRUE;
				$es_success = __('Selected record was successfully deleted.', ES_TDOMAIN);
			}
			
			if (isset($_GET['ac']) && $_GET['ac'] == 'resend' && isset($_GET['did']) && $_GET['did'] != '')
			{
				$did = isset($_GET['did']) ? $_GET['did'] : '0';
				$setting = array();
				$setting = es_cls_settings::es_setting_select(1);
				if($setting['es_c_optinoption'] <> "Double Opt In")
				{
					?>
					<div class="error fade">
					  <p><strong><?php _e('To send confirmation mail, Please change the Opt-in option to Double Opt In.', ES_TDOMAIN); ?></strong></p>
					</div>
					<?php
				}
				else
				{
					es_cls_sendmail::es_prepare_optin("single", $did, $idlist = "");
					es_cls_dbquery::es_view_subscriber_upd_status("Unconfirmed", $did);
					$es_success_msg = TRUE;
					$es_success  = __('Confirmation email resent successfully.', ES_TDOMAIN);
				}
			}
		}
	}
	else
	{
		check_admin_referer('es_form_show');
		
		if (isset($_POST['frm_es_bulkaction']) && $_POST['frm_es_bulkaction'] == 'delete')
		{
			$chk_delete = isset($_POST['chk_delete']) ? $_POST['chk_delete'] : '';
			if(!empty($chk_delete))
			{			
				$count = count($chk_delete);
				for($i=0; $i<$count; $i++)
				{
					$del_id = $chk_delete[$i];
					es_cls_dbquery::es_view_subscriber_delete($del_id);
				}
				
				//	Set success message
				$es_success_msg = TRUE;
				$es_success = __('Selected record was successfully deleted.', ES_TDOMAIN);
			}
			else
			{
				?>
				<div class="error fade">
				  <p><strong><?php _e('Oops, No record was selected.', ES_TDOMAIN); ?></strong></p>
				</div>
				<?php
			}
		}
		elseif (isset($_POST['frm_es_bulkaction']) && $_POST['frm_es_bulkaction'] == 'resend')
		{
			$chk_delete = isset($_POST['chk_delete']) ? $_POST['chk_delete'] : '';
			
			$setting = array();
			$setting = es_cls_settings::es_setting_select(1);
			if($setting['es_c_optinoption'] <> "Double Opt In")
			{
				?>
				<div class="error fade">
				  <p><strong><?php _e('To send confirmation mail, Please change the Opt-in option to Double Opt In.', ES_TDOMAIN); ?></strong></p>
				</div>
				<?php
			}
			else
			{
				if(!empty($chk_delete))
				{			
					$count = count($chk_delete);
					$idlist = "";
					for($i = 0; $i<$count; $i++)
					{
						$del_id = $chk_delete[$i];
						if($i < 1)
						{
							$idlist = $del_id;
						}
						else
						{
							$idlist = $idlist . ", " . $del_id;
						}
					}
					es_cls_sendmail::es_prepare_optin("group", 0, $idlist);
					es_cls_dbquery::es_view_subscriber_upd_status("Unconfirmed", $idlist);
					$es_success_msg = TRUE;
					$es_success = __('Confirmation email(s) resent successfully.', ES_TDOMAIN);
				}
				else
				{
					?>
					<div class="error fade">
					  <p><strong><?php _e('Oops, No record was selected.', ES_TDOMAIN); ?></strong></p>
					</div>
					<?php
				}
			}
		}
	}
	
	if ($es_success_msg == TRUE)
	{
		?><div class="updated fade"><p><strong><?php echo $es_success; ?></strong></p></div><?php
	}
}
?>
<script language="javaScript" src="<?php echo ES_URL; ?>subscribers/view-subscriber.js"></script>
<div class="wrap">
  <div id="icon-plugins" class="icon32"></div>
  <h2><?php _e(ES_PLUGIN_DISPLAY, ES_TDOMAIN); ?></h2>
  <div class="tool-box">
  <h3><?php _e('View subscriber', ES_TDOMAIN); ?> 
  <a class="add-new-h2" href="<?php echo ES_ADMINURL; ?>?page=es-view-subscribers&amp;ac=add"><?php _e('Add New', ES_TDOMAIN); ?></a></h3>
	<?php
	$myData = array();
	$myData = es_cls_dbquery::es_view_subscriber_search($search, 0);
	?>
	<div class="tablenav">
		<span style="text-align:left;">
			<a class="button add-new-h2" href="admin.php?page=es-view-subscribers&search=A,B,C">A,B,C</a>&nbsp;&nbsp; 
			<a class="button add-new-h2" href="admin.php?page=es-view-subscribers&search=D,E,F">D,E,F</a>&nbsp;&nbsp; 
			<a class="button add-new-h2" href="admin.php?page=es-view-subscribers&search=G,H,I">G,H,I</a>&nbsp;&nbsp; 
			<a class="button add-new-h2" href="admin.php?page=es-view-subscribers&search=J,K,L">J,K,L</a>&nbsp;&nbsp; 
			<a class="button add-new-h2" href="admin.php?page=es-view-subscribers&search=M,N,O">M,N,O</a>&nbsp;&nbsp; 
			<a class="button add-new-h2" href="admin.php?page=es-view-subscribers&search=P,Q,R">P,Q,R</a>&nbsp;&nbsp; 
			<a class="button add-new-h2" href="admin.php?page=es-view-subscribers&search=S,T,U">S,T,U</a>&nbsp;&nbsp; 
			<a class="button add-new-h2" href="admin.php?page=es-view-subscribers&search=V,W,X,Y,Z">V,W,X,Y,Z</a>&nbsp;&nbsp; 
			<a class="button add-new-h2" href="admin.php?page=es-view-subscribers&search=0,1,2,3,4,5,6,7,8,9">0-9</a>&nbsp;&nbsp; 
			<a class="button add-new-h2" href="admin.php?page=es-view-subscribers&search=ALL">ALL</a> 
		<span>
		<span style="float:right;">
			<a class="button add-new-h2" href="<?php echo ES_ADMINURL; ?>?page=es-view-subscribers&amp;ac=add"><?php _e('Add New', ES_TDOMAIN); ?></a> 
			<a class="button add-new-h2" href="<?php echo ES_ADMINURL; ?>?page=es-view-subscribers&amp;ac=import"><?php _e('Import Email', ES_TDOMAIN); ?></a> 
			<a class="button add-new-h2" href="<?php echo ES_ADMINURL; ?>?page=es-view-subscribers&amp;ac=export"><?php _e('Export Email (CSV)', ES_TDOMAIN); ?></a> 
			<a class="button add-new-h2" target="_blank" href="<?php echo ES_FAV; ?>"><?php _e('Help', ES_TDOMAIN); ?></a> 
		</span>
    </div>
    <form name="frm_es_display" method="post" onsubmit="return _es_bulkaction()">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
            <th class="check-column" scope="col">
			<input type="checkbox" name="es_checkall" id="es_checkall" onClick="_es_checkall('frm_es_display', 'chk_delete[]', this.checked);" /></th>
            <th scope="col"><?php _e('Sno', ES_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Email address', ES_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Name', ES_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Status', ES_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Group', ES_TDOMAIN); ?></th>
            <th scope="col"><?php _e('Database ID', ES_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Action', ES_TDOMAIN); ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th class="check-column" scope="col">
			<input type="checkbox" name="es_checkall" id="es_checkall" onClick="_es_checkall('frm_es_display', 'chk_delete[]', this.checked);" /></th>
            <th scope="col"><?php _e('Sno', ES_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Email address', ES_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Name', ES_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Status', ES_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Group', ES_TDOMAIN); ?></th>
            <th scope="col"><?php _e('Database ID', ES_TDOMAIN); ?></th>
			<th scope="col"><?php _e('Action', ES_TDOMAIN); ?></th>
          </tr>
        </tfoot>
        <tbody>
          <?php 
			$i = 0;
			$displayisthere = FALSE;
			if(count($myData) > 0)
			{
				$i = 1;
				foreach ($myData as $data)
				{					
					?>
					<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
					<td align="left"><input name="chk_delete[]" id="chk_delete[]" type="checkbox" value="<?php echo $data['es_email_id'] ?>" /></td>
					<td><?php echo $i; ?></td>
					<td><?php echo $data['es_email_mail']; ?></td>
					<td><?php echo stripslashes($data['es_email_name']); ?></td>     
					<td><?php echo es_cls_common::es_disp_status($data['es_email_status']); ?></td>
					<td><?php echo $data['es_email_group']; ?></td>
					<td><?php echo $data['es_email_id']; ?></td>
					<td><div> 
					<span class="edit">
						<a title="Edit" href="<?php echo ES_ADMINURL; ?>?page=es-view-subscribers&amp;ac=edit&search=<?php echo $search; ?>&amp;did=<?php echo $data['es_email_id']; ?>">
					<?php _e('Edit', ES_TDOMAIN); ?></a> | </span> 
					<span class="trash">
					<a onClick="javascript:_es_delete('<?php echo $data['es_email_id']; ?>','<?php echo $search; ?>')" href="javascript:void(0);">
					<?php _e('Delete', ES_TDOMAIN); ?></a>
					</span>
					<?php
					if($data['es_email_status'] != "Confirmed")
					{
						?>
						<span class="edit"> 
						| <a onClick="javascript:_es_resend('<?php echo $data['es_email_id']; ?>','<?php echo $search; ?>')" href="javascript:void(0);">
						<?php _e('Resend Confirmation', ES_TDOMAIN); ?></a>
						</span> 
						<?php
					}
					?>
					</div>
					</td>
					</tr>
					<?php
					$i = $i+1;
				} 
			}
			else
			{
				?>
				<tr>
					<td colspan="7" align="center"><?php _e('No records available. Please use the above alphabet search button to search.', ES_TDOMAIN); ?></td>
				</tr>
				<?php 
			}
			?>
        </tbody>
      </table>
      <?php wp_nonce_field('es_form_show'); ?>
      <input type="hidden" name="frm_es_display" value="yes"/>
	  <input type="hidden" name="frm_es_bulkaction" value=""/>
	  <input name="searchquery" id="searchquery" type="hidden" value="<?php echo $search; ?>" />
	<div style="padding-top:10px;"></div>
    <div class="tablenav">
		<div class="alignleft">
			<select name="action" id="action">
				<option value=""><?php _e('Bulk Actions', ES_TDOMAIN); ?></option>
				<option value="delete"><?php _e('Delete', ES_TDOMAIN); ?></option>
				<option value="resend"><?php _e('Resend Confirmation', ES_TDOMAIN); ?></option>
			</select>
			<input type="submit" value="<?php _e('Apply', ES_TDOMAIN); ?>" class="button action" id="doaction" name="">
		</div>
		<div class="alignright">
			<a class="button add-new-h2" href="<?php echo ES_ADMINURL; ?>?page=es-view-subscribers&amp;ac=add"><?php _e('Add New', ES_TDOMAIN); ?></a> 
			<a class="button add-new-h2" href="<?php echo ES_ADMINURL; ?>?page=es-view-subscribers&amp;ac=import"><?php _e('Import Email', ES_TDOMAIN); ?></a> 
			<a class="button add-new-h2" href="<?php echo ES_ADMINURL; ?>?page=es-view-subscribers&amp;ac=export"><?php _e('Export Email (CSV)', ES_TDOMAIN); ?></a> 
			<a class="button add-new-h2" target="_blank" href="<?php echo ES_FAV; ?>"><?php _e('Help', ES_TDOMAIN); ?></a> 
		</div>
    </div>
	</form>
    <p class="description"><?php echo ES_OFFICIAL; ?></p>
  </div>
</div>