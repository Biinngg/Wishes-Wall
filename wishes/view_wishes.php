<?php 
/*
	Name: Wishes_Wall
	URI:  http://unixoss.com/?q=wishes
	Description: Wishes wall using a flash based Tag Cloud.
	Version: 1.0
	Author: Liu Bing
	Author URI: http://www.unixoss.com
*/
$page_title = 'wishes list';
include ('./includes/header.html');
echo "<div class=\"main_body\">";

if(!isset($_SESSION['admin'])) {
	header("Location: login.php");
	exit();
}
else {
require_once ('../mysql_connect.php'); 

function utf8Substr($str, $from, $len) {  
	return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.  
'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',  
'$1',$str);  
}

$first = TRUE; 

$display = 50;

if (isset($_GET['np'])) {
	$num_pages = $_GET['np'];
} else { 
	$query = "SELECT COUNT(*) FROM wishes";
	$result = @mysql_query ($query);
	$row = mysql_fetch_array ($result, MYSQL_NUM);
	$num_records = $row[0];
	
	if ($num_records > $display) { 
		$num_pages = ceil ($num_records/$display);
	} else {
		$num_pages = 1;
	}
}
if (isset($_GET['s'])) {
	$start = $_GET['s'];
} else {
	$start = 0;
}

// Default column links.
$link1 = "{$_SERVER['PHP_SELF']}?sort=wea";
$link2 = "{$_SERVER['PHP_SELF']}?sort=ida";
$link3 = "{$_SERVER['PHP_SELF']}?sort=dea";

// Determine the sorting order.
if (isset($_GET['sort'])) {

	// Use existing sorting order.
	switch ($_GET['sort']) {
		case 'wea':
			$order_by = 'weight ASC';
			$link1 = "{$_SERVER['PHP_SELF']}?sort=wed";
			break;
		case 'wed':
			$order_by = 'weight DESC';
			$link1 = "{$_SERVER['PHP_SELF']}?sort=wea";
			break;
		case 'ida':
			$order_by = 'user_id ASC';
			$link2 = "{$_SERVER['PHP_SELF']}?sort=idd";
			break;
		case 'idd':
			$order_by = 'user_id DESC';
			$link2 = "{$_SERVER['PHP_SELF']}?sort=ida";
			break;
		case 'dea':
			$order_by = 'wishes_posted DESC';
			$link3 = "{$_SERVER['PHP_SELF']}?sort=ded";
			break;
		case 'ded':
			$order_by = 'wishes_posted ASC';
			$link3 = "{$_SERVER['PHP_SELF']}?sort=dea";
			break;
		default:
			$order_by = 'wishes_posted DESC';
			break;
	}
	
	// $sort will be appended to the pagination links.
	$sort = $_GET['sort'];
	
} else { // Use the default sorting order.
	$order_by = 'wishes_posted DESC';
	$sort = 'ded';
}

if(isset($_GET['cid'])) {
	$id = $_GET['cid'];
	$query = "DELETE FROM wishes WHERE wishes_id=$id";		
	$result = @mysql_query ($query);
	if (mysql_affected_rows() == 1) {
		echo "<script LANGUAGE=\"JavaScript\">"
			."alert(\"删除成功。\")"
			."</script>";
		header("refresh:1;url=view_wishes.php");
	}
}

$query = "SELECT wishes_id,user_id,wishes_content,weight,wishes_posted FROM wishes ORDER BY $order_by LIMIT $start, $display";
$result = mysql_query ($query);
while ($row = mysql_fetch_array ($result, MYSQL_ASSOC)) {

	if ($first) {
		echo '<table border="0" width="900px" cellspacing="3" cellpadding="3" align="center">
	<tr id="file_category">';
		if($_SESSION['admin']==true)
			echo '<td align="center">管理</td>';
		echo '<td align="center"><font size="+1">祝福id</font></td>
		<td align="center"><font size="+1">内容</font></td>
		<td align="center"><a href="' . $link1 . '"><font size="+1">好评</font></a></td>
		<td align="center"><a href="' . $link2 . '"><font size="+1">用户id</font></a></td>
		<td align="center"><a href="' . $link3 . '"><font size="+1">日期</font></a></td>
	</tr>';
		$first = FALSE; 
	}
	
	echo "	<tr id=\"file_list\">";
		if($_SESSION['admin']==true)
			echo "<td align=\"center\"><a href=\"view_wishes.php?cid={$row['wishes_id']}\"><font size=\"-2\">删除</font></a></td>";
		echo "<td align=\"center\">{$row['wishes_id']}</a></td>
		<td align=\"center\">".utf8Substr($row['wishes_content'],0,30)."</td>
		<td align=\"center\">{$row['weight']}</td>
		<td align=\"center\"><a href=\"wishes.php?uid={$row['user_id']}\">" .$row['user_id']. "</a></td>
		<td align=\"center\">" .date("Y-m-d H:i:s",$row['wishes_posted']). "</td>
	</tr>\n";
	
}

if ($num_pages > 1) {
	
	echo '<br /><p>';
	// Determine what page the script is on.	
	$current_page = ($start/$display) + 1;
	
	// If it's not the first page, make a Previous button.
	if ($current_page != 1) {
		echo '<a href="view_wishes.php?s=' . ($start - $display) . '&np=' . $num_pages . '&sort=' . $sort .'">Previous</a> ';
	}
	
	// Make all the numbered pages.
	for ($i = 1; $i <= $num_pages; $i++) {
		if ($i != $current_page) {
			echo '<a href="view_wishes.php?s=' . (($display * ($i - 1))) . '&np=' . $num_pages . '&sort=' . $sort .'">' . $i . '</a> ';
		} else {
			echo $i . ' ';
		}
	}
	
	// If it's not the last page, make a Next button.
	if ($current_page != $num_pages) {
		echo '<a href="view_wishes.php?s=' . ($start + $display) . '&np=' . $num_pages . '&sort=' . $sort .'">Next</a>';
	}
	echo '</p>';
} // End of links section.

if ($first) {
	echo '<div align="center">没有祝福</div>';
} else {
	echo '</table>'; 
}

mysql_close(); 
}
echo "</div>";
include ('./includes/footer.html');
?>
