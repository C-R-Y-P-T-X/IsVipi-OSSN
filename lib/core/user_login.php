<?php
/*******************************************************
 *   Copyright (C) 2013  http://isvipi.com

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
 ?>
<?php
require_once('../db/db.php');
include('../functions/functions.php');
//For login

	// we check if everything is filled in and perform checks
	
	if(!$_POST['username'] || !$_POST['password'])
	{
		die(msg(0,"Username and / or password fields empty!"));
	}

	else
		{
			$res = login($_POST['username'],$_POST['password']);
				if ($res == 1){
					die(msg(0,"Username and / or password incorrect!"));
				}
				if ($res == 2){
					die(msg(0,"Sorry! Your account has been suspended!"));
				}
				if ($res == 3){
					die(msg(0,"Sorry! Your account has not been activated. Please check your email's inbox or spam folder for a link to activate your account."));
				}
				if ($res == 99){
					$user = ($_POST['username']);
					mysqli_query($conn,"UPDATE users SET online=1
WHERE username='Jones'");

					echo(header("Location: ../../members/"));
				}
		}

	function msg($status,$txt)
	{
		return '{"status":'.$status.',"txt":"'.$txt.'"}';
	}
	
?>
