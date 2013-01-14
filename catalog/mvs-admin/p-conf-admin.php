<?php
	
	$REQUEST_URI_ADMIN = str_replace("p-conf-admin.php", "", $_SERVER["REQUEST_URI"]);
	$REQUEST_URI = str_replace("mvs-admin/p-conf-admin.php", "", $_SERVER["REQUEST_URI"]);	
	$SCRIPT_FILENAME = str_replace("mvs-admin/p-conf-admin.php", "", $_SERVER["SCRIPT_FILENAME"]);
	$SCRIPT_FILENAME_ADMIN = str_replace("p-conf-admin.php", "", $_SERVER["SCRIPT_FILENAME"]);
	
	
	echo "<h1>includes/configure.php</h1>";
	
	echo "define('HTTP_SERVER', 'http://".$_SERVER["SERVER_NAME"]."');<br>";
	echo "define('HTTP_CATALOG_SERVER', 'http://".$_SERVER["SERVER_NAME"]."');<br>";
	echo "define('HTTPS_CATALOG_SERVER', 'http://".$_SERVER["SERVER_NAME"]."');<br>";
	echo "define('ENABLE_SSL_CATALOG', 'false');<br>";
	echo "define('DIR_FS_DOCUMENT_ROOT', '".$SCRIPT_FILENAME."');<br>";
	echo "define('DIR_WS_ADMIN', '".$REQUEST_URI_ADMIN."');<br>";
	echo "define('DIR_FS_ADMIN', '".$SCRIPT_FILENAME_ADMIN."');<br>";
	echo "define('DIR_WS_CATALOG', '".$REQUEST_URI."');<br>";
	echo "define('DIR_FS_CATALOG', '".$SCRIPT_FILENAME."');<br>";
	echo "define('DIR_WS_IMAGES', 'images/');<br>";
	echo "define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/');<br>";
	echo "define('DIR_WS_CATALOG_IMAGES', DIR_WS_CATALOG . 'images/');<br>";
	echo "define('DIR_WS_INCLUDES', 'includes/');<br>";
	echo "define('DIR_WS_BOXES', DIR_WS_INCLUDES . 'boxes/');<br>";
	echo "define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');<br>";
	echo "define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');<br>";
	echo "define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/');<br>";
	echo "define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/');<br>";
	echo "define('DIR_WS_CATALOG_LANGUAGES', DIR_WS_CATALOG . 'includes/languages/');<br>";
	echo "define('DIR_FS_CATALOG_LANGUAGES', DIR_FS_CATALOG . 'includes/languages/');<br>";
	echo "define('DIR_FS_CATALOG_IMAGES', DIR_FS_CATALOG . 'images/');<br>";
	echo "define('DIR_FS_CATALOG_MODULES', DIR_FS_CATALOG . 'includes/modules/');<br>";
	echo "define('DIR_FS_BACKUP', DIR_FS_ADMIN . 'backups/');<br>";
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