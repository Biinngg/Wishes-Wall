<?php
/*
	Name: Wishes_Wall
	URI:  http://unixoss.com/?q=wishes
	Description: Wishes wall using a flash based Tag Cloud.
	Version: 1.0
	Author: Liu Bing
	Author URI: http://www.unixoss.com
	
	Copyright 2011, Liu Bing

	Original WP-Cumulus plugin info:
	
	Plugin Name: WP-Cumulus
	Plugin URI: http://www.roytanck.com/2008/03/15/wp-cumulus-released
	Description: Flash based Tag Cloud for WordPress
	Version: 1.23
	Author: Roy Tanck
	Author URI: http://www.roytanck.com
	
	Copyright 2009, Roy Tanck

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
$page_title = 'iBeiKe十周年祝福墙';
include ('./includes/header.html');
require_once ('../mysql_connect.php');

function fetchSettings() {
	$options = array (
		'width' => '550',
		'height' => '455',
		'tcolor' => 'fbe70e',
		'hicolor' => '336699',
		'bgcolor' => '6B0000',
		'speed' => '100',
		'trans' => 'false',
		'size' => '3.0'
	);
	$query = "SELECT name, content FROM settings";
	$result = mysql_query ($query);
	while($row = mysql_fetch_array ($result, MYSQL_ASSOC)) {
		$options[$row['name']] = $row['content'];
	}
	return $options;
}

function escape_string($escape_data){
	$str=htmlspecialchars($escape_data);
	$str=escape_data($str);
	return $str;
}

function post_wishes_handler(){
	if (!empty($_POST['wishes'])) {
		$wishes_content = escape_string($_POST['wishes']);
	} else {
		$wishes_content = FALSE;
	}
	if (!empty($_POST['id'])) {
		$discuz_id = escape_string($_POST['id']);
	} else {
		$discuz_id = FALSE;
	}
	if (!empty($_POST['name'])) {
		$user_name = escape_string($_POST['name']);
	} else {
		$user_name = FALSE;
	}
	if (!empty($_POST['phone'])) {
		$phone_number = escape_string($_POST['phone']);
	} else {
		$phone_number = FALSE;
	}
	if ($wishes_content && $discuz_id && $user_name && $phone_number) {
		$timestamp = time();
		$query1 = "SELECT user_id FROM users WHERE discuz_id='$discuz_id' OR name='$user_name' OR phone='$phone_number'";		
		$result1 = @mysql_query ($query1);
		
		if (mysql_num_rows($result1) == 0) {
			$query2 = "INSERT INTO users (discuz_id,phone,name) VALUES ('$discuz_id', '$phone_number', '$user_name')";		
			$result2 = @mysql_query ($query2);
		}
		$query3 = "SELECT user_id FROM users WHERE discuz_id='$discuz_id'";
		$result3 = mysql_query ($query3);
		while($row = mysql_fetch_array ($result3, MYSQL_ASSOC)) {
			$query4 = "INSERT INTO wishes (wishes_content,user_id,wishes_posted) VALUES ('$wishes_content', '$row[user_id]',$timestamp)";
			$result4 = @mysql_query ($query4);
			if($result4) {
				echo "<script LANGUAGE=\"JavaScript\">"
					."alert(\"提交成功\")"
					."</script>";
			}
		}
	} else { 
			echo "<script LANGUAGE=\"JavaScript\">"
				."alert(\"请完整填写后重新提交。\")"
				."</script>";	
	}
}

function utf8_strlen($string = null) {
	preg_match_all("/./us", $string, $match);
	return count($match[0]);
}

function utf8Substr($str, $from, $len) {  
	return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.  
'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',  
'$1',$str);  
}

function get_wishes_content($limit, $length,$size){
	$query = "SELECT wishes_id,wishes_content, user_id FROM wishes ORDER BY wishes_posted DESC LIMIT $limit";
	$result = mysql_query ($query);
	$n = 0;
	$wish_content = '';
	while($row = mysql_fetch_array ($result, MYSQL_ASSOC)) {
		$fetched_wishes = '';
		$n++;
		$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
		if ((substr($url, -1) == '/') OR (substr($url, -1) == '\\') ) {
			$url = substr ($url, 0, -1); 
		}
		$link = $url."/wishes.php?uid=".$row['user_id']."&cid=".$row['wishes_id'];
		$wish_content .= "<a href='$link' style='font-size: $size pt;'>";
		$fetched_wishes = $row['wishes_content'];
		$sub = utf8Substr($fetched_wishes, 0,$length);
		$wish_content .= $sub."...";
		$wish_content .= "</a>\n";
	}
	return $wish_content;
}
// piece together the flash code
function build_wishes_cloud($tagcloud_res,$options){
	$options['tcolor2'] = '336699';
	$options['distr'] = 'true';
	$options['mode'] = 'tags';
	// get the options
	$soname = "widget_so";
	$divname = "wpcumuluswidgetcontent";
	
	$tagcloud = urlencode( $tagcloud_res);
	$soname .= rand(0,9999999);
	$movie = 'includes/tagcloud.swf?r=' . rand(0,9999999);
	$divname .= rand(0,9999999);
	// write flash tag
	$flashtag = '<!-- SWFObject embed by Geoff Stearns geoff@deconcept.com http://blog.deconcept.com/swfobject/ -->';	
	$flashtag .= '<script type="text/javascript" src="'.'includes/swfobject.js"></script>';
	$flashtag .= '<div id="'.$divname.'">';
	$flashtag .= '<p>';
	// alternate content
	$flashtag .= urldecode($tagcloud);
	$flashtag .= '</p><p>iBeiKe wish cloud by <a href="http://www.ibeike.com" rel="nofollow">iBeiKe Team</a> requires <a href="http://www.macromedia.com/go/getflashplayer">Flash Player</a> 9 or better.</p></div>';
	$flashtag .= '<script type="text/javascript">';
	$flashtag .= 'var '.$soname.' = new SWFObject("'.$movie.'", "tagcloudflash", "'.$options['width'].'", "'.$options['height'].'", "9", "#'.$options['bgcolor'].'");';
	if( $options['trans'] == 'true' ){
		$flashtag .= $soname.'.addParam("wmode", "transparent");';
	}
	$flashtag .= $soname.'.addParam("allowScriptAccess", "always");';
	$flashtag .= $soname.'.addVariable("tcolor", "0x'.$options['tcolor'].'");';
	$flashtag .= $soname.'.addVariable("tcolor2", "0x' . ($options['tcolor2'] == "" ? $options['tcolor'] : $options['tcolor2']) . '");';
	$flashtag .= $soname.'.addVariable("hicolor", "0x' . ($options['hicolor'] == "" ? $options['tcolor'] : $options['hicolor']) . '");';
	$flashtag .= $soname.'.addVariable("tspeed", "'.$options['speed'].'");';
	$flashtag .= $soname.'.addVariable("distr", "'.$options['distr'].'");';
	$flashtag .= $soname.'.addVariable("mode", "'.$options['mode'].'");';
	// put tags in flashvar
	$flashtag .= $soname.'.addVariable("tagcloud", "'.urlencode('<tags>') . $tagcloud . urlencode('</tags>').'");';
	
	$flashtag .= $soname.'.write("'.$divname.'");';
	$flashtag .= '</script>';
	return $flashtag;
}

function statistics($selects,$wish_num,$user_num,$length) {
	$return_content = '';
	switch($selects)
	{
	case 'wishes_num':
		$select = "COUNT(*)";
		$from = "wishes";
		$order = "wishes_id";
		$limit = 1;
		break;
	case 'user_num':
		$select = "COUNT(*)";
		$from = "users";
		$order = "user_id";
		$limit = 1;
		break;
	case 'best_wishes':
		$select = "wishes_content";
		$from = "wishes";
		$order = "weight";
		$limit = $wish_num;
		break;
	case 'best_users':
		$select = "discuz_id";
		$from = "users";
		$order = "score";
		$limit = $user_num;
		break;
	}
	if($selects=="best_users") {
		$query = "SELECT $select,user_id FROM $from ORDER BY $order DESC LIMIT $limit";
	} elseif($selects=="best_wishes") {
		$query = "SELECT $select,user_id,wishes_id FROM $from ORDER BY $order DESC LIMIT $limit";
	}
	else {
		$query = "SELECT $select FROM $from ORDER BY $order DESC LIMIT $limit";
	}
	$result = mysql_query ($query);
	while($row = mysql_fetch_array ($result, MYSQL_ASSOC)) {
		if($select == "discuz_id") {
			$uid=$row['user_id'];
			$return_content .= "<div class=\"$selects\"><a href=\"wishes.php?uid=$uid\">"
					.utf8Substr($row[$select], 0,$length)."...</a></div>";
		} elseif($selects=="best_wishes") {
			$uid=$row['user_id'];
			$cid=$row['wishes_id'];
			$return_content .= "<div class=\"$selects\"><a href=\"wishes.php?uid=$uid&cid=$cid\">"
					.utf8Substr($row[$select], 0,$length)."...</a></div>";
		}
		else {
			$return_content = $row[$select];
		}
	}
	return $return_content;
}

?>

<div id="Content">
<div id="wishes_input">
<h3>iBeiKe十岁啦，想对她说点什么？</h3>
<form action="index.php" method="post">
	<p><textarea id="wish_content" name="wishes" maxlength="280" ></textarea><br />
	<font id="discuz_id" class="info">论坛id：</font> <input type="text" name="id" size="8" maxlength="20" value="<?php if (isset($_POST['id'])) echo $_POST['id']; ?>" />
	<font class="info">姓名：</font> <input type="text" name="name" size="5" maxlength="10" value="<?php if (isset($_POST['name'])) echo $_POST['name']; ?>" /> 
	<font class="info">电话号码：</font><input type="text" name="phone" size="11" maxlength="11" value="<?php if (isset($_POST['phone'])) echo $_POST['phone']; ?>" />
	<input id="submit" type="submit" name="submit" value="" /></p>
	<input type="hidden" name="submitted" value="TRUE" />
</form>
</div>

<?php
if (isset($_POST['submitted'])) {
	if(isset($PHPSESSID)) { 
		session_id($PHPSESSID); 
	}
	$PHPSESSID = session_id();
	$lifeTime = 30;
	setcookie(session_name(), $PHPSESSID, time() + $lifeTime, "/");
	if(!isset($_SESSION['posted'])) {
		post_wishes_handler();
	} else {
		echo "<script LANGUAGE=\"JavaScript\">"
			."alert(\"发送祝福过于频繁。\")"
			."</script>";
	}
	$_SESSION["posted"] = true;
}
$settings = fetchSettings();
$wishes_contents = get_wishes_content($settings['limit'],$settings['length'],$settings['size']);
$wishes_cloud = build_wishes_cloud($wishes_contents,$settings);
echo "$wishes_cloud";
?>

</div>
<div class="right_sidebar">

<?php
$wishes_max = statistics('wishes_num',$settings['wish_num'],$settings['user_num'],$settings['length']);
$user_max = statistics('user_num',$settings['wish_num'],$settings['user_num'],$settings['length']);
$best_wishes = statistics('best_wishes',$settings['wish_num'],$settings['user_num'],$settings['length']);
$best_users = statistics('best_users',$settings['wish_num'],$settings['user_num'],$settings['length']);
echo "<p class=\"statistic\">已发祝福数量:<font class=\"wishes_count\">$wishes_max</font>&nbsp;&nbsp;".
		"写下祝福人数:<font class=\"user_count\">$user_max</font></p>";
echo "<p class=\"best_title\">热门祝福:</p>$best_wishes";
echo "<p class=\"best_title\">活跃用户:</p>$best_users";
?>

</div>

<?php
include ('./includes/footer.html');
?>