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
 include_once ISVIPI_USER_INC_BASE. 'emails/emailFunc.php';
isLoggedIn();    //We will integrate better security in next releases
$user = $_SESSION['user_id'];
getUserDetails($user);
$from_url = $_SERVER['HTTP_REFERER'];


//Define key actions
//It will return fail if no correct action is defined from a POST command
$action = $_GET['action'];
if (!is_numeric($action))
	{
	$_SESSION['err'] =INV_ACTION;
    header ('location:'.$from_url.'');
	exit();
}
if ($action !== '0' && $action !== '1' && $action !== '3' && $action !== '4' && $action !== '5' && $action !== '6')
	{
	$_SESSION['err'] =UNKNOWN_REQ;
    header ('location:'.$from_url.'');
	exit();
}
	
/////////////////////////////////////////////////////////////
//////////////// ADD FRIEND REQUEST (ACTION=3) /////////////
////////////////////////////////////////////////////////////
if ($action === '3') {	
	if (isset($_GET['id']))
		$to_id = $_GET['id'];
			//Check to see whether our ID is clean
			if (!is_numeric($to_id))
				{
				$_SESSION['err'] =INVALID_ID;
				header ('location:'.$from_url.'');
				exit();
			}
	
			//We check if an existing request is available
			$from_id = $user;
			$id = $to_id;

		if ($from_id == $id){
			$_SESSION['err'] =N_ACTION_NOT_ALLOWED;
			header ('location:'.$from_url.'');
			exit();	
		}
		if(checkExistingReq($id,$user)){
			$_SESSION['err'] =N_FREQUEST_EXISTS;
			header ('location:'.$from_url.'');
			exit();
		}

		//we then check if they are already friends
		if(checkFriendship($id,$user)){
			$_SESSION['err'] =ALREADY_FRIENDS;
			header ('location:'.$from_url.'');
			exit();	
		}
		$status= "0";
		$sendrq = $db->prepare('insert into friend_requests (from_id, to_id, status, timestamp) values (?,?,?,NOW())');
		$sendrq->bind_param('iii', $from_id,$to_id,$status);
		$sendrq->execute();
		$sendrq->close();
			//We then send an email notification if the user is not online
			if (!isOnlineNOW($to_id)){
					{getUserDetails($to_id);{$toUser = $username;$toEmail = $email;}}
					{getUserDetails($from_id);{$fromUser = $username;}}
				sendFReqnotif($toUser,$toEmail,$fromUser);	
				}
				//We the send a success messaege
				$_SESSION['succ'] =S_SUCCESS;
				header("location:".$from_url."");
				exit();
		}

/////////////////////////////////////////////////////////////
//////////////// ACCEPT FRIEND REQUEST (ACTION=1) //////////
////////////////////////////////////////////////////////////
if ($action === '1') {	
if (isset($_GET['id'])) {
$to_id = $_GET['id'];
//Check to see whether our ID is clean
if (!is_numeric($to_id))
	{
		$_SESSION['err'] =INVALID_ID;
		header("location:".$from_url."");
		exit();
	}
	
//We check if an existing request is available and return fail if it doesn't
$from_id = $user;
$id = $to_id;
if(!checkExistingReq($id,$user))
	{
		$_SESSION['err'] =N_NO_SUCH_REQ;
		header("location:".$from_url."");
		exit();
	}

//If reqest has been found we first update my_friends table then delete the request from friend_request
//The essence of deleting will ensure that no further manipulation of the request is done as the system will return a fail
//and stop any further activity in case the checkrequest does not find anything
if (addMyFriend($id,$user))
	
//We now delete the friend Request from the friend_request table
deleteFReq($user,$id);
//Then notify the user that his/her friend request has been accepted
if (isset($id)){
	getUserN($id);
	$notice = "<a href=".ISVIPI_URL."profile/".$username.">".$username."</a>" .N_ACCEPTED_YOUR_FREQ;
	$user = $id;
	//Then we update
	updNotices($user,$notice);
		
		//if the user is offline, we send an email
	
}
//Redirect back to friend_requests page
		$_SESSION['succ'] =S_SUCCESS;
		header("location:".$from_url."");
		exit();
	}
	exit();
}

