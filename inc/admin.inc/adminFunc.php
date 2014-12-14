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
//include_once ISVIPI_USER_INC_BASE. 'emails/emailFunc.php';
function AgentIPCheck(){
	global $db;
	$admin_id =$_SESSION['admin_id'];
	$agentcheck = $db->prepare("SELECT ip,user_agent FROM admin WHERE id=?");
	$agentcheck->bind_param("i",$admin_id);
	$agentcheck->execute();
	$agentcheck->store_result();
	$agentcheck->bind_result($ip,$userAgent);
	$agentcheck->fetch();
	$agentcheck->close();	
		$curr_ip = $_SERVER['REMOTE_ADDR'];
		$curr_usrAgnt = $_SERVER['HTTP_USER_AGENT'];
			if ($curr_ip == $ip && $curr_usrAgnt == $userAgent)
			{
				return true;	
			}
			else
			{
				return false;
			}
}
function isAdminLoggedIn (){
	global $adminPath;
	if(!isset($_SESSION['admin_logged_in'])) {
		$_SESSION['err'] =E_LOG_IN_PROMPT;
		header('location: '.ISVIPI_URL.$adminPath.'/login/');
		exit();
		}
		else if (!AgentIPCheck()){
		$_SESSION['err'] =E_IP_BROWSER_CHANGE;
		header('location: '.ISVIPI_URL.$adminPath.'/login/');
		exit();	
		}
	}
function getAdminDetails($value){
	global $db;
	global $username;
	global $email;
	$getAdminDet = $db->prepare("SELECT username,email FROM admin WHERE id=?");
	$getAdminDet->bind_param("i",$value);
	$getAdminDet->execute();
	$getAdminDet->store_result();
	$getAdminDet->bind_result($username,$email);
	$getAdminDet->fetch();
	$getAdminDet->close();	
}
function getIsVipiFeeds(){
		$rss = new DOMDocument();
	$rss->load('http://isvipi.org/feed/');
	$feed = array();
	foreach ($rss->getElementsByTagName('item') as $node) {
		$item = array ( 
			'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
			'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
			'guid' => $node->getElementsByTagName('guid')->item(0)->nodeValue,
			'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
			);
		array_push($feed, $item);
	}
	$limit = 2;
	for($x=0;$x<$limit;$x++) {
		$title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
		$link = $feed[$x]['guid'];
		$description = $feed[$x]['desc'];
		$descr = trunc_text($description, 20);
		$date = date('l F d, Y', strtotime($feed[$x]['date']));
		echo '<small><em>'.POSTED_ON.' '.$date.'</em></small></p>';
		echo '<p><strong><a href="'.$link.'" title="'.$title.'" target="_blank">'.$title.'</a></strong><br />';
		echo '<p>'.$descr.'</p>';
	}
}

//Get Members function
function getMembersAll(){
	global $db;
	global $getmembersAll;
	global $id;
	global $profile_name;
	global $email;
	global $Allcount;
	global $status;
	$getmembersAll = $db->prepare("SELECT id,username,email,active FROM members");
	$getmembersAll->execute();
	$getmembersAll->store_result();
	$getmembersAll->bind_result($id,$profile_name,$email,$status);
	$Allcount = $getmembersAll->num_rows();
}

//Get Members function
function getMembersAll2($pager,$filter,$p_limit){
	global $db;
	global $getmembersAll2;
	global $id;
	global $profile_name;
	global $email;
	global $m_count;
	global $status;
	if ($filter=="5"){
	$getmembersAll2 = $db->prepare("SELECT id,username,email,active FROM members ORDER BY ID DESC LIMIT ?, ?");
	$getmembersAll2->bind_param("ii",$pager,$p_limit);
	$getmembersAll2->execute();
	$getmembersAll2->store_result();
	$getmembersAll2->bind_result($id,$profile_name,$email,$status);
	$m_count = $getmembersAll2->num_rows;	
	} else {
	$getmembersAll2 = $db->prepare("SELECT id,username,email,active FROM members WHERE active=?");
	$getmembersAll2->bind_param("i",$filter);
	$getmembersAll2->execute();
	$getmembersAll2->store_result();
	$getmembersAll2->bind_result($id,$profile_name,$email,$status);
	$m_count = $getmembersAll2->num_rows;
	
	}
}

