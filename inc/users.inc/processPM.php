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
isLoggedIn();   //We will try to integrate better security in next releases
$user = $_SESSION['user_id'];
getUserDetails($user);
$from_url = $_SERVER['HTTP_REFERER'];

//Define key actions
//It will return fail if no correct action is defined from a POST command
$msg = $_POST['msg'];
if (!is_numeric($msg))
	{
		$_SESSION['err'] =INV_ACTION;
		header("location:".$from_url."");
		exit();
	}
if ($msg !== '0'/**Add Message**/ && $msg !== '1' /**Retrieve Message**/ && $msg !== '3' /**Delete Message**/)
	{
		$_SESSION['err'] =UNKNOWN_REQ;
		header("location:".$from_url."");
		exit();
	}
	
/////////////////////////////////////////////////////////////
//////////////// ADD PM (MSG=0) ////////////////////////////
////////////////////////////////////////////////////////////
if ($msg === '0') {
//Capture and clean recipient ID	
if (isset($_POST['recip']))
$recip = $_POST['recip'];
//Check to see whether our ID is clean
if (!is_numeric($recip))
	{
		$_SESSION['err'] =E_INV_REC_ID;
		header("location:".$from_url."");
		exit();
	}
//Capture and clean Message	
if (isset($_POST['message']))
$msg = get_post_var('message');
if (empty($msg)) 
    {
		$_SESSION['err'] =E_MSG_EMPTY;
		header("location:".$from_url."");
		exit();
	}
	$message = htmlspecialchars("".$msg."", ENT_QUOTES);
//Check if an existing conversation between the two users is available
//checkConv($user,$recip);
if (checkConv($user,$recip)){
	//When all is okay, we insert values into the database
	addPM($user,$recip,$message,$unique_id);
} else {
$unique_id = mt_rand(0,1000).rand(0,1000);
addPM($user,$recip,$message,$unique_id);
}
getLastMsg ($unique_id);
updMsgUnRead($lastMsgID,$unique_id);

		$_SESSION['succ'] =S_MSG_SENT;
		header("location:".$from_url."");
		exit();
		{
    }
}
?>