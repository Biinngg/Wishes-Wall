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

$first = TRUE; 

$display = 50;

if (isset($_GET['np'])) {
	$num_pages = $_GET['np'];
} else { 
		$query = "SELECT COUNT(*) FROM users";
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
$link1 = "{$_SERVER['PHP_SELF']}?sort=ida";
$link2 = "{$_SERVER['PHP_SELF']}?sort=dia";
$link3 = "{$_SERVER['PHP_SELF']}?sort=pha";
$link4 = "{$_SERVER['PHP_SELF']}?sort=sca";

// Determine the sorting order.
if (isset($_GET['sort'])) {

	// Use existing sorting order.
	switch ($_GET['sort']) {
		case 'ida':
			$order_by = 'user_id ASC';
			$link1 = "{$_SERVER['PHP_SELF']}?sort=idd";
			break;
		case 'idd':
			$order_by = 'user_id DESC';
			$link1 = "{$_SERVER['PHP_SELF']}?sort=ida";
			break;
		case 'dia':
			$order_by = 'discuz_id ASC';
			$link2 = "{$_SERVER['PHP_SELF']}?sort=did";
			break;
		case 'did':
			$order_by = 'discuz_id DESC';
			$link2 = "{$_SERVER['PHP_SELF']}?sort=dia";
			break;
		case 'pha':
			$order_by = 'phone DESC';
			$link3 = "{$_SERVER['PHP_SELF']}?sort=phd";
			break;
		case 'phd':
			$order_by = 'phone ASC';
			$link3 = "{$_SERVER['PHP_SELF']}?sort=pha";
			break;
		case 'sca':
			$order_by = 'score DESC';
			$link4 = "{$_SERVER['PHP_SELF']}?sort=scd";
			break;
		case 'scd':
			$order_by = 'score ASC';
			$link4 = "{$_SERVER['PHP_SELF']}?sort=sca";
			break;
		default:
			$order_by = 'user_id DESC';
			break;
	}
	
	// $sort will be appenphd to the pagination links.
	$sort = $_GET['sort'];
	
} else { // Use the default sorting order.
	$order_by = 'user_id DESC';
	$sort = 'idd';
}

if(isset($_GET['duid'])) {
	$id = $_GET['duid'];
	$query1 = "DELETE FROM users WHERE user_id=$id";		
	$result1 = @mysql_query ($query1);
	$query2 = "DELETE FROM wishes WHERE user_id=$id";		
	$result2 = @mysql_query ($query2);
	if ($result1) {
		echo "<script LANGUAGE=\"JavaScript\">"
			."alert(\"删除成功。\")"
			."</script>";
		header("refresh:1;url=view_users.php");
	}
}

$query = "SELECT user_id,discuz_id,name,phone,score FROM users ORDER BY $order_by LIMIT $start, $display";
$result = mysql_query ($query);
while ($row = mysql_fetch_array ($result, MYSQL_ASSOC)) {

	if ($first) {
		echo '<table border="0" width="900px" cellspacing="3" cellpadding="3" align="center">
	<tr id="file_category">';
		if($_SESSION['admin']==true)
			echo '<td align="center" width="33px">管理</td>';
		echo '<td align="center" width="140px"><a href="' . $link1 . '"><font size="+1">id</font></a></td>
		<td align="center" width="116px"><a href="' . $link2 . '"><font size="+1">论坛id</font></a></td>
		<td align="center" width="116px"><font size="+1">姓名</font></td>
		<td align="center" width="110px"><a href="' . $link3 . '"><font size="+1">电话</font></a></td>
		<td align="center" width="116px"><a href="' . $link4 . '"><font size="+1">好评</font></a></td>
	</tr>';
		$first = FALSE; 
	}
	
	echo "	<tr id=\"file_list\">";
		if($_SESSION['admin']==true)
			echo "<td align=\"center\"><a href=\"view_users.php?duid={$row['user_id']}\"><font size=\"-2\">删除</font></a></td>";
		echo "<td align=\"center\">{$row['user_id']}</td>
		<td align=\"center\"><a href=\"wishes.php?uid={$row['user_id']}\">{$row['discuz_id']}</a></td>
		<td align=\"center\">{$row['name']}</td>
		<td align=\"center\">{$row['phone']}</td>
		<td align=\"center\">" .$row['score']. "</td>
	</tr>\n";
	
}

if ($num_pages > 1) {
	
	echo '<br /><p>';
	// Determine what page the script is on.	
	$current_page = ($start/$display) + 1;
	
	// If it's not the first page, make a Previous button.
	if ($current_page != 1) {
		echo '<a href="view_users.php?s=' . ($start - $display) . '&np=' . $num_pages . '&sort=' . $sort .'">Previous</a> ';
	}
	
	// Make all the numbered pages.
	for ($i = 1; $i <= $num_pages; $i++) {
		if ($i != $current_page) {
			echo '<a href="view_users.php?s=' . (($display * ($i - 1))) . '&np=' . $num_pages . '&sort=' . $sort .'">' . $i . '</a> ';
		} else {
			echo $i . ' ';
		}
	}
	
	// If it's not the last page, make a Next button.
	if ($current_page != $num_pages) {
		echo '<a href="view_users.php?s=' . ($start + $display) . '&np=' . $num_pages . '&sort=' . $sort .'">Next</a>';
	}
	echo '</p>';
} // End of links section.

if ($first) {
	echo '<div align="center">没有用户</div>';
} else {
	echo '</table>'; 
}

mysql_close(); 
}
echo"</div>";
include ('./includes/footer.html');
?>
