<?php
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
include_once ISVIPI_USER_INC_BASE. 'classes/PasswordHash.php';
include_once ISVIPI_USER_INC_BASE. 'emails/emailFunc.php';
$from_url = $_SERVER['HTTP_REFERER'];

// Base-2 logarithm of the iteration count used for password stretching
$hash_cost_log2 = 8;
// Do we require the hashes to be portable to older systems (less secure)?
$hash_portable = FALSE;

$op = $_POST['op'];
if ($op !== 'new' && $op !== 'login' && $op !== 'change' && $op !== 'feed' && $op !== 'p_details' && $op !== 'forgot_pass'){
	echo UNKNOWN_REQ;
	//$_SESSION['err'] =UNKNOWN_REQ;
    //header ('location:'.$from_url.'');
	exit();
} 
if (isset($_POST['user'])){
$user = get_post_var('user');
if (empty($user)) {
	echo USERNAME.E_IS_EMPTY;
    //$_SESSION['err'] =USERNAME.E_IS_EMPTY;
    //header ('location:'.$from_url.'');
	exit();
}
// Sanity-check the username, don't rely on our use of prepared statements
// alone to prevent attacks on the SQL server via malicious usernames
if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $user)){
	echo E_INVALID_CHAR_USERNAME;
	//$_SESSION['err'] =E_INVALID_CHAR_USERNAME;
    //header ('location:'.$from_url.'');
	exit();
}
}
//And now here comes the hasher
$hasher = new PasswordHash($hash_cost_log2, $hash_portable);

/////////////////////////////////////////////////////////////
//////////////// REGISTRATION //////////////////////////////
////////////////////////////////////////////////////////////
if ($op === 'new') {
	getAdminGenSett();
	if ($usrReg == "1"){
		echo N_REG_DISABLED;
	//$_SESSION['err'] =N_REG_DISABLED;
    //header ('location:'.$from_url.'');
	exit();	
	}
if (strlen($user) < 6)
	{
		echo E_USERNAME_SHORT;
	//$_SESSION['err'] =E_USERNAME_SHORT;
    //header ('location:'.$from_url.'');
	exit();
}	
	
/* Validate Display Name */
$d_name = get_post_var('d_name');
if (empty($d_name)) {
	echo DISPLAY_NAME.E_IS_EMPTY;
	//$_SESSION['err'] =DISPLAY_NAME.E_IS_EMPTY;
    //header ('location:'.$from_url.'');
	exit();
}
$d_name = preg_replace('/\s\s+/',' ', $d_name);
if (strlen($d_name) < 6)
	{
		echo E_SHORT_DISPL_NAME;
	//$_SESSION['err'] =E_SHORT_DISPL_NAME;
    //header ('location:'.$from_url.'');
	exit();
}	
if (!preg_match('/^[a-zA-Z0-9_ ]{1,60}$/', $d_name))
	{
		echo E_INVALID_CHARS_IN.DISPLAY_NAME;
	//$_SESSION['err'] =E_INVALID_CHARS_IN.DISPLAY_NAME;
    //header ('location:'.$from_url.'');
	exit();
}
/* Validate email */
$email = get_post_var('email');
if (empty($email)) 
    {
		echo EMAIL.E_IS_EMPTY;
	//$_SESSION['err'] =EMAIL.E_IS_EMPTY;
    //header ('location:'.$from_url.'');
	exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
    {
		echo E_INVALID_CHARS_IN.EMAIL;
	//$_SESSION['err'] =E_INVALID_CHARS_IN.EMAIL;
    //header ('location:'.$from_url.'');
	exit();
}

/* Validate Password */
$pass = get_post_var('pass');
if (empty($pass)) {
    {
		echo PASSWORD.E_IS_EMPTY;
	//$_SESSION['err'] =PASSWORD.E_IS_EMPTY;
    //header ('location:'.$from_url.'');
	exit();
   }
  }
if (strlen($pass) < 6)
	{
		echo E_SHORT_PASS;
	//$_SESSION['err'] =E_SHORT_PASS;
    //header ('location:'.$from_url.'');
	exit();
}	
if (strlen($pass) > 72)
	{
		echo E_LONG_PASS;
	//$_SESSION['err'] =E_LONG_PASS;
    //header ('location:'.$from_url.'');
	exit();
}

/* Validate Password Repeat */
$pass2 = get_post_var('pass2');
if (empty($pass2)) 
    {
		echo REPEAT_PASSWORD.E_IS_EMPTY;
	//$_SESSION['err'] =REPEAT_PASSWORD.E_IS_EMPTY;
    //header ('location:'.$from_url.'');
	exit();
}
/* Check if passwords match */
if ($pass!= $pass2)
    {
		echo E_PASS_NOT_MATCH;
	//$_SESSION['err'] =E_PASS_NOT_MATCH;
    //header ('location:'.$from_url.'');
	exit();
}
	$hash = $hasher->HashPassword($pass);
if (strlen($hash) < 20)
	{
		echo E_SYS_ERR;
	//$_SESSION['err'] =E_SYS_ERR;
    //header ('location:'.$from_url.'');
	exit();
}
	unset($hasher);
	
// Validate Gender just in case someone goes around the select elements
$user_gender = get_post_var('user_gender');
if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $user_gender))
	{
		echo E_INVALID_CHARS_IN.GENDER;
	//$_SESSION['err'] =E_INVALID_CHARS_IN.GENDER;
    //header ('location:'.$from_url.'');
	exit();
}
if ((!isset($_POST['user_dob']))&& isset($_POST['month'])&& isset($_POST['day'])&& isset($_POST['year'])){
// Validate Date
$mm = get_post_var('month');
$dd = get_post_var('day');
$yyyy = get_post_var('year');
if (empty($mm)||empty($dd)||empty($yyyy)){
	echo DOB.E_IS_EMPTY;
	exit();
}
if (!is_numeric($mm)||!is_numeric($dd)||!is_numeric($yyyy)){
	echo E_INVALID_CHARS_IN.DOB;
	exit();
}
$user_dob = $mm."/".$dd."/".$yyyy;
} else if (isset($_POST['user_dob'])){
$user_dob = get_post_var('user_dob');
} else {
$user_dob = "01/01/1900";	
}
	if (!preg_match('/^[A-Za-z0-9:_.\/\\\\ ]+$/', $user_dob))
	{
		echo E_INVALID_CHARS_IN.DOB;
	//$_SESSION['err'] =E_INVALID_CHARS_IN.DOB;
    //header ('location:'.$from_url.'');
	exit();
}
if (!checkDateTime($user_dob)){
	echo E_WRONG_DATE_FORMAT;
	//$_SESSION['err'] =E_WRONG_DATE_FORMAT;
    //header ('location:'.$from_url.'');
	exit();	
}

