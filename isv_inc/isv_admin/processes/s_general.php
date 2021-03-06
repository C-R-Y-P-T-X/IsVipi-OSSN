<?php
	/*******************************************************
	*   Copyright (C) 2014  http://isvipi.org
							
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
							
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
							
	You should have received a copy of the GNU General Public License along
	with this program; if not, write to the Free Software Foundation, Inc.,
	51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
	******************************************************/ 
	require_once(ISVIPI_ADMIN_CLS_BASE .'init.cls.php');
	$track = new admin_security();
	
	require_once(ISVIPI_CLASSES_BASE .'utilities/encrypt_decrypt.php'); 
	$converter = new Encryption;
	
	if(!$track->admin_logged_in()){
		$entry = "A non-admin tried to access isv_admin/processes/s_general.php file.";
		$ip = get_user_ip();
		log_entry($entry,$ip);
		notFound404Err();
	}

	//prohibit direct access to this file
	 if(!isset($_SERVER['HTTP_REFERER']) || empty ($_SERVER['HTTP_REFERER'])){
		//log this entry
		$entry = "Someone tried to access isv_admin/processes/s_general.php directly.";
		$ip = get_user_ip();
		log_entry($entry,$ip);
		notFound404Err();
	 }
	 
	 $from_url = $_SERVER['HTTP_REFERER'];
	 
	 /** check if our hidden field is present */
	 if (isset($_POST['aop']) && !empty($_POST['aop'])){
		 $op = cleanPOST('aop');
	 } else if(isset($PAGE[2]) && !empty($PAGE[2])){
		 $op = $converter->decode($PAGE[2]);
	 } else {
		 $_SESSION['isv_error'] = 'An error occured. Invalid alterations detected.';
		 header('location:'.$from_url.'');
		 exit();
	 }
	 
	 if ($op !== 'gen' && $op !== 'c_theme' && $op !== 'security' && $op !== 's_settings' && $op !== 'key_w' && $op !== 'm_descr' && $op !== 'logo' && $op !== 'favicon'){
		 $entry = "Someone interfered with admin member page.";
		 $ip = get_user_ip();
		 log_entry($entry,$ip);
		 
		 $_SESSION['isv_error'] = 'An error occured. Invalid entries detected.';
		 header('location:'.$from_url.'');
		 exit();
	}
	
	//require our admin.cls file
	require_once(ISVIPI_ADMIN_CLS_BASE .'settings.cls.php');
	$sett = new settings();
	
	if ($op === 'gen'){
		
		//capture our post variables and save them in an array
		$sett_arr = array(
			'url' => cleanPOST('url'),
			'Title' => cleanPOST('title'),
			'Email' => cleanPOST('email'),
			'Timezone' => cleanPOST('timezone')
		
		);
		
		//check if any is not supplied
		foreach( $sett_arr as $field => $value){
			if(!isSupplied($value)){
				 $_SESSION['isv_error'] = 'Please fill in '.$field.'!';
				 header('location:'.$from_url.'');
				 exit();
			}
		}
		
		//validate email
		if(!filter_var($sett_arr['Email'], FILTER_VALIDATE_EMAIL)){
			$_SESSION['isv_error'] = 'Please provide the correct email';
		 	header('location:'.$from_url.'');
		 	exit();
		}
		
		//update
		global $isv_db;
		$stmt = $isv_db->prepare("UPDATE s_info SET s_url=?,s_title=?,s_email=?,s_time_zone=? WHERE id=1");
		$stmt->bind_param('ssss',$sett_arr['url'],$sett_arr['Title'],$sett_arr['Email'],$sett_arr['Timezone']);
		$stmt->execute();
		$stmt->close();
			
		//return success
		$_SESSION['isv_success'] = 'Settings updated.';
		header('location:'.$from_url.'');
		exit();
		

	}
	
	if ($op === 'c_theme'){
		$theme = cleanPOST('theme');
		
		//check if a theme name has been supplied
		if(!isset($theme) || empty($theme)){
			$_SESSION['isv_error'] = 'Please enter a theme name';
		 	header('location:'.$from_url.'');
		 	exit();
		}
		
		//update
		global $isv_db;
		$stmt = $isv_db->prepare("UPDATE s_info SET s_theme = ? WHERE id=1");
		$stmt->bind_param('s',$theme);
		$stmt->execute();
		$stmt->close();
		
		//return success
		$_SESSION['isv_success'] = 'Theme changed successfully';
		header('location:'.$from_url.'');
		exit();
		
	}
	
	if ($op === 'security'){
		//capture our variables
		$admin = cleanPOST('admin');
		$ssl = cleanPOST('ssl');
		
		//check if our variables have been supplied
		if(!isset($admin) || empty($admin)){
			$_SESSION['isv_error'] = 'Please enter admin back-end name';
		 	header('location:'.$from_url.'');
		 	exit();
		}
		
		//check if our variables have been supplied
		if(!isset($ssl) || empty($ssl)){
			$ssl = 0;
		}
		
		//update ssl
		global $isv_db;
		$stmt = $isv_db->prepare("UPDATE s_info SET s_enable_ssl = ? WHERE id=1");
		$stmt->bind_param('i',$ssl);
		$stmt->execute();
		
		//update admin back end
		$stmt = $isv_db->prepare("UPDATE s_settings SET admin_end = ? WHERE id=1");
		$stmt->bind_param('s',$admin);
		$stmt->execute();
		$stmt->close();
		
		//return success
		$_SESSION['isv_success'] = 'Security settings saved successfully';
		header('location:'.ISVIPI_URL.$admin.'/general/');
		exit();
		
	}
	
	if ($op === 's_settings'){
		
		//capture our variables
		$m_mode = cleanPOST('status');
		$h_errors = cleanPOST('errors');
		$n_members = cleanPOST('reg');
		$m_validate = cleanPOST('validate');
		$s_timezone = cleanPOST('timezone');
		$cron = cleanPOST('cronjob');
		$admin_notify = cleanPOST('notify_admin');
		
		//check if they have been supplied
		if(!isset($m_mode) || empty($m_mode)){
			$m_mode = 0;
		}
		
		if(!isset($h_errors) || empty($h_errors)){
			$h_errors = 0;
		} 
		
		if(!isset($n_members) || empty($n_members)){
			$n_members = 0;
		}
		if(!isset($m_validate) || empty($m_validate)){
			$m_validate = 0;
		}
		if(!isset($s_timezone) || empty($s_timezone)){
			$s_timezone = 0;
		}
		if(!isset($cron) || empty($cron)){
			$cron = 0;
		}
		if(!isset($admin_notify) || empty($admin_notify)){
			$admin_notify = 0;
		}
		
		//update
		$stmt = $isv_db->prepare("UPDATE s_info SET s_status=? WHERE id=1");
		$stmt->bind_param('i',$m_mode);
		$stmt->execute();
		$stmt->close();
		
		$stmt = $isv_db->prepare("UPDATE s_settings SET user_reg=?,user_validate=?,sys_cron=?,timezone=?,errors=?,newuser_notice=? WHERE id=1");
		$stmt->bind_param('iiiiii',$n_members,$m_validate,$cron,$s_timezone,$h_errors,$admin_notify);
		$stmt->execute();
		$stmt->close();
		
		//return success
		$_SESSION['isv_success'] = 'Site status settings saved successfully';
		header('location:'.$from_url.'');
		exit();
	}
	
	if ($op === 'key_w'){
		
		//capture our variable
		$keywords = cleanPOST('keywords');
		
		//check if our variable has been supplied		
		if(!isset($keywords) || empty($keywords)){
			$_SESSION['isv_error'] = 'Please fill in the meta keywords';
		 	header('location:'.$from_url.'');
		 	exit();
			
		}
		
		//remove unnecessary line breaks and white spaces
		$keywords = str_replace("  ","",$keywords);
		$keywords = nl2br($keywords);
		
		//save
		$stmt = $isv_db->prepare("UPDATE s_meta SET tags=? WHERE id=1");
		$stmt->bind_param('s',$keywords);
		$stmt->execute();
		$stmt->close();
		
		//return success
		$_SESSION['isv_success'] = 'Meta keywords saved successfully';
		header('location:'.$from_url.'');
		exit();
		
	}
	
	if ($op === 'm_descr'){
		
		//capture our variable
		$descr = cleanPOST('description');
		
		//check if our variable has been supplied		
		if(!isset($descr) || empty($descr)){
			$_SESSION['isv_error'] = 'Please fill in the meta description';
		 	header('location:'.$from_url.'');
		 	exit();
			
		}
		
		//remove unnecessary line breaks and white spaces
		$descr = str_replace("  ","",$descr);
		$descr = nl2br($descr);
		
		//save
		$stmt = $isv_db->prepare("UPDATE s_meta SET description=? WHERE id=1");
		$stmt->bind_param('s',$descr);
		$stmt->execute();
		$stmt->close();
		
		//return success
		$_SESSION['isv_success'] = 'Meta description saved successfully';
		header('location:'.$from_url.'');
		exit();
		
	}
	
	if ($op === 'logo'){
		if (!is_uploaded_file($_FILES['logo_img']['tmp_name'])) {
		   	 $_SESSION['isv_error'] = 'Please select a logo to upload';
		 	 header('location:'.$from_url.'');
		 	 exit();
		}
		
		$logo = $_FILES['logo_img'];
		
		$allowed_size = '1000000' /* 1MB */;
		$new_logo_name = str_replace(' ', '', microtime());
		$new_logo_name = str_replace('.', '', $new_logo_name);
		
		$save_path = ISVIPI_STYLE_BASE .'site/imgs/';
		
		//check file size
		if ($logo["size"] > $allowed_size) {
				$_SESSION['isv_error'] = 'This image is too big. Maximum allowed file size is 1MB.';
		 	 	header('location:'.$from_url.'');
		 	 	exit();
		}
		
		//check file type
		if($logo["type"] != "image/jpg" && 
			$logo["type"] != "image/png" && 
			$logo["type"] != "image/jpeg" && 
			$logo["type"] != "image/gif" ) {
				$_SESSION['isv_error'] = 'Wrong file format. Allowed file formats are jpg, jpeg, png and gif';
		 	 	header('location:'.$from_url.'');
		 	 	exit();
		}
		
		//require file upload class
		require_once(ISVIPI_CLASSES_BASE .'utilities/class.upload.php');
			
		$newUpload = new Upload($logo);
		
		$newUpload->file_new_name_body = $new_logo_name;
		$newUpload->image_resize = true;
		$newUpload->image_convert = 'png';
		$newUpload->image_x = 150;
		$newUpload->image_ratio_y = true;
		$newUpload->Process($save_path);
			
		if (!$newUpload->processed) {
			 $_SESSION['isv_error'] = 'Something went wrong. Please try again.';
		 	 header('location:'.$from_url.'');
		 	 exit();
	    }
		$newUpload->Clean(); 
		
		//update our db
		$new_n = $new_logo_name.'.png';
		$stmt = $isv_db->prepare("UPDATE s_settings SET logo_name=? WHERE id=1");
		$stmt->bind_param('s',$new_n);
		$stmt->execute();
		$stmt->close();
		
		//return success
		$_SESSION['isv_success'] = 'Logo changed successfully';
		header('location:'.$from_url.'');
		exit();
		
	}
	
	if ($op === 'favicon'){
		if (!is_uploaded_file($_FILES['fav_icon']['tmp_name'])) {
		   	 $_SESSION['isv_error'] = 'Please select a logo to upload';
		 	 header('location:'.$from_url.'');
		 	 exit();
		}
		
		$image_mime = image_type_to_mime_type(exif_imagetype($_FILES['fav_icon']['tmp_name']));

		$favicon = $_FILES['fav_icon'];
		
		$allowed_size = '1000000' /* 1MB */;
		$new_favicon_name = str_replace(' ', '', microtime());
		$new_favicon_name = str_replace('.', '', $new_favicon_name);
		
		$save_path = ISVIPI_STYLE_BASE .'site/imgs/';
		
		//check file size
		if ($favicon["size"] > $allowed_size) {
				$_SESSION['isv_error'] = 'This image is too big. Maximum allowed file size is 1MB.';
		 	 	header('location:'.$from_url.'');
		 	 	exit();
		}
		
		//check file type
		if($image_mime != "image/jpg" && 
			$image_mime != "image/png" && 
			$image_mime != "image/jpeg" && 
			$image_mime != "image/vnd.microsoft.icon" && 
			$image_mime != "image/gif" ) {
				$_SESSION['isv_error'] = 'Wrong file format. Allowed file formats are jpg, jpeg, png, gif and ico';
		 	 	header('location:'.$from_url.'');
		 	 	exit();
		}
		
		//require file upload class
		require_once(ISVIPI_CLASSES_BASE .'utilities/class.upload.php');
			
		$newUpload = new Upload($favicon);
		
		$newUpload->file_new_name_body = $new_favicon_name;
		$newUpload->image_resize = true;
		$newUpload->image_convert = 'ico';
		$newUpload->image_x = 64;
		$newUpload->image_ratio_y = true;
		$newUpload->Process($save_path);
			
		if (!$newUpload->processed) {
			 $_SESSION['isv_error'] = 'Something went wrong. Please try again.';
		 	 header('location:'.$from_url.'');
		 	 exit();
	    }
		$newUpload->Clean(); 
		
		//update our db
		$new_n = $new_favicon_name.'.ico';
		$stmt = $isv_db->prepare("UPDATE s_settings SET favicon=? WHERE id=1");
		$stmt->bind_param('s',$new_n);
		$stmt->execute();
		$stmt->close();
		
		//return success
		$_SESSION['isv_success'] = 'Favicon changed successfully';
		header('location:'.$from_url.'');
		exit();
		
	}