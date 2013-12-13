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
DEFINE('INCLUDE_CHECK',1);
require_once('../lib/connections/db.php');
include('../lib/functions/admin_functions.php');

checkLogin('1');

		if (empty($_POST['newpassword'])) 
		{
			die(msg(0,"New password field empty!"));
		}
		
		if(strlen($_POST['newpassword'])<5)
		{
			die(msg(0,"New password must contain more than 5 characters."));
		}
				
		$res = adminUpdatePass($_POST['id'],$_POST['newpassword']);
				
			if($res == 1){
				die(msg(0,"An error occured saving the password. Please try again."));
			}
			if($res == 99){
				die(msg(1,"New password saved."));
			}

	function msg($status,$txt)
	{
		return '{"status":'.$status.',"txt":"'.$txt.'"}';
	}

?>