//Get New Members function
function getNewMembersAll(){
	global $db;
	global $getmembers;
	global $id;
	global $n_count;
	global $username;
	global $email;
	$getmembers = $db->prepare("SELECT id,username,email FROM members where (reg_date > NOW() - INTERVAL 1 DAY) ORDER BY ID Desc");
	$getmembers->execute();
	$getmembers->store_result();
	$getmembers->bind_result($id,$username,$email);
	$n_count = $getmembers->num_rows();
}

//Get New Members function
function getNewMembersAll2(){
	global $db;
	global $getmembers;
	global $id;
	global $n_count;
	global $username;
	global $email;
	$getmembers = $db->prepare("SELECT id,username,email FROM members where (reg_date > NOW() - INTERVAL 1 DAY) ORDER BY ID Desc LIMIT 0, 5");
	$getmembers->execute();
	$getmembers->store_result();
	$getmembers->bind_result($id,$username,$email);
	$n_count = $getmembers->num_rows();
}


function upSiteStatus($value){
	global $db;
	$status ="1";
	$siteStatus = $db->prepare("UPDATE site_settings SET status=? LIMIT 1");
	$siteStatus->bind_param("i", $value);
	$siteStatus->execute();
	$siteStatus->close();	
}
function selectTheme(){
	global $db;
	global $user_country;
	$selTheme = $db->prepare("SELECT theme_name FROM themes");
	$selTheme->execute();
	$selTheme->store_result();
	$selTheme->bind_result($theme_name);
	echo '<select name="theme_name" class="form-control">';
	while($selTheme->fetch()){?>
	<option value="<?php echo $theme_name ?>"><?php echo $theme_name ?></option>
    <?php }
	echo '</select>';
}

function isOneWeeks(){
	global $db;
	$isTwoW = $db->prepare("SELECT id FROM site_settings WHERE (last_version_check > NOW() - INTERVAL 1 WEEK)");
	$isTwoW->execute();
	$isTwoW->store_result();
	if ($isTwoW->num_rows > 0){
	return false;
	}
	else {
		return true;
	}
	$isTwoW->close();
}

