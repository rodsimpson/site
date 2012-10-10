<?php
require_once (ROOT_PATH.'birding/sqlDB.class.php');
  
  
define('DB_NAME', 'rodsimp_wordpress'); 
define('DB_USERNAME', 'rodsimp_riomoab'); 
define('DB_PASSWORD', 'Ph0t0M0j0'); 
define('DB_HOST', 'rodsimpson.com');

$sql_db = new sqlDB(DB_USERNAME, DB_PASSWORD, DB_NAME, DB_HOST); 

?>
