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
if(!isset($_SESSION['admin'])) {
	header("Location: login.php");
	exit();
}
?>
<div class="main_body">
<?php
function post_settings_handler() {
	$truth = true;
	$settings_names = array('length','limit','width','height','speed','tcolor','hicolor','bgcolor','trans','wish_num','user_num');
	$settings_array = array('length'=>$_POST['length'],'limit'=>$_POST['limit'],'width'=>$_POST['width'],
		'height'=>$_POST['height'],'speed'=>$_POST['speed'],'tcolor'=>$_POST['tcolor'],'hicolor'=>$_POST['hicolor'],
		'bgcolor'=>$_POST['bgcolor'],'trans'=>$_POST['trans'],'wish_num'=>$_POST['wish_num'],'user_num'=>$_POST['user_num']);
	if (!empty($_POST['length']) && !empty($_POST['limit']) && !empty($_POST['width']) && !empty($_POST['height'])
	&& !empty($_POST['speed']) && !empty($_POST['tcolor']) && !empty($_POST['hicolor']) && !empty($_POST['bgcolor'])
	&& !empty($_POST['trans']) && !empty($_POST['wish_num']) && !empty($_POST['user_num'])) {
		foreach($settings_names as $element) {
			$query = "UPDATE settings SET content='$settings_array[$element]' WHERE name='$element'";
			$result = @mysql_query ($query);
			$truth = $truth && $result;
		}
		if($truth) {
			echo "<script LANGUAGE=\"JavaScript\">"
				."alert(\"修改成功。\");"
				."</script>";
		}
	}
}

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

require_once ('../mysql_connect.php');
if(isset($_POST['submitted'])) {
	post_settings_handler();
}
$current_settings = fetchSettings();

?>

<div id="manageContent">
<div id="settings_input">
<h3>设置</h3>
<form action="manage.php" method="post">
	<p><font class="info">字符串长度：</font> <input type="text" name="length" size="2" maxlength="2" value="<?php echo $current_settings['length']; ?>" />
	<font class="info">标签云中祝福数量：</font> <input type="text" name="limit" size="4" maxlength="4" value="<?php echo $current_settings['limit']; ?>" /> 
	<font class="info">标签云大小：</font><input type="text" name="width" size="3" maxlength="3" value="<?php echo $current_settings['width']; ?>" />
	<font class="info">X</font><input type="text" name="height" size="3" maxlength="3" value="<?php echo $current_settings['height']; ?>" />
	<font class="info">标签云切换速度：</font> <input type="text" name="speed" size="3" maxlength="3" value="<?php echo $current_settings['speed']; ?>" />
	<br/><font class="info">标签云文字颜色：#</font> <input type="text" name="tcolor" size="6" maxlength="6" value="<?php echo $current_settings['tcolor']; ?>" />
	<font class="info">标签云悬停文字框颜色：#</font> <input type="text" name="hicolor" size="6" maxlength="6" value="<?php echo $current_settings['hicolor']; ?>" />
	<font class="info">标签云背景色：#</font> <input type="text" name="bgcolor" size="6" maxlength="6" value="<?php echo $current_settings['bgcolor']; ?>" />
	<font class="info">标签云是否透明：</font> <input type="text" name="trans" size="5" maxlength="5" value="<?php echo $current_settings['trans']; ?>" />
	<br/><font class="info">右边栏显示祝福条数：</font> <input type="text" name="wish_num" size="2" maxlength="2" value="<?php echo $current_settings['wish_num']; ?>" />
	<font class="info">右边栏显示用户数目：</font> <input type="text" name="user_num" size="2" maxlength="2" value="<?php echo $current_settings['user_num']; ?>" />
	<input id="post" type="submit" name="submit" value="提交" /></p>
	<input type="hidden" name="submitted" value="TRUE" />
</form>
<div id="end_line">&nbsp;</div>
</div>
<div style="padding: 5px 0 20px 10px;"><a style="margin: 5px 20px;" href="view_wishes.php">祝福列表</a>
	<a href="view_users.php">用户列表</a></div>
</div>
</div>
<?php
include ('./includes/footer.html');
?>