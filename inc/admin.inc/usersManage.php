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
include_once ISVIPI_ADMIN_INC_BASE. 'adminFunc.php';
$from_url = $_SERVER['HTTP_REFERER'];
if (isset($ACTION[2])){
	$adm = $ACTION[2];
	}
if (isset($_POST['adm_users'])){$adm = $_POST['adm_users'];}
if ($adm !== 'new' && $adm !== '1'/*Validate*/ && $adm !== '2'/*Suspend*/ && $adm !== '3'/*Unsuspend*/ && $adm !== '4'/*Delete*/ && $adm !== 's_All'/*Suspend All*/ && $adm !== 'uns_All'/*Unsuspend All*/ && $adm !== 'del_unv_All'/*Delete All Unvalidated*/ && $adm !== 'del_sus_All'/*Delete All Suspended*/ && $adm !== 'edit_user'/*Delete All Suspended*/ && $adm !== 'as_user'/*Delete All Suspended*/ ){
	$_SESSION['err'] =UNKNOWN_REQ;
    header ('location:'.$from_url.'');
	exit();
} 
/////////////////////////////////////////////////////////////
//////////////// ADD NEW USER //////////////////////////////
////////////////////////////////////////////////////////////
if ($adm == 'new') {
	include_once ISVIPI_USER_INC_BASE. 'classes/PasswordHash.php';
	$hash_cost_log2 = 8;
	$hash_portable = FALSE;
	$hasher = new PasswordHash($hash_cost_log2, $hash_portable);
	
	$user = get_post_var('user');
if (empty($user)) {
    $_SESSION['err'] =USERNAME.E_IS_EMPTY;
    header ('location:'.$from_url.'');
	exit();
}

if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $user)){
	$_SESSION['err'] =E_INVALID_CHAR_USERNAME;
    header ('location:'.$from_url.'');
	exit();
}

	$d_name = get_post_var('d_name');
if (empty($d_name)) {
	$_SESSION['err'] =DISPLAY_NAME.E_IS_EMPTY;
    header ('location:'.$from_url.'');
	exit();
}
if (!preg_match('/^[a-zA-Z0-9_ ]{1,60}$/', $d_name))
	{
	$_SESSION['err'] =E_INVALID_CHARS_IN.DISPLAY_NAME;
    header ('location:'.$from_url.'');
	exit();
}
/* Validate email */
$email = get_post_var('email');
if (empty($email)) 
    {
	$_SESSION['err'] =EMAIL.E_IS_EMPTY;
    header ('location:'.$from_url.'');
	exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
    {
	$_SESSION['err'] =E_INVALID_CHARS_IN.EMAIL;
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
if (strlen($pass) < 6)
	{
	$_SESSION['err'] =E_SHORT_PASS;
    header ('location:'.$from_url.'');
	exit();
}	
if (strlen($pass) > 72)
	{
	$_SESSION['err'] =E_LONG_PASS;
    header ('location:'.$from_url.'');
	exit();
}

/* Validate Password Repeat */
$pass2 = get_post_var('pass2');
if (empty($pass2)) 
    {
	$_SESSION['err'] =REPEAT_PASSWORD.E_IS_EMPTY;
    header ('location:'.$from_url.'');
	exit();
}
/* Check if passwords match */
if ($pass!= $pass2)
    {
	$_SESSION['err'] =E_PASS_NOT_MATCH;
    header ('location:'.$from_url.'');
	exit();
}
	$hash = $hasher->HashPassword($pass);
if (strlen($hash) < 20)
	{
	$_SESSION['err'] =E_SYS_ERR;
    header ('location:'.$from_url.'');
	exit();
}
	unset($hasher);
	
// Validate Gender just in case someone goes around the select elements
$user_gender = get_post_var('user_gender');
if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $user_gender))
	{
	$_SESSION['err'] =E_INVALID_CHARS_IN.GENDER;
    header ('location:'.$from_url.'');
	exit();
}
	
