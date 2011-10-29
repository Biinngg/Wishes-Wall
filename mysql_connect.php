<?php # Script 13.4 - mysql_connect.php
/*
	Name: Wishes_Wall
	URI:  http://unixoss.com/?q=wishes
	Description: Wishes wall using a flash based Tag Cloud.
	Version: 1.0
	Author: Liu Bing
	Author URI: http://www.unixoss.com
*/
// This file contains the database access information. 
// This file also establishes a connection to MySQL and selects the database.

// Set the database access information as constants.
DEFINE ('DB_USER', 'wishes');
DEFINE ('DB_PASSWORD', 'wishesdata@mysql');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'wishes');

if ($dbc = mysql_connect (DB_HOST, DB_USER, DB_PASSWORD)) { // Make the connnection.

	if (!mysql_select_db (DB_NAME)) { // If it can't select the database.
	
		// Handle the error.
		trigger_error("Could not select the database!\n<br />MySQL Error: " . mysql_error());
		
		// Print a message to the user, include the footer, and kill the script.
		include ('./includes/footer.html');
		exit();
		
	} // End of mysql_select_db IF.
	
} else { // If it couldn't connect to MySQL.

	// Print a message to the user, include the footer, and kill the script.
	trigger_error("Could not connect to MySQL!\n<br />MySQL Error: " . mysql_error());
	include ('./includes/footer.html');
	exit();
	
} // End of $dbc IF.

// Create a function for escaping the data.
function escape_data ($data) {
	
	// Address Magic Quotes.
	if (ini_get('magic_quotes_gpc')) {
		$data = stripslashes($data);
	}
	
	// Check for mysql_real_escape_string() support.
	if (function_exists('mysql_real_escape_string')) {
		global $dbc; // Need the connection.
		$data = mysql_real_escape_string (trim($data), $dbc);
	} else {
		$data = mysql_escape_string (trim($data));
	}

	// Return the escaped value.	
	return $data;

} // End of function.
?>