// Validate City
$user_city = get_post_var('user_city');
if (empty($user_city)) 
    {
		echo CITY.E_IS_EMPTY;
	//$_SESSION['err'] =CITY.E_IS_EMPTY;
    //header ('location:'.$from_url.'');
	exit();
}
if (!preg_match('/^[a-zA-Z0-9_ ]{1,60}$/', $user_city))
	{
		echo E_INVALID_CHARS_IN.CITY;
	//$_SESSION['err'] =E_INVALID_CHARS_IN.CITY;
    //header ('location:'.$from_url.'');
	exit();
}
	
// Validate Country
$user_country = get_post_var('user_country');
if (empty($user_country)) 
    {
		echo COUNTRY.E_IS_EMPTY;
	//$_SESSION['err'] =COUNTRY.E_IS_EMPTY;
    //header ('location:'.$from_url.'');
	exit();
}
if (!preg_match('/^[a-zA-Z0-9_, ]{1,60}$/', $user_country))
	{
		echo E_INVALID_CHARS_IN.COUNTRY;
	//$_SESSION['err'] =E_INVALID_CHARS_IN.COUNTRY;
    //header ('location:'.$from_url.'');
	exit();
}

// Check if the username is already in the database
if(checkName($user))
	{
		echo E_USERNAME_TAKEN;
	//$_SESSION['err'] =E_USERNAME_TAKEN;
    //header ('location:'.$from_url.'');
	exit();
}else
	{
// Check if the email is already in the database
if(checkEmail($email))
	{
		{
			echo E_EMAIL_IN_USE;
		//$_SESSION['err'] =E_EMAIL_IN_USE;
		//header ('location:'.$from_url.'');
		exit();
		}
	}else
	{ 
     //Generate a random string for email validation
	 $randomstring = generateRandomString();
	 addUser($user,$d_name,$hash,$email,$randomstring,$user_gender,$user_dob,$user_city,$user_country);
	 
	 //Update timeline/activity feeds
	 //$activity = 'has joined our site';
	 //xtractUID($user);
	 //updateTimeline($uid,$user,$activity);
	 //send activation email
	 getAdminGenSett();
	 if ($usrValid=="1"){ 
	 sendActEmail($site_url,$site_email,$user,$site_title,$randomstring,$email);
	 }
	 if ($NewUserNotice == 1){
		newUserRegistered($user,$email);	 
	 }
   } 
}
if ($usrValid=="1"){
//$_SESSION['succ_reg'] =S_REG_VALID;
echo S_REG_VALID;
//$_SESSION['succ'] =S_REG_VALID;
		//header ('location:'.$from_url.'');
		exit();
} else {
//$_SESSION['succ_reg'] =S_REG_NO_VALID;
echo S_REG_NO_VALID;
//$_SESSION['succ'] =S_REG_NO_VALID;
		//header ('location:'.$from_url.'');
		exit();	
}
$db->close();
}
/////////////////////////////////////////////////////////////
//////////////// LOGIN /////////////////////////////////////
////////////////////////////////////////////////////////////