// Validate Date
$user_dob = get_post_var('user_dob');
	if (!checkDateTime($user_dob))
	{
	$_SESSION['err'] =E_WRONG_DATE_FORMAT;
		header ('location:'.$from_url.'');
		exit();	
	}
	if (!preg_match('/^[A-Za-z0-9:_.\/\\\\ ]+$/', $user_dob))
	{
	$_SESSION['err'] =E_INVALID_CHARS_IN.DOB;
    header ('location:'.$from_url.'');
	exit();
}

// Validate City
$user_city = get_post_var('user_city');
if (empty($user_city)) 
    {
	$_SESSION['err'] =CITY.E_IS_EMPTY;
    header ('location:'.$from_url.'');
	exit();
}
if (!preg_match('/^[a-zA-Z0-9_ ]{1,60}$/', $user_city))
	{
	$_SESSION['err'] =E_INVALID_CHARS_IN.CITY;
    header ('location:'.$from_url.'');
	exit();
}
	
// Validate Country
$user_country = get_post_var('user_country');
if (empty($user_country)) 
    {
	$_SESSION['err'] =COUNTRY.E_IS_EMPTY;
    header ('location:'.$from_url.'');
	exit();
}
if (!preg_match('/^[a-zA-Z0-9_ ]{1,60}$/', $user_country))
	{
	$_SESSION['err'] =E_INVALID_CHARS_IN.COUNTRY;
    header ('location:'.$from_url.'');
	exit();
}

$user_status = get_post_var('user_status');
if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $user_gender))
	{
	$_SESSION['err'] =E_INVALID_CHARS_IN.GENDER;
    header ('location:'.$from_url.'');
	exit();
}
if (isset($_POST['actEmailcheck'])){
$sendActEmail = get_post_var('actEmailcheck');
}
// Check if the username is already in the database
if(checkName($user))
	{
	$_SESSION['err'] =E_USERNAME_TAKEN;
    header ('location:'.$from_url.'');
	exit();
}else
	{
// Check if the email is already in the database
if(checkEmail($email))
		{
		$_SESSION['err'] =E_EMAIL_IN_USE;
		header ('location:'.$from_url.'');
		exit();
	}
	//Generate a random string for email validation
	 $randomstring = generateRandomString();
	 $time = date("Y-m-d H-i-s");
	 $stmt = $db->prepare('insert into members (username, password, email, a_code, active, reg_date, level, online, last_activity) values (?, ?, ?, ?, ?, NOW(), "1", "0", ?)');
	$stmt->bind_param('ssssis', $user, $hash, $email, $randomstring, $user_status,$time);
	$stmt->execute();
	//Extract the ID of the user that has just signed up
	$xtrctid = $db->prepare("SELECT id FROM members WHERE username=?");
	$xtrctid->bind_param("s",$user);
	$xtrctid->execute();
	$xtrctid->store_result();
	$xtrctid->bind_result($user_id);
	$xtrctid->fetch();
	$xtrctid->close();
	
	//Create user in member_sett table
	$stmt = $db->prepare('insert into member_sett (user_id,d_name,gender,dob,city,country) values (?,?,?,?,?,?)');
	$stmt->bind_param('isssss', $user_id, $d_name,$user_gender,$user_dob,$user_city,$user_country);
	$stmt->execute();
	$stmt->close();
	 
	 if (isset($sendActEmail)){
	 include_once ISVIPI_USER_INC_BASE. 'emails/emailFunc.php';
	 sendActEmail($site_url,$site_email,$user,$site_title,$randomstring,$email);
	 }
	 $_SESSION['succ'] =S_SUCCESS;
    	header ('location:'.ISVIPI_URL.'admin/members');
	exit();
	}
}

/////////////////////////////////////////////////////////////
//////////////// VALIDATE USER /////////////////////////////
////////////////////////////////////////////////////////////
if ($adm == '1') {
	$userid = $ACTION[3];
	
	$act = "1";
	$activate = $db->prepare('UPDATE members set active=? WHERE id=?');
	$activate->bind_param('ii', $act,$userid);
	$activate->execute();
	$activate->close();	
		$_SESSION['succ'] =S_SUCCESS;
		header('location: '.$from_url.'');
		exit();
}

