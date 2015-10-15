﻿<?php $pageManager->loadCustomHead('g_head','m_head'); ?>
<?php $pageManager->loadCustomHeader('g_header','m_header'); ?>
<?php $pageManager->loadsideBar('sidebar'); ?>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
        	<!-- members -->
        	<section class="col-lg-6">
				<div class="box box-solid members">
                    <div class="box-header with-border">
                      <h3 class="box-title">Browse Members</h3>
                      <div class='box-tools'>
                      	<div class="dropdown" style="display:inline-block">
                          <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <span class="fa fa-chevron-down"></span>
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li><a href="<?php echo ISVIPI_URL ."members/latest/$limit" ?>">Order by Latest</a></li>
                            <li><a href="<?php echo ISVIPI_URL ."members/oldest/$limit" ?>">Order by Oldest</a></li>
                          </ul>
                    	</div>
                        <div class="dropdown" style="display:inline-block">
                          <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <span class="fa fa-bars"></span>
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li><a href="<?php echo ISVIPI_URL ."members/$oderBY/25" ?>">25 per page</a></li>
                            <li><a href="<?php echo ISVIPI_URL ."members/$oderBY/50" ?>">50 per page</a></li>
                            <li><a href="<?php echo ISVIPI_URL ."members/$oderBY/100" ?>">100 per page</a></li>
                            <li><a href="<?php echo ISVIPI_URL ."members/$oderBY/all" ?>">All</a></li>
                          </ul>
                    	</div>
                    </div>
                      <hr style="margin:5px 0"/>
                    </div>
                	<div class="row">
                    
                    	<!--load members -->
                    	<?php if(is_array($m_info)) foreach ($m_info as $key => $mi) {
							//get friends properties (request pending, rejected and so on)
							$fr_rquest = new friends();
						?>
                    	<div class="col-md-12">
                          <!-- Widget: user widget style 1 -->
                          <div class="box box-widget widget-user-2 <?php if($fr_rquest->friendReqExists($mi['m_id']) && ($fr_to === $_SESSION['isv_user_id']) && $friendReq_status === 1){ echo "f_req_exists"; }?>">
                          <div class="showme">pending friend request</div>
                            <!-- Add the bg color to the header using any of the bg-* classes -->
                            <div class="widget-user-header bg-white">
                              <a href="<?php echo ISVIPI_URL.'profile/'.$mi['m_username'] ?>">
                              <div class="widget-user-image">
                                <img class="img-square" src="<?php echo user_pic($mi['m_profile_pic']) ?>" alt="User Avatar">
                              </div><!-- /.widget-user-image -->
                              </a>
                              <h3 class="widget-user-username" style="color:#000; font-weight:500">
                              <a href="<?php echo ISVIPI_URL.'profile/'.$mi['m_username'] ?>">
							  	<?php echo $mi['m_fullname']; ?>
                              </a>
                              </h3>
                              <h5 class="widget-user-desc" style="color:#000"><?php echo ucfirst($mi['m_gender']); ?> (
							  <?php echo age($mi['m_dob']) ?>)</h5>
                              <div class="widget-user-username" style="margin-top:-5px;">
                              
                              	  <!-- check if request exists -->
								  <?php global $fr_id,$friendReq_status,$fr_from,$fr_to; ?>
                                  <!-- if friend request exists (sender point of view) -->
                              	  <?php if($fr_rquest->friendReqExists($mi['m_id']) && ($fr_from === $_SESSION['isv_user_id'])){?>
                                  	<button type="button" class="btn bg-navy btn-xs btn-flat disabled">Request Pending</button>
                                  <!-- if friend request exists and has not been ignored (recepient point of view) -->
								  <?php } else if($fr_rquest->friendReqExists($mi['m_id']) && ($fr_to === $_SESSION['isv_user_id']) && $friendReq_status === 1) {?>
                                  <a href="<?php echo ISVIPI_URL.'p/friends/f_accept/'.$fr_id.'/'.$fr_from ?>" class="btn btn-primary btn-xs btn-flat">Accept</a>
                                  <a href="<?php echo ISVIPI_URL.'p/friends/f_ignore/'.$fr_id ?>" class="btn btn-danger btn-xs btn-flat">Ignore</a> &nbsp;
								  <!-- if friend request exists and recepient had ignored it (recepient point of view) -->
								  <?php } else if($fr_rquest->friendReqExists($mi['m_id']) && ($fr_to === $_SESSION['isv_user_id']) && $friendReq_status === 0) {?>
                                  <div class="pend_f_req bg-maroon">You ignored this request but you can still accept it at any time.</div>
                                  <a href="<?php echo ISVIPI_URL.'p/friends/f_accept/'.$fr_id ?>" class="btn btn-primary btn-xs btn-flat">Accept</a>
								  <?php } else {?>
                                  <a href="<?php echo ISVIPI_URL.'p/friends/f_req/'.$mi['m_id'] ?>" class="btn btn-primary btn-xs btn-flat">Send Friend Request</a> &nbsp;
                                  <?php } ?>
                                  <a href="<?php echo ISVIPI_URL.'profile/'.$mi['m_username'] ?>" class="btn btn-warning btn-xs btn-flat"><i class="fa fa-eye"></i> View Profile</a>
                              </div>
                            </div>
                          </div><!-- /.widget-user -->
                        </div><!-- /.col -->
                        <?php } ?>
                    
                    
                    </div>
                </div>
            <div class="clear"></div> 
			</section>
            <!--end::members -->
			
            
            
            <!-- announcements -->
            <section class="col-lg-4 announcements">
            	<div class="box box-solid">
                    <div class="box-header">
                    
                    </div>
                    
                    
                </div>
            </section>
            
            <!-- online friends -->
            <section class="col-lg-2 friends-sidebar">
            	<div class="box box-solid">
                    <div class="box-header">
                    
                    </div>
                    
                    
                </div>
            </section>
            
            <div class="clear"></div>
            </section>
        </section>
        <!-- /.content -->
        
      </div>
      <!-- /.content-wrapper -->
      
      <!-- scripts section -->
<?php $pageManager->loadCustomFooter('g_footer','m_footer'); ?>