if ($op === 'login') {
	ob_start();
$email = get_post_var('email');
if (empty($email)) {
	$_SESSION['err'] =EMAIL.E_IS_EMPTY;;
    header ('location:'.$from_url.'');
	exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
    {
	$_SESSION['err'] =E_INVALID_EMAIL;
    header ('location:'.$from_url.'');
	exit();
}
$pass = get_post_var('pass');	
if (empty($pass)) {
    {
	$_SESSION['err'] =PASSWORD.E_IS_EMPTY;
    header ('location:'.$from_url.'');
	exit();
}
  }

	// Check if the email is already in the database
	$chkusrnme = $db->prepare("SELECT id,active,username FROM members WHERE email=?");
	$chkusrnme->bind_param("s",$email);
	$chkusrnme->execute();
	$chkusrnme->store_result();
		if ($chkusrnme->num_rows === 0){
			$_SESSION['err'] =N_EMAIL_NOT_FOUND;
			header ('location:'.$from_url.'');
			exit();
		}
	else
		{
			$chkusrnme->bind_result($id,$active,$user);
			$chkusrnme->fetch();
			   if ($active == 0){
				$_SESSION['err'] =N_ACCOUNT_NOT_VALID;
				header ('location:'.$from_url.'');
				exit();
		}
 	else if ($active == 3){
				$_SESSION['err'] =ACC_SUSPENDED;
				header ('location:'.$from_url.'');
				exit();
	} else {  
		// Retrieve password and try to authenticate
		$chkusrlog = $db->prepare("SELECT password FROM members WHERE email=?");
		$chkusrlog->bind_param("s",$email);
		$chkusrlog->execute();
		$chkusrlog->store_result();
		$chkusrlog->bind_result($hash);
		$chkusrlog->fetch();
			if ($hasher->CheckPassword($pass, $hash)) {
			//Regenerate a session user based on the user's username
			session_regenerate_id(true);
			$_SESSION['user_id'] = $id;
			$_SESSION['logged_in'] = TRUE;
			$_SESSION['succ'] =S_LOGIN_SUCCESS;
			
			session_write_close();
	
	  		userOnline($user);
			//Redirect to members area
			header ('location:'.ISVIPI_URL.'home/');
			exit();
		} else {
			$_SESSION['err'] =N_EMAIL_PASS_INCORRECT;
			header ('location:'.$from_url.'');
			exit();
		$op = 'fail'; 
	}
  }
}
$db->close();
}
/////////////////////////////////////////////////////////////
//////////////// USER CHANGE PASSWORD //////////////////////
////////////////////////////////////////////////////////////

if ($op === 'change') {
	$newpass = get_post_var('newpass');
	if (empty($newpass)) 
	{
		$_SESSION['err'] =NEW_PASS.E_IS_EMPTY;
		header ('location:'.$from_url.'');
		exit();
	}
	if (strlen($newpass) < 6)
	{
	$_SESSION['err'] =E_SHORT_PASS;
    header ('location:'.$from_url.'');
	exit();
}	
		$newpass2 = get_post_var('newpass2');
		if (empty($newpass2)) {
		$_SESSION['err'] =REP_NEW_PASS.E_IS_EMPTY;
		header ('location:'.$from_url.'');
		exit();
	}
		//Check if the new passwords match 
       if ($newpass!= $newpass2)
         {
			$_SESSION['err'] =E_PASS_NOT_MATCH;
			header ('location:'.$from_url.'');
			exit();
		  }
		if (strlen($newpass) > 72)
		  {
			$_SESSION['err'] =E_LONG_PASS;
			header ('location:'.$from_url.'');
			exit();
		  }
			$hash = $hasher->HashPassword($newpass);
		if (strlen($hash) < 20)
			{
				$_SESSION['err'] =E_SYS_ERR;
				header ('location:'.$from_url.'');
				exit();
			}
		$stmt = $db->prepare('update members set password=? where username=?');
		$stmt->bind_param('ss', $hash, $user);
		$stmt->execute();
			$_SESSION['succ'] =S_PASS_CHANGE_SUCCESS;
			header ('location:'.$from_url.'');
			exit();
	  $db->close();
	 }
	unset($hasher);
	
	
/////////////////////////////////////////////////////////////
//////////////// TIMELINE FEED //////////////////////
////////////////////////////////////////////////////////////
if ($op === 'feed') {
		$myfeed = get_post_var('myfeed');
		$myfeed = str_replace("  ","",$myfeed);
		if (trim($myfeed)==''){
		$_SESSION['err'] =N_EMPTY_FEED;
			header ('location:'.$from_url.'');
			exit();
	}
		$myfeed = htmlspecialchars($myfeed, ENT_QUOTES);
		if (empty($myfeed)) {
			$_SESSION['err'] =N_EMPTY_FEED;
			header ('location:'.$from_url.'');
			exit();
			}
		$myfeed = nl2br($myfeed);
		//Update the timeline
		$updtml = $db->prepare('insert into timeline (uid, username, activity, time) values (?, ?, ?, NOW())');
		$updtml->bind_param('iss', $_SESSION['user_id'],$user, $myfeed);
		$updtml->execute();
		
		//success('Update successful');
			echo S_SUCCESS;
			if (ismobile()){
				$_SESSION['succ'] =S_SUCCESS;
			header ('location:'.$from_url.'');
			}
			exit();
			$db->close();
		}
/////////////////////////////////////////////////////////////
//////////////// UPDATE PROFILE //////////////////////
////////////////////////////////////////////////////////////
if ($op === 'p_details') {
/* User ID */
$user_id_n = get_post_var('userid');
if (!is_numeric($user_id_n)){
	$_SESSION['err'] =INVALID_ID;
    header ('location:'.$from_url.'');
	exit();
}
/* Display Name */
$display_nn = get_post_var('display_name');
$display_n = preg_replace('/[^a-zA-Z0-9 ]/','',$display_nn);
/* Gender */
$gender_n = get_post_var('user_gender');
if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $gender_n)){
	$_SESSION['err'] =E_INVALID_CHARS_IN.GENDER;
    header ('location:'.$from_url.'');
	exit();
}
/* Date of Birth */
$dob_n = get_post_var('dob');
if (!checkDateTime($dob_n))
{
$_SESSION['err'] =E_WRONG_DATE_FORMAT;
    header ('location:'.$from_url.'');
	exit();	
}
if (!preg_match('/^[A-Za-z0-9:_.\/\\\\ ]+$/', $dob_n))
	{
	$_SESSION['err'] ="E_INVALID_CHARS_IN.DOB";
    header ('location:'.$from_url.'');
	exit();
}
/* Phone number */
$phone_nn = get_post_var('phone');
$phone_n = preg_replace('/[^0-9]/','',$phone_nn);

