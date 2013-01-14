<?php
	
	$REQUEST_URI = str_replace("p-conf.php", "", $_SERVER["REQUEST_URI"]);
	$SCRIPT_FILENAME = str_replace("p-conf.php", "", $_SERVER["SCRIPT_FILENAME"]);
	
	echo "<h1>includes/configure.php</h1>";
	
	echo "define('HTTP_SERVER', 'http://".$_SERVER["SERVER_NAME"]."');<br>";
	echo "define('HTTPS_SERVER', 'http://".$_SERVER["SERVER_NAME"]."');<br>";
	echo "define('ENABLE_SSL', false);<br>";
	echo "define('HTTP_COOKIE_DOMAIN', '');<br>";
	echo "define('HTTPS_COOKIE_DOMAIN', '');<br>";
	echo "define('HTTP_COOKIE_PATH', '".$REQUEST_URI."');<br>";
	echo "define('HTTPS_COOKIE_PATH', '".$REQUEST_URI."');<br>";
	echo "define('DIR_WS_HTTP_CATALOG', '".$REQUEST_URI."');<br>";
	echo "define('DIR_WS_HTTPS_CATALOG', '".$REQUEST_URI."');<br>";
	echo "define('DIR_WS_IMAGES', 'images/');<br>";
	echo "define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/');<br>";
	echo "define('DIR_WS_INCLUDES', 'includes/');<br>";
	echo "define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');<br>";
	echo "define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');<br>";
	echo "define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/');<br>";
	echo "define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/');<br>";
	
	echo '<br>';
	
	echo "define('DIR_WS_DOWNLOAD_PUBLIC', 'pub/');<br>";
	echo "define('DIR_FS_CATALOG', '".$SCRIPT_FILENAME."');<br>";
	echo "define('DIR_FS_DOWNLOAD', DIR_FS_CATALOG . 'down-load/');<br>";
	echo "define('DIR_FS_DOWNLOAD_PUBLIC', DIR_FS_CATALOG . 'pub/');<br>";
	
	echo '<br>';
		
	echo "define('DB_SERVER', 'database-server');<br>";
	echo "define('DB_SERVER_USERNAME', 'database-username');<br>";
	echo "define('DB_SERVER_PASSWORD', 'database-password');<br>";
	echo "define('DB_DATABASE', 'database-name');<br>";
	echo "define('USE_PCONNECT', 'false');<br>";
	echo "define('STORE_SESSIONS', 'mysql');<br>";
	echo "define('CFG_TIME_ZONE', 'America/Chicago');<br>";
?>