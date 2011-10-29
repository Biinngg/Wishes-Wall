<?php
/*
	Name: Wishes_Wall
	URI:  http://unixoss.com/?q=wishes
	Description: Wishes wall using a flash based Tag Cloud.
	Version: 1.0
	Author: Liu Bing
	Author URI: http://www.unixoss.com
*/
$page_title = '登陆';
include ('./includes/header.html');

if (isset($_SESSION['admin'])) {
	header("Location: manage.php");
}
	
if (isset($_POST['submitted'])) {
	require_once ('../mysql_connect.php'); 

	if (!empty($_POST['id'])) {
		$u = escape_data($_POST['id']);
	} else {
		echo '<p><font color="red" size="+1">没有输入用户名</font></p>';
		$u = FALSE;
	}
	
	if (!empty($_POST['pass'])) {
		$p = escape_data($_POST['pass']);
	} else {
		$p = FALSE;
		echo '<p><font color="red" size="+1">没有输入密码</font></p>';
	}
	
	if ($u && $p) { 
		$query = "SELECT admin_id FROM admin WHERE (admin_name='$u' AND pass_word=SHA('$p'))";		
		$result = mysql_query ($query) or trigger_error("Query: $query\n<br />MySQL Error: " . mysql_error());
		
		if (@mysql_num_rows($result) == 1) {
			mysql_free_result($result);
			mysql_close(); 
			session_start();
			$_SESSION['admin'] = true;
							
			$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
			if ((substr($url, -1) == '/') OR (substr($url, -1) == '\\') ) {
				$url = substr ($url, 0, -1); 
			}
			$url .= '/manage.php';
			
			ob_end_clean();
			header("Location: $url");
			exit(); 
		} else { 
			echo '<p><font color="red" size="+1">用户名和密码不匹配</font></p>'; 
		}
	} else { 
		echo '<p><font color="red" size="+1">请重试</font></p>';		
	}
	
	mysql_close(); 

}
?>

<h1>登录</h1>
<form action="login.php" method="post">
	<fieldset>
	<p><b>用户名:</b> <input type="text" name="id" size="20" maxlength="40"/></p>
	<p><b>密码:</b> <input type="password" name="pass" size="20" maxlength="20" /></p>
	<div align="center"><input type="submit" name="submit" value="登录" /></div>
	<input type="hidden" name="submitted" value="TRUE" />
	</fieldset>
</form>

<?php 
include ('./includes/footer.html');
?>