/* City */
$city_nn = get_post_var('city');
$city_n = preg_replace('/[^a-zA-Z0-9 ]/','',$city_nn);


/* Country */
$coutry_nn = get_post_var('user_country');
$coutry_n = preg_replace('/[^a-zA-Z0-9, ]/','',$coutry_nn);
	 /* Update profile*/
	 updateProfile($display_n,$user_id_n,$gender_n,$dob_n,$phone_n,$city_n,$coutry_n);
	 $_SESSION['succ'] =S_PROFILE_UPD_SUCC;
	 header("location: ".$from_url."");
	 exit ();
}

/////////////////////////////////////////////////////////////
//////////////// RESET PASSWORD //////////////////////
////////////////////////////////////////////////////////////

if ($op === 'forgot_pass') {
	$recov_email = get_post_var('recov_email');
	
	if (empty($recov_email)) 
	{
		$_SESSION['err'] =EMPTY_REC_EMAIL;
		header ('location:'.$from_url.'');
		exit();
	}
  if (!filter_var($recov_email, FILTER_VALIDATE_EMAIL)) 
    {
	$_SESSION['err'] =E_INVALID_EMAIL;
    header ('location:'.$from_url.'');
	exit();
	}
	//check if email exists
	if(!checkEmail($recov_email))
	{
		$_SESSION['err'] =E_INVALID_EMAIL;
    header ('location:'.$from_url.'');
	exit();
	}
	else {
		//Generate a random string for email validation
	 	$randomstring = generateRandomString();
		//Update members table
		updtRecov($recov_email,$randomstring);
		//Get username so that we can send password recovery email
		emailUsername($recov_email);
		//passRecovEmail();
		sendRecEmail($recov_email,$randomstring,$site_title,$site_email,$username,$site_url);
		$_SESSION['succ'] =S_PASS_RESET_LINK_SENT.$recov_email;
        header ('location:'.$from_url.'');
	    exit();
	}
}
$db->close();
?>