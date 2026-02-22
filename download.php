<?php
  define('ROOT_DIR', '');
  require_once(ROOT_DIR.'includes/loader.php');
  require_once(ROOT_DIR.'includes/functions.php');

  if (isLoggedIn()) {
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
      if (isset($_POST["CMD"]) && $_POST['CMD'] == 'DOWNLOAD')
      {
        $file = ROOT_DIR.getAppConfigFileName();
        if (file_exists($file)) {
          echo "# Downloaded configuration file for the Firestation-Gateway!!!\n";
          header('Content-Description: File Transfer');
          header('Content-Type: application/octet-stream');
          header('Content-Disposition: attachment; filename="'.basename($file).'"');
          header('Expires: 0');
          header('Cache-Control: must-revalidate');
          header('Pragma: public');
          header('Content-Length: ' . filesize($file));
          readfile($file);
          unset($_POST['CMD']);
        }
      }
    }
  }
?>