/////////////////////////////////////////////////////////////
//////////////// REJECT FRIEND REQUEST (ACTION=0) //////////
////////////////////////////////////////////////////////////
if ($action === '0') {	
if (isset($_GET['id'])) {
$to_id = $_GET['id'];
//Check to see whether our ID is clean
if (!is_numeric($to_id))
	{
		$_SESSION['err'] =INVALID_ID;
		header("location:".$from_url."");
		exit();
	}
	
//We check if an existing request is available and return fail if it doesn't
$from_id = $user;
$id = $to_id;
if(!checkExistingReq($id,$user))
	{
		$_SESSION['err'] =N_NO_SUCH_REQ;
		header("location:".$from_url."");
		exit();
	}
	
	//If such a request exists, we update it
		global $db;
		$status = "5";
	//Update user status to online
	$upfreq = $db->prepare('UPDATE friend_requests set status=? WHERE (from_id=? AND to_id=?) OR (to_id=? AND from_id=?)');
		$upfreq->bind_param('iiiii', $status,$user,$id,$user,$id);
		$upfreq->execute();
	
	if (isset($id)){
	//Retrieve the user's username
	$user_id = $user;
	getUserN($user_id);
	$notice = "<a href=".ISVIPI_URL."profile/".$username.">".$username."</a> ".N_REJECTED_YOUR_FREQ;
	$user = $id;
	//Then we update
	updNotices($user,$notice);
}

		{
		$_SESSION['succ'] =S_SUCCESS;
		header("location:".$from_url."");
		exit();
	}
}
exit();
}

/////////////////////////////////////////////////////////////
//////////////// UNFRIEND (ACTION=4) ///////////////////////
////////////////////////////////////////////////////////////
if ($action === '4') {	
	if (isset($_GET['id'])) {
	$to_id = $_GET['id'];
	}
	//Check to see whether our ID is clean
	if (!is_numeric($to_id)){
		$_SESSION['err'] =INVALID_ID;
		header ('location:'.$from_url.'');
		exit();
	}
	//we then check if they are already friends
	if(!checkFriendship($to_id,$user)){
		$_SESSION['err'] =E_UNFRIEND_ERR;
		header ('location:'.$from_url.'');
		exit();	
	}
//We delete friendship
	global $db;
	$delfrnshp = $db->prepare('DELETE FROM my_friends WHERE (user1=? AND user2=?) OR (user2=? AND user1=?)');
	$delfrnshp->bind_param('iiii', $user,$to_id,$user,$to_id);
	$delfrnshp->execute();
    $_SESSION['succ'] =S_SUCCESS;
    header ('location:'.$from_url.'');
	exit();	
}
/////////////////////////////////////////////////////////////
//////////////// BLOCK USER (ACTION=5) /////////////////////
////////////////////////////////////////////////////////////
if ($action === '5') {
		
	if (isset($_GET['id'])) {
		$usrID = $_GET['id'];
	}
	if (!is_numeric($usrID)){
		$_SESSION['err'] =INVALID_ID;
		header ('location:'.$from_url.'');
		exit();
	}
		//We first check if they are friends and delete the friendship
		if(checkFriendship($usrID,$_SESSION['user_id'])){
			$stmt = $db->prepare('DELETE from my_friends WHERE (user1=? AND user2=?) OR (user2=? AND user1=?)');
			$stmt->bind_param('iiii', $_SESSION['user_id'],$usrID,$_SESSION['user_id'],$usrID);
			$stmt->execute();
			$stmt->close();
		}
	global $db;
	$stmt = $db->prepare('INSERT INTO blocked_users (user1,user2,timestamp) VALUES (?,?,NOW())');
	$stmt->bind_param('ii', $_SESSION['user_id'],$usrID);
	$stmt->execute();
	$stmt->close();
	
		$_SESSION['succ'] =S_SUCCESS;
		header ('location:'.$from_url.'');
		exit();	
}

/////////////////////////////////////////////////////////////
//////////////// UNBLOCK USER (ACTION=6) /////////////////////
////////////////////////////////////////////////////////////
if ($action === '6') {	
		if (isset($_GET['id'])) {
		$usrID = $_GET['id'];
	}
	if (!is_numeric($usrID)){
		$_SESSION['err'] =INVALID_ID;
		header ('location:'.$from_url.'');
		exit();
	}
	global $db;
		$stmt = $db->prepare('DELETE from blocked_users WHERE (user1=? AND user2=?) OR (user2=? AND user1=?)');
		$stmt->bind_param('iiii', $_SESSION['user_id'],$usrID,$_SESSION['user_id'],$usrID);
		$stmt->execute();
		$stmt->close();
		$_SESSION['succ'] =S_SUCCESS;
		header ('location:'.$from_url.'');
		exit();	
}
?>