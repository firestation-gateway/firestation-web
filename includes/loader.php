<?php

session_start();

# Imports and initializations
require_once(ROOT_DIR.'includes/models.php');
require_once(ROOT_DIR.'includes/functions.php');


# Check database
checkDatabaseInstallation();

# Check logged user
if (!isLoggedIn() && !preg_match('/login(.php)?/', currentPage())) {
  redirectTo(ROOT_DIR.'login.php');
}

$app_config = loadAppConfig();

// $config = load_config($config_file);
// $config_generated = false;
// if ($config == false) {
//   $config = get_initial_config();
//   $config_generated = true;
//   save_config($config_file, $config);
// }


?>
