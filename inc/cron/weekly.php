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
global $db;
define('REMOTE_VERSION', 'http://isvipi.org/version/version');
$Realesed_Ver = file_get_contents(REMOTE_VERSION);
$Realesed_Ver = str_replace(".", "", $Realesed_Ver);
$Inst_Ver = str_replace(".", "", VERSION);
if($Realesed_Ver == $Inst_Ver || $Inst_Ver > $Realesed_Ver ) {
	$uplastVcheck = $db->prepare('UPDATE site_settings set last_version_check=NOW() LIMIT 1');
	$uplastVcheck->execute();
	$uplastVcheck->close();
	$_SESSION['up-to-date'] = TRUE;
} else {
	upSiteStatus("5"); //status 5 for update available
	$uplastVcheck = $db->prepare('UPDATE site_settings set last_version_check=NOW() LIMIT 1');
	$uplastVcheck->execute();
	$uplastVcheck->close();
}
?>