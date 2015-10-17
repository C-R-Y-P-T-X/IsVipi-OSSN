﻿<body class="hold-transition skin-blue sidebar-mini" style="margin-top:-20px;">
<div class="wrapper">
      <!-- Main Header -->
      <header class="main-header">
        <!-- Logo -->
        <a href="<?php echo ISVIPI_URL .'home/' ?>" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><?php echo $isv_siteDetails['s_title'] ?></span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><img src="<?php echo ISVIPI_STYLE_URL . 'site/imgs/'.$isv_siteSettings['logo'] ?>" alt="logo" height="47"></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-fixed-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <form action="#" method="get" class="search-form">
            <div class="input-group">
              <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" class="btn search-btn"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </form>
          <!-- /.search form -->
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav" id="notifications">
            	<!-- load header notices (friend requests,messages,global notifications) -->
                <?php require_once(ISVIPI_ACT_THEME .'pages/notifications.php') ?>
                <script>
                	load_user_notices();
                </script>
            </ul>
          </div>
        </nav>
      </header>
      
      <!-- our site notifications -->
      <?php if (isset($_SESSION['isv_error']) && !empty($_SESSION['isv_error'])){?>
      <div class="alert alert-danger alert-dismissable" id="global-alert">
      	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
   		<?php echo $_SESSION['isv_error']; unset($_SESSION['isv_error']); ?>
     </div>
     <?php } else if(isset($_SESSION['isv_success']) && !empty($_SESSION['isv_success'])){?>
     <div class="alert alert-success alert-dismissable" id="global-alert">
      	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
   		<?php echo $_SESSION['isv_success']; unset($_SESSION['isv_success']); ?>
     </div>
     <?php } ?>