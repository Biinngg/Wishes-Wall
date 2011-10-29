<?PHP #install.php
/*
	Name: Wishes_Wall
	URI:  http://unixoss.com/?q=wishes
	Description: Wishes wall using a flash based Tag Cloud.
	Version: 1.0
	Author: Liu Bing
	Author URI: http://www.unixoss.com
*/
$page_title = 'Create the needed tables';
include ('./includes/header.html');
echo '<h1 id="mainhead">创建需要的tables</h1>';
require_once ('../mysql_connect.php');
$query1 = "CREATE TABLE admin ( admin_id INT UNSIGNED NOT NULL AUTO_INCREMENT, admin_name VARCHAR(10) NOT NULL, pass_word CHAR(40) NOT NULL, PRIMARY KEY (admin_id), KEY (admin_name))";
$query2 = "CREATE TABLE users ( user_id INT unsigned NOT NULL auto_increment, discuz_id varchar(15) NOT NULL, phone varchar(11) NOT NULL, name CHAR(15) NOT NULL, score INT NOT NULL, PRIMARY KEY (user_id), UNIQUE KEY (name) )";
$query3 = "CREATE TABLE wishes ( wishes_id INT UNSIGNED NOT NULL AUTO_INCREMENT, wishes_content VARCHAR(280) NOT NULL, user_id INT NOT NULL, weight INT NOT NULL, wishes_posted varchar(10) NOT NULL, PRIMARY KEY (wishes_id) )";
$query4 = "CREATE TABLE settings ( settings_id INT UNSIGNED NOT NULL AUTO_INCREMENT, name VARCHAR(10) NOT NULL, content VARCHAR(10) NOT NULL, PRIMARY KEY (settings_id) )";

$query5= "INSERT INTO admin ( admin_id, admin_name, pass_word) VALUE ('1', 'ibk_admin', SHA('aishenghuoiBeiKe'))";
$query6= "INSERT INTO settings ( name, content ) VALUES ('length', '20'),('limit','50'),('width','550'),"
		."('height', '450'),('speed','100'),('tcolor','fbe70e'),('hicolor','336699'),('bgcolor','6B0000'),"
		."('trans','false'),('wish_num','10'),('user_num','10'),('size','3.0')";

$mysql=mysql_query($query1);
$mysql=mysql_query($query2);
$mysql=mysql_query($query3);
$mysql=mysql_query($query4);
$mysql=mysql_query($query5);
$mysql=mysql_query($query6);

include ('./includes/footer.html');
?>