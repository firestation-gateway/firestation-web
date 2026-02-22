<?php
define('ROOT_DIR', '');
require_once(ROOT_DIR.'includes/loader.php');
require_once(ROOT_DIR.'includes/partials/header.php');
?>

<meta http-equiv="refresh" content="5; url="<?php echo $_SERVER['PHP_SELF']; ?>" />	
<h3><center>Backend Logoutput</center></h3>
<pre><?php print_r(serviceLogs()); ?></pre>

<?php require_once(ROOT_DIR.'includes/partials/footer.php'); ?>