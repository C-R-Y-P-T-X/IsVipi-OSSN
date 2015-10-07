﻿      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

          <!-- Sidebar user panel (optional) -->
          <div class="user-panel">
            <div class="pull-left image">
            <img src="<?php echo ISVIPI_STYLE_URL . 'site/user.jpg' ?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
              <p><?php echo $memberinfo['full_name'] ?></p>
              <!-- Status -->
              <a href="#"><i class="fa fa-pencil-square-o"></i> Edit Profile</a>
            </div>
          </div>

          <!-- Sidebar Menu -->
          <ul class="sidebar-menu">
            <li <?php if ($PAGE['0'] === "home"){?> class="active" <?php } ?>>
            	<a href="<?php echo ISVIPI_URL .'home/' ?>"><i class="fa fa-feed"></i> <span>News Feed</span></a>
            </li>
            <li>
            	<a href="#"><i class="fa fa-envelope-o"></i> <span>Messages</span></a>
            </li>
            <li>
            	<a href="#"><i class="fa fa-users"></i> <span>Friends</span></a>
            </li>
            <li <?php if ($PAGE['0'] === "members"){?> class="active" <?php } ?>>
            	<a href="<?php echo ISVIPI_URL .'members/' ?>"><i class="fa fa-search"></i> <span>Browse</span></a>
            </li>
          </ul><!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
      </aside>
