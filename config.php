<?php
@session_start();

//DATABASE DETAILS 
//SET HOSTNAME
$db_hostname = "localhost";	

//MYSQL USERNAME
$db_username ="root";	

//MYSQL PASSWORD
$db_password="";

//MYSQL DATABASE NAME
$db_name="bharatcode";


/*SET THE DEFAULT PAGE PER RECORD LIMIT*/
if(!isset($_SESSION['pagerecords_limit']))
{
	$_SESSION['pagerecords_limit']=20;
}

/*DEFINE CONSTANT FOR THE SITE */

// TABLE PREFIX
define("TABLE_PREFIX","bh_");	//DATABASE TABLE PREFIX IF YOU HAVE SET LIKE : hm_user_master. => "bh_" otherwise leave it blank.

// SECRET KEY FOR PASSWORD ENCRYPT
define("SECRET_KEY","BhArAt383PArmAr");	/*IMPORTANT*/

?>