<?php include_once'header.php';?>
<?php include_once'sidebar.php';?>
    <!-- Start of the container-->
    <div class="container-admin">
      <div class="page-header">
		<ul class="breadcrumb breadcrumb-admin">
  			<li><i class="fa fa-home"></i> <?php echo HOME ?></li>
  			<li class="active"><?php echo MANAGE_PAGES ?></li>
            <span class="donate_support"><span class="label label-danger"><?php echo DONATE ?> </span></span>
        <div class="donate">
        <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8EKWYJABNLDE2" data-toggle="tooltip" data-placement="bottom" target="_blank" title="<?php echo DONATE_TEXT ?>"><img src="<?php echo ISVIPI_STYLE_URL.'images/donate.png';?>" width="100%" alt="" /></a>
        </div>
        </ul>
     </div>
     <!-- Start of main_content-->
     <div class="main_content">
     <div style="clear:both"></div>
     <div class="dash_admin_panel_cont"> <!--start of dash_cont_stat-->
       <div class="row">
     	<div class="panel panel-default midi-left">
    	<div class="panel-heading"><strong><?php echo TERMS_CONDITIONS ?> </strong></div>
          <form method="post" action="<?php echo ISVIPI_URL.'conf/managePages/' ?>">
          <input type="hidden" name="page" value="terms">
          <input type="hidden" name="p_slug" value="terms">
          <table>
           <tbody>
           <tr>
           <td><?php echo TITLE ?>: <input type="text" class="form-control" name="title" value="<?php termsConditions(); echo $Termstitle?>"></td>
           </tr>
           <tr>
           <td width="450"><?php echo CONTENT ?>: <textarea class="form-control" name="termsCont" rows="8" required="required"><?php echo $Termscontent?></textarea></td>
           </tr>
           <tr>
           <td>
           <?php 
		   $sub = str_replace(" ", "_", $Termstitle);
		   ?>
           <button type="submit" class="btn btn-primary"><?php echo UPDATE_TERMS ?></button> <a href="<?php echo ISVIPI_URL.'p/'.$sub.'-p'.$termsID.'#.'.rand(0, 9999) ?>" target="_blank"><span class="label label-info" style="padding:5px; margin-left:10px"><?php echo VIEW_PAGE ?></span></a></td>
           
           </tr>
           </tbody>
          </table>
          </form>
  		</div>  
        <div class="panel panel-default midi-left2">
    	<div class="panel-heading"><strong><?php echo PRIVACY_POLICY ?> </strong></div>
          <table class="table">
           <tbody>
          <form method="post" action="<?php echo ISVIPI_URL.'conf/managePages/' ?>">
          <input type="hidden" name="page" value="pPolicy">
          <input type="hidden" name="p_slug" value="pPolicy">
          <table>
           <tbody>
           <tr>
           <td><?php echo TITLE ?>: <input type="text" class="form-control" name="pTitle" value="<?php privacyPolicy(); echo $policyTitle?>"></td>
           </tr>
           <tr>
           <td width="450"><?php echo CONTENT ?>: <textarea class="form-control" name="pContent" rows="8" required="required"><?php echo $policyContent?></textarea></td>
           </tr>
           <tr>
           <td>
           <?php 
		   $sub = str_replace(" ", "_", $policyTitle);
		   ?>
           <button type="submit" class="btn btn-primary"><?php echo UPD_PRIVACY_POLICY ?></button>
           <a href="<?php echo ISVIPI_URL.'p/'.$sub.'-p'.$policyID.'#.'.rand(0, 9999) ?>" target="_blank"><span class="label label-info" style="padding:5px; margin-left:10px"><?php echo VIEW_PAGE ?></span></a>
           </td>
           </tr>
           </tbody>
          </table>
          </form>
           </tbody>
           </table>
  		</div> 
     </div> 
<hr />
     	<div class="panel panel-default midi-left">
    	<div class="panel-heading"><strong><?php echo PUBLISHED_PAGES ?> </strong></div>
        <table class="table table-bordered">
        <?php getAllPages()?>
          <thead>
          <tr>
          <th width="20"><?php echo ID ?></th>
           <th width="200"><?php echo TITLE ?></th>
           <th width="50"><?php echo ACTION ?></th>
           </tr>
           </thead>
           <tbody>
           <?php while($getAllP->fetch()){?>
           <tr>
           <td><?php echo $p_id ?></td>
           <?php 
		   $sub = str_replace(" ", "_", $p_title);
		   ?>
           <td><a href="<?php echo ISVIPI_URL.'p/'.$sub.'-p'.$p_id.'#.'.rand(0, 9999) ?>" target="_blank" title="<?php echo VIEW_PAGE ?>" data-toggle="tooltip" data-placement="top"><?php echo $p_title ?></a></td>
           <td><a href="<?php echo ISVIPI_URL.'admin/edit_page/'.encrypt_str($p_id) ?>" title="<?php echo EDIT_PAGE ?>" data-toggle="tooltip" data-placement="top"><i class="fa fa-pencil"></i></a> | <a href="<?php echo ISVIPI_URL.'conf/managePages/del/'.encrypt_str($p_id).'' ?>" title="<?php echo PAGES_DEL ?>" data-toggle="tooltip" data-placement="top" onclick="return confirm('<?php echo PAGES_DEL_PROMPT ?>')"><i class="fa fa-trash-o"></i></a> </td>
           </tr>
           <?php }?>
           </tbody>
          </table>
  		</div>  
        <div class="panel panel-default midi-left2">
    	<div class="panel-heading"><strong><?php echo ADD_NEW_PAGE ?> </strong></div>
           <center><button class="btn btn-primary" style="margin-top:10px; margin-bottom:10px" data-toggle="modal" data-target="#myModal"><?php echo ADD_NEW_PAGE ?></button></center>
  		</div>
<div style="clear:both"></div>

     </div>
     </div><!--end of dash_cont_stat-->
     </div><!-- End of main_content-->
    </div> <!-- End of the container-->
    
   <!------////////////////////////
   <!-- Add Page Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"><?php echo ADD_NEW_PAGE ?></h4>
      </div>
      <div class="modal-body">
      <div class='alert alert-info' style="width:560px;">
          <?php echo PARAG_PROMPT ?> 
          </div>
        <form method="post" action="<?php echo ISVIPI_URL.'conf/managePages/' ?>">
        <div class="form-group">
        <input type="text" class="form-control" name="p_title" placeholder="<?php echo TITLE ?>" required>
        </div>
        <div class="form-group">
        <textarea class="form-control" name="p_content" rows="10" placeholder="<?php echo TYPE_ANNOUNCE ?>" required="required"></textarea>
        </div>
        <input type="hidden" name="page" value="new_page">
        <button class="btn btn-primary" type="submit"><?php echo PUBLISH ?></button>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo CLOSE ?></button>
      </div>
    </div>
  </div>
</div>
<?php include_once'footer.php';?>