                  <!--========HEADER=====---->
                    <?php include ISVIPI_THEMES_BASE.$theme.'/global/header.php';?>
                  <!--========/HEADER=====---->
                  <?php
//We check if the users ID is defined
if(isset($_GET['id']))
{
	$id = intval($_GET['id']);
	//We check if the user exists
	$dn = mysql_query('select * from users where id="'.$id.'"');
	if(mysql_num_rows($dn)>0)
	{
		$dnn = mysql_fetch_array($dn);
		//We display the user datas
?>
        
                  <!--========SIDEBAR MENU=====---->
                    <?php include ISVIPI_THEMES_BASE.$theme.'/global/sidebar_menu.php';?>
                  <!--========/SIDEBAR MENU=====---->
                  
                  <!--========MY PROFILE=====---->
                  <div class="col-md-6">
                  <div class="row">
                   <div class="panel panel-default tabless-panel timeline-layout">
                     <div class="panel-heading">
                      <div class="text-muted isvipi-title"><?php echo htmlentities($dnn['username']); ?>'s Profile
                       
                      </div>
                    </div>
                    <div class="isvipi-panel-content tabless-panel-content collapse in">
                  <div class="profile_image"><img src="<?=$getuser[0]['thumb_path'];?>" height="100%" width="100%" alt="" /></div>
                  <div class="profile_info">
                  <div class="isvipi-panel-content">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td><b>Username</b></td>
                                                <td><b>:</b></td>
                                                <td><?php echo htmlentities($dnn['username']); ?></td>
                                            </tr>
                                            <tr>
                                                <td><b>Email</b></td>
                                                <td><b>:</b></td>
                                                <td><?php echo htmlentities($dnn['email']); ?></td>
                                            </tr>
                                            <tr>
                                                <td><b>Phone No.</b></td>
                                                <td><b>:</b></td>
                                                <td>+<?php echo htmlentities($dnn['dialing_code']); ?>-<?php echo htmlentities($dnn['phone']); ?></td>
                                            </tr>
                                            <tr>
                                                <td><b>Location</b></td>
                                                <td><b>:</b></td>
                                                <td><?php echo htmlentities($dnn['city']); ?>, <?php echo htmlentities($dnn['country']); ?></td>
                                            </tr>
                                            <tr>
                                                <td><b>User Type</b></td>
                                                <td><b>:</b></td>
                                                <td>Member</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><!--end of isvipi-panel-content-->
                                <div class="profile_options_bar">
                                 <div class="upload_pic"></i></a><a href="" title="Report Picture"><i class="fa fa-exclamation-triangle"></i></a></div>
                                <a href="#" title="Send a Message" data-toggle="modal" data-target="#composeMessage"><i class="fa fa-envelope-o"></i></a><a href="" title="Send Friend Request"><i class="fa fa-user"></i></a><a href="" title="Report this person"><i class="fa fa-exclamation-triangle"></i></a>
                                </div>
                                <!-- Button trigger modal -->
          
                             </div><!--end of profile_info-->
              </div><!--End of isvipi-panel-content-->
          </div><!--End of panel-->
<?php
//We add a link to send a pm to the user
if(isset($_SESSION['username']))
{
?>
<!-------------------------
---------------------------
---------------------------
------SEND PM MODAL--------
---------------------------
-------------------------->
    
              <!-- Modal -->
          <div class="modal fade" id="composeMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
           <div class="modal-dialog">
            <div class="modal-content">
             <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               <h4 class="modal-title" id="myModalLabel">Send <b><?php echo htmlentities($dnn['username']); ?></b> Message</h4>
            </div>
            <div class="modal-body">
            <div class="form-group">
            <form name="addphoto" method="post" action="new_pm.php">
            <label for="title">To</label>
            <input type="text" class="form-control" value="<?php echo htmlentities($dnn['username']); ?>" name="recip">
            <label for="title">Subject</label>
            <input type="text" class="form-control" value="" name="title">
            <label for="title">Message</label>
            <textarea name="message" class="form-control" id="inputField" tabindex="1"rows="2" cols="40"></textarea>
             </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" name="submit"/>Send Message</button>
            </form>
            </div>
          </div><!-- /.modal-content -->
       </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php
}
	}
	else
	{
		echo 'This user dont exists.';
	}
}
else
{
	echo 'The user ID is not defined.';
}
?>

              <!--========/MY PROFILE=====---->
                        
                  <!--========ANNOUNCEMENTS=====---->
                    <?php include ISVIPI_THEMES_BASE.$theme.'/global/announcements.php';?> 
                  <!--========/ANNOUNCEMENTS=====---->
                  <!--========FOOTER=====---->
                    <?php include ISVIPI_THEMES_BASE.$theme.'/global/footer.php';?> 
                  <!--========/FOOTER=====---->