function updateSystem() {
	global $db;
	upSiteStatus('3');
	$url  = 'http://isvipi.org/files/isvipi.zip';
    $path = ISVIPI_ROOT.'temp/isvipi.zip';
    $fp = fopen($path, 'w');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    $data = curl_exec($ch);
    curl_close($ch);
    fclose($fp);
   
    $zip = new ZipArchive;
	$extractPath = ISVIPI_ROOT;
    $res = $zip->open($path);
    if ($res === TRUE) {
        $zip->extractTo($extractPath);
        $zip->close();
        return TRUE;
		unlink ($path);
    } else {
        return FALSE;
    }
	$uplastVcheck = $db->prepare('UPDATE site_settings set last_version_check=NOW() LIMIT 1');
	$uplastVcheck->execute();
	$uplastVcheck->close();
}
function checkVersion(){
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
}
function genBackUp(){
	include_once ISVIPI_ADMIN_INC_BASE. 'backup.php';
}
function getAllPages(){
	global $db;
	global $getAllP;
	global $p_title;
	global $p_id;
	$getAllP = $db->prepare('SELECT id,title,content FROM pages ORDER by id ASC LIMIT 2,9999 ');
	$getAllP->execute();
	$getAllP->store_result();
	$getAllP->bind_result($p_id,$p_title,$p_content);
}
function getEditpage($pid){
	global $db;
	global $p_title;
	global $p_content;
	$getEditp = $db->prepare("SELECT title,content FROM pages WHERE id=?");
	$getEditp->bind_param("s",$pid);
	$getEditp->execute();
	$getEditp->store_result();
	$getEditp->bind_result($p_title,$p_content);
	$getEditp->fetch();
	$getEditp->close();		
}
function getAllAnnounc(){
	global $db;
	global $getAllAnn;
	global $annID;
	global $annDate;
	global $annSubject;
	global $annContent;
	$getAllAnn = $db->prepare('SELECT id,date,subject,content FROM announcements ORDER by date DESC ');
	$getAllAnn->execute();
	$getAllAnn->store_result();
	$getAllAnn->bind_result($annID,$annDate,$annSubject,$annContent);
}
function getEditAnn($annID){
	global $db;
	global $a_subject;
	global $a_content;
	$getEditp = $db->prepare("SELECT subject,content FROM announcements WHERE id=?");
	$getEditp->bind_param("s",$annID);
	$getEditp->execute();
	$getEditp->store_result();
	$getEditp->bind_result($a_subject,$a_content);
	$getEditp->fetch();
	$getEditp->close();		
}
function genSitemap(){
include_once ISVIPI_ADMIN_INC_BASE. 'sitemap.php';
}
function siteMapPages(){
	global $db;
	global $siteMapP;
	global $p_title;
	global $p_id;
	$siteMapP = $db->prepare('SELECT id,title,content FROM pages ORDER by id');
	$siteMapP->execute();
	$siteMapP->store_result();
	$siteMapP->bind_result($p_id,$p_title,$p_content);
}
function selectLang(){
	getAdminGenSett();
	global $lang;
	global $db;
	global $language;
	$selectLan = $db->prepare("SELECT name,abbr FROM languages");
	$selectLan->execute();
	$selectLan->store_result();
	$selectLan->bind_result($name,$abbr);
	echo '<select name="language" class="form-control">';
	while($selectLan->fetch()){?>
	<option <?php if ($lang == $abbr ){echo "selected";}?> value="<?php echo $abbr ?>"><?php echo $name ?></option>
    <?php }
	echo '</select>';
}
function getAllEmails($to){
	global $db;
	global $usrEmail;
	global $usrID;
	global $stmt;
	if ($to == 5) {
	$stmt = $db->prepare("SELECT id,email FROM members");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($usrID,$usrEmail);
	} else if ($to == 9){
		$tr = 0;
		$stmt = $db->prepare("SELECT id,email FROM members WHERE active=?");
		$stmt ->bind_param('i',$tr);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($usrID,$usrEmail);	
	} else {
	$stmt = $db->prepare("SELECT id,email FROM members WHERE active=?");
	$stmt ->bind_param('i',$to);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($usrID,$usrEmail);
	}
}
function recordSentMsgs($usrID,$subject,$message){
	global $db;
	$limit = 250;
	$message = trunc_text($message, $limit);
	$stmt = $db->prepare('insert into sent_msgs (msg_to,subject,message,timestamp) values (?,?,?,NOW())');
	$stmt->bind_param('iss', $usrID,$subject,$message);
	$stmt->execute();
	$stmt->close();
}

function getSentMessages(){
	global $db;
	global $stmt;
	global $msgTO;
	global $message;
	global $timestamp;
	global $subject;
	$stmt = $db->prepare("SELECT msg_to,subject,message,timestamp FROM sent_msgs ORDER BY id DESC LIMIT 10");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($msgTO,$subject,$message,$timestamp);
}
function AddonIsInstalled($value){
	global $db,$ADDonID,$Addcount,$ADDURI,$ADDDESC,$ADDVER,$ADDAUTH,$ADDAUTHURI,$ADDSTATUS;
	$stmt = $db->prepare("SELECT id,mod_url,description,version,author,author_url,active FROM mods WHERE mod_name=?");
	$stmt->bind_param('s', $value);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($ADDonID,$ADDURI,$ADDDESC,$ADDVER,$ADDAUTH,$ADDAUTHURI,$ADDSTATUS);
	$stmt->fetch();
	$Addcount = $stmt->num_rows();
	$stmt->close();
	
}

?>