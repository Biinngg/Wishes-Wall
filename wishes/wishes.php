<?php
/*
	Name: Wishes_Wall
	URI:  http://unixoss.com/?q=wishes
	Description: Wishes wall using a flash based Tag Cloud.
	Version: 1.0
	Author: Liu Bing
	Author URI: http://www.unixoss.com
*/
$page_title = 'iBeiKe十周年祝福墙';
include ('./includes/header.html');
?>
<div class="main_body">
<?php
function fetchId($uid) {
	$query = "SELECT discuz_id FROM users WHERE user_id='$uid'";		
	$result = @mysql_query ($query);
	$row = mysql_fetch_array ($result, MYSQL_ASSOC);
	$user_name = $row['discuz_id'];
	return $user_name;
}

function fetchWishes($type,$uid) {
	switch($type) {
	case 'id':
		$select='wishes_id';
		break;
	case 'content':
		$select='wishes_content';
		break;
	}
	$query = "SELECT $select FROM wishes WHERE user_id='$uid' ORDER BY wishes_posted DESC";		
	$result = @mysql_query ($query);
	$i=0;
	while($row = mysql_fetch_array ($result, MYSQL_ASSOC)) {
		$content[$i] = $row[$select];
		$i++;
	}
	return $content;
}

function scoreCalculater($uid,$cid) {
	$query1 = "SELECT score FROM users WHERE user_id='$uid'";		
	$result1 = @mysql_query ($query1);
	while($row1 = mysql_fetch_array ($result1, MYSQL_ASSOC)) {
		$score = $row1['score'];
	}
	$query2 = "SELECT weight FROM wishes WHERE wishes_id='$cid'";
	$result2 = @mysql_query ($query2);
	while($row2 = mysql_fetch_array ($result2, MYSQL_ASSOC)) {
		$weight = $row2['weight'];
	}
	$array = array(
		'score' => $score,
		'weight' => $weight
	);
	return $array;
}

function scoreHandler($uid,$cid,$score,$weight) {
	$query1 = "UPDATE wishes SET weight=$weight WHERE wishes_id=$cid";
	$result1 = mysql_query ($query1);
	$query2 = "UPDATE users SET score=$score WHERE user_id=$uid";
	$result2 = mysql_query ($query2);
	return true;
}


if(isset($_GET['uid'])||isset($_GET['cid'])) {
	require_once ('../mysql_connect.php'); 
	$uid=$_GET['uid'];
	$user=fetchId($uid);
	$wishes_id=fetchWishes('id',$uid);
	$wishes_content=fetchWishes('content',$uid);
	if(isset($_GET['lid'])&&isset($_GET['cid'])) {
		if($_GET['lid']==1 || $_GET['lid']==0) {
			$cid=$_GET['cid'];
			$score_array=scoreCalculater($uid,$cid);
			if($_GET['lid']==1) {
				$score=$score_array['score']-1;
				$weight=$score_array['weight']-1;
			} elseif($_GET['lid']==0) {
				$score=$score_array['score']+1;
				$weight=$score_array['weight']+1;
			}
			if(isset($PHPSESSID)) { 
				session_id($PHPSESSID); 
			}
			$PHPSESSID = session_id();
			$lifeTime = 24 * 3600;
			setcookie(session_name(), $PHPSESSID, time() + $lifeTime, "/");
			$_SESSION["user_id"] = true;
			
			if (isset($_SESSION['user_id']) && !isset($_SESSION['start'])) {
				$handle_result = scoreHandler($uid,$cid,$score,$weight);
				echo "<script LANGUAGE=\"JavaScript\">"
					."alert(\"评分成功。\")"
					."</script>";
			} else {
				echo "<script LANGUAGE=\"JavaScript\">"
					."alert(\"你已经评过分。\")"
					."</script>";
			}
			$_SESSION["start"] = true;
		}
	}
	echo "<p class='wishes_title'>$user 写下的祝福</p>";
	$i=0;
	foreach($wishes_content as $element) {
		echo "<div class='wishes_content'>&nbsp;&nbsp;&nbsp;&nbsp;$element</div>"
			."<div class='like_dislike'><a class='like' href='wishes.php?uid=$uid&cid=$wishes_id[$i]&lid=0'>"
			."<img class='like_img' src='images/like.png'/></a>"
			."<a href='wishes.php?uid=$uid&cid=$wishes_id[$i]&lid=1'>"
			."<img class='dislike_img' src='images/dislike.png'/></a></div>";
		$i++;
	}
}
?>
<div id="end_line">&nbsp;</div>
</div>
<?php
include ('./includes/footer.html');
?>