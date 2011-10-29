<?php
/*
	Name: Wishes_Wall
	URI:  http://unixoss.com/?q=wishes
	Description: Wishes wall using a flash based Tag Cloud.
	Version: 1.0
	Author: Liu Bing
	Author URI: http://www.unixoss.com
*/
$page_title = '设置';
include ('./includes/header.html');
require_once ('../mysql_connect.php');
$options = array (
	'width' => '550',
	'height' => '375',
	'tcolor' => 'ffffff',
	'tcolor2' => 'ffffff',
	'hicolor' => 'ffffff',
	'bgcolor' => '333333',
	'speed' => '100',
	'trans' => 'false',
	'distr' => 'true'
);
include ('./includes/footer.html');
?>