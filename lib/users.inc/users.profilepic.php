<?php
session_start();
/*******************************************************
 *   Copyright (C) 2014  http://isvipi.com

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
include_once '../../init.php'; 
include_once ISVIPI_DB_BASE.'db.php';
include_once ISVIPI_USER_INC_BASE. 'users.func.php';
checkLogin();
$user = $_SESSION['user_id'];
$from_url = $_SERVER['HTTP_REFERER'];

$op = $_POST['op'];
if ($op !== 'newpic' && $op !== 'deletepic')
	{
    $_SESSION['err'] ="Unknown request";
	$user_id = $_SESSION['user_id'];
    header ('location:../../members/profile.php?id='.$user_id.'');
	exit();
	}

/////////////////////////////////////////////////////////////
//////////////// UPLOAD PROFILE PIC //////////////////////
////////////////////////////////////////////////////////////

if ($op === 'newpic') {
function uploadFile ($file_field = null, $check_image = false, $random_name = false) {
    if(isset($_POST['name'])){ $name = $_POST['name']; } 
   
  //Config Section    
  //Set file upload path
  $path = ISVIPI_MEMBER_BASE.'/pics/'; //with trailing slash
  //Set max file size in bytes
  $max_size = 1000000;
  //Set default file extension whitelist
  $whitelist_ext = array('jpg','png','gif');
  //Set default file type whitelist
  $whitelist_type = array('image/jpeg', 'image/png','image/gif');

  //The Validation
  // Create an array to hold any output
  $out = array('error'=>null);
               
  if (!$file_field) {
    {
    $_SESSION['err'] ="Please specify a valid form field";
	$user_id = $_SESSION['user_id'];
    header ('location:../../members/profile.php?id='.$user_id.'');
	exit();
	}           
  }

  if (!$path) {
    {
    $_SESSION['err'] ="Please specify a valid upload path";
	$user_id = $_SESSION['user_id'];
    header ('location:../../members/profile.php?id='.$user_id.'');
	exit();
	}               
  }
       
  //Make sure that there is a file
  if((!empty($_FILES[$file_field])) && ($_FILES[$file_field]['error'] == 0)) {
         
    // Get filename
    $file_info = pathinfo($_FILES[$file_field]['name']);
    $name = $file_info['filename'];
    $ext = $file_info['extension'];
               
    //Check file has the right extension           
    if (!in_array($ext, $whitelist_ext)) 
	{
    $_SESSION['err'] ="Wrong file extension";
	$user_id = $_SESSION['user_id'];
    header ('location:../../members/profile.php?id='.$user_id.'');
	exit();
	}
               
    //Check that the file is of the right type
    if (!in_array($_FILES[$file_field]["type"], $whitelist_type)) {
    $_SESSION['err'] ="Invalid file type";
	$user_id = $_SESSION['user_id'];
    header ('location:../../members/profile.php?id='.$user_id.'');
	exit();
	}
               
    //Check that the file is not too big
    if ($_FILES[$file_field]["size"] > $max_size) 
	{
    $_SESSION['err'] ="The file is too big";
	$user_id = $_SESSION['user_id'];
    header ('location:../../members/profile.php?id='.$user_id.'');
	exit();
	}
               
    //If $check image is set as true
    if ($check_image) {
      if (!getimagesize($_FILES[$file_field]['tmp_name'])) 
	  {
    $_SESSION['err'] ="Uploaded image is not a valid image";
	$user_id = $_SESSION['user_id'];
    header ('location:../../members/profile.php?id='.$user_id.'');
	exit();
	}
    }

    //Create full filename including path
    if ($random_name) {
      // Generate random filename
      $tmp = str_replace(array('.',' '), array('',''), microtime());
                       
      if (!$tmp || $tmp == '') 
	  {
    $_SESSION['err'] ="The file must have a name";
	$user_id = $_SESSION['user_id'];
    header ('location:../../members/profile.php?id='.$user_id.'');
	exit();
	}
      $newname = $tmp.'.'.$ext;                                
    } else {
        $newname = $name.'.'.$ext;
    }
               
    //Check if file already exists on server
    if (file_exists($path.$newname)) 
	{
    $_SESSION['err'] ="A file with this name already exists";
	$user_id = $_SESSION['user_id'];
    header ('location:../../members/profile.php?id='.$user_id.'');
	exit();
	}

    if (count($_SESSION['err'])>0) 
	{
	$_SESSION['err'] ="The file has not been correctly validated";
	$user_id = $_SESSION['user_id'];
    header ('location:../../members/profile.php?id='.$user_id.'');
	exit();
    } 

    if (move_uploaded_file($_FILES[$file_field]['tmp_name'], $path.$newname)) {
      //Success
      $out['filepath'] = $path;
      $out['filename'] = $newname;
	  global $db;
	  $user_id = get_post_var('userid');
	  $name = $newname;
	//Update thumbnail name to member_sett table
	$upoprf = $db->prepare('UPDATE member_sett set thumbnail=? where user_id=?');
		$upoprf->bind_param('si', $name,$user_id);
		$upoprf->execute();
     return $out;
    } else $_SESSION['err'] ="System error. Please try again";
	$user_id = $_SESSION['user_id'];
    header ('location:../../members/profile.php?id='.$user_id.'');
	exit();
         
  } else $_SESSION['err'] ="No file has been uploaded";
	$user_id = $_SESSION['user_id'];
    header ('location:../../members/profile.php?id='.$user_id.'');
	exit();
}
}

if (isset($_POST['submit'])) {
  $file = uploadFile('file', true, true);
  
    $_SESSION['succ'] ="File uploaded successfully";
	$user_id = $_SESSION['user_id'];
    header ('location:../../members/profile.php?id='.$user_id.'');
	exit();
  }
$db->close();
?>