/////////////////////////////////////////////////////////////
//////////////// SUSPEND USER ////////////////////////////
////////////////////////////////////////////////////////////
if ($adm == '2') {
	$userid = $ACTION[3];
	
	$sus = "3";
	$suspend = $db->prepare('UPDATE members set active=? WHERE id=?');
	$suspend->bind_param('ii', $sus,$userid);
	$suspend->execute();
	$suspend->close();	
		$_SESSION['succ'] =S_SUCCESS;
		header('location: '.$from_url.'');
		exit();
}


/////////////////////////////////////////////////////////////
//////////////// UNSUSPEND USER ////////////////////////////
////////////////////////////////////////////////////////////
if ($adm == '3') {
	$userid = $ACTION[3];
	
	$unsus = "1";
	$unsuspend = $db->prepare('UPDATE members set active=? WHERE id=?');
	$unsuspend->bind_param('ii', $unsus,$userid);
	$unsuspend->execute();
	$unsuspend->close();	
		$_SESSION['succ'] =S_SUCCESS;
		header('location: '.$from_url.'');
		exit();
}

/////////////////////////////////////////////////////////////
//////////////// DELETE USER ///////////////////////////////
////////////////////////////////////////////////////////////
if ($adm == '4') {
	$userid = $ACTION[3];
	
	$delete = $db->prepare('DELETE from members WHERE id=?');
	$delete->bind_param('i', $userid);
	$delete->execute();
	$delete = $db->prepare('DELETE from member_sett WHERE user_id=?');
	$delete->bind_param('i', $userid);
	$delete->execute();
	$delete = $db->prepare('DELETE from my_friends WHERE (user1=? or user2=?)');
	$delete->bind_param('ii', $userid,$userid);
	$delete->execute();
	$delete->close();	
		$_SESSION['succ'] =S_SUCCESS;
		header('location: '.$from_url.'');
		exit();
}

/////////////////////////////////////////////////////////////
//////////////// SUSPEND ALL ///////////////////////////////
////////////////////////////////////////////////////////////
if ($adm == 's_All') {
	$sus = "3";
	$suspendAll = $db->prepare('UPDATE members set active=?');
	$suspendAll->bind_param('i', $sus);
	$suspendAll->execute();
	$suspendAll->close();	
		$_SESSION['succ'] =S_SUCCESS;
		header('location: '.$from_url.'');
		exit();
}

/////////////////////////////////////////////////////////////
//////////////// UNSUSPEND ALL /////////////////////////////
////////////////////////////////////////////////////////////
if ($adm == 'uns_All') {
	$sus = "1";
	$active = "3";
	$unsuspendAll = $db->prepare('UPDATE members set active=? WHERE active=?');
	$unsuspendAll->bind_param('ii', $sus,$active);
	$unsuspendAll->execute();
	$unsuspendAll->close();	
		$_SESSION['succ'] =S_SUCCESS;
		header('location: '.$from_url.'');
		exit();
}

/////////////////////////////////////////////////////////////
//////////////// DELETE ALL UNVALIDATED /////////////////////
////////////////////////////////////////////////////////////
if ($adm == 'del_unv_All') {
	$active = "0";
	$selectAll = $db->prepare('SELECT id FROM members WHERE active=?');
	$selectAll->bind_param('i', $active);
	$selectAll->execute();
	$selectAll->store_result();
	$selectAll->bind_result($id);
	while ($selectAll->fetch()){ 
	
	$deleteAll = $db->prepare('DELETE FROM members WHERE id=?');
	$deleteAll->bind_param('i', $id);
	$deleteAll->execute();
	$deleteAll->close();	
	
	$deleteAll = $db->prepare('DELETE FROM member_sett WHERE user_id=?');
	$deleteAll->bind_param('i', $id);
	$deleteAll->execute();
	$deleteAll->close();
	}
		$_SESSION['succ'] =S_SUCCESS;
		header('location: '.$from_url.'');
		exit();
}

/////////////////////////////////////////////////////////////
//////////////// DELETE ALL SUSPENDED //////////////////////
////////////////////////////////////////////////////////////
if ($adm == 'del_sus_All') {
	$active = "3";
	$selectAll = $db->prepare('SELECT id FROM members WHERE active=?');
	$selectAll->bind_param('i', $active);
	$selectAll->execute();
	$selectAll->store_result();
	$selectAll->bind_result($id);
	while ($selectAll->fetch()){ 
	
	$deleteAll = $db->prepare('DELETE FROM members WHERE id=?');
	$deleteAll->bind_param('i', $id);
	$deleteAll->execute();
	$deleteAll->close();	
	
	$deleteAll = $db->prepare('DELETE FROM member_sett WHERE user_id=?');
	$deleteAll->bind_param('i', $id);
	$deleteAll->execute();
	$deleteAll->close();
	}
		$_SESSION['succ'] =S_SUCCESS;
		header('location: '.$from_url.'');
		exit();
}

/////////////////////////////////////////////////////////////
//////////////// EDIT USER /////////////////////////////////
////////////////////////////////////////////////////////////
if ($adm == 'edit_user') {
/* User ID */
$user_id_n = get_post_var('userid');
if (!is_numeric($user_id_n)){
	$_SESSION['err'] =INVALID_ID;
    header ('location:'.$from_url.'');
	exit();
}
/* Display Name */
$display_nn = get_post_var('d_name');
$display_n = preg_replace('/[^a-zA-Z0-9 ]/','',$display_nn);
/* Validate email */
$email = get_post_var('email');
if (empty($email)) 
    {
	$_SESSION['err'] =EMAIL.E_IS_EMPTY;
    header ('location:'.$from_url.'');
	exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
    {
	$_SESSION['err'] =E_INVALID_CHARS_IN.EMAIL;
    header ('location:'.$from_url.'');
	exit();
}
/* Gender */
$gender_n = get_post_var('user_gender');
if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $gender_n)){
	$_SESSION['err'] =E_INVALID_CHARS_IN.GENDER;
    header ('location:'.$from_url.'');
	exit();
}
/* Date of Birth */
$dob_n = get_post_var('dob');
if (!preg_match('/^[A-Za-z0-9:_.\/\\\\ ]+$/', $dob_n))
	{
	$_SESSION['err'] =E_INVALID_CHARS_IN.DOB;
    header ('location:'.$from_url.'');
	exit();
}
if (!checkDateTime($dob_n))
{
$_SESSION['err'] =E_WRONG_DATE_FORMAT;
    header ('location:'.$from_url.'');
	exit();	
}
/* Phone number */
$phone_nn = get_post_var('phone');
$phone_n = preg_replace('/[^0-9]/','',$phone_nn);

/* City */
$city_nn = get_post_var('user_city');
$city_n = preg_replace('/[^a-zA-Z0-9 ]/','',$city_nn);


/* Country */
$coutry_nn = get_post_var('user_country');
$coutry_n = preg_replace('/[^a-zA-Z0-9 ]/','',$coutry_nn);

	 /* Update profile*/
	 updateProfile($display_n,$user_id_n,$gender_n,$dob_n,$phone_n,$city_n,$coutry_n);
	 	//check if the email is not yet registered and if it is, whether it belongs to the user
		global $db;
		$checkmail = $db->prepare('SELECT id FROM members where email=?');
		$checkmail->bind_param('s', $email);
		$checkmail->execute();
		$checkmail->store_result();
		$checkmail->bind_result($user_ID);
		$checkmail->fetch();
		$isRegistered = $checkmail->num_rows();
//echo $user_ID;
//echo $user_id_n;
		if (($isRegistered > 0 && $user_ID == $user_id_n)||($isRegistered == 0)){
			$addEmail = $db->prepare('UPDATE members set email=? where id=?');
			$addEmail->bind_param('si', $email,$user_id_n);
			$addEmail->execute();
			$addEmail->close();	
			
			$_SESSION['succ'] =S_SUCCESS;
			header ('location:'.$from_url.'');
			exit();
			
		} else {
			$_SESSION['err'] =E_EMAIL_IN_USE;
			header ('location:'.$from_url.'');
			exit();
		}
}
/////////////////////////////////////////////////////////////
//////////////// LOGIN AS USER /////////////////////////////
////////////////////////////////////////////////////////////
if ($adm == 'as_user') {
	$userid = $ACTION[3];
	unset($_SESSION['user_id']);
	$_SESSION['user_id'] = $userid;
		header('location:'.ISVIPI_URL.'home');
		exit();
}
$db->close();
?>