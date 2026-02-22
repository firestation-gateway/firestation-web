<?php
  define('ROOT_DIR', '');
  require_once(ROOT_DIR.'includes/loader.php');
  require_once(ROOT_DIR.'includes/partials/header.php');

  $uploadSuc = "";
  $uploadErr = "";
  if ($_SERVER["REQUEST_METHOD"] == "POST")
  {
    if (isset($_POST["CMD"]) && $_POST['CMD'] == 'UPLOAD')
    {
      if (isset($_FILES['fileToUpload'])) 
      {
        if (empty($_FILES["fileToUpload"]["tmp_name"])) {
            $uploadErr = "Fehler: Keine Datei ausgewählt.";
        } else {
            $cfg = loadAppConfigFile($_FILES["fileToUpload"]["tmp_name"]);
            $check = false;
            if (is_array($cfg)) {
                $check = checkAppConfig($cfg);
            }
            
            # TODO: check for valid file
            if ($cfg == false || $check == false) {
                $uploadErr = "Fehler: Ungültige Datei";
            } else {
                $result = saveAppConfig($cfg);
                if ($result == true) {
                    $uploadSuc = "Konfiguration gespeichert.";
                    serviceRestart();
                    # header("refresh: 3;");
                } else {
                    $uploadErr = "Fehler: Konfiguration NICHT gespeichert. (interner Fehler)";
                }
            }
        }
      }
    }
  }
?>

<h3><center>Konfiguration</center></h3>
  <br>
  <hr>
  <h4>Hochladen:</h4>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
    <input type="submit" value="Upload Konfiguration" name="submit">
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type='hidden' name='CMD' value='UPLOAD'>
    <span class="message-error"><?php echo $uploadErr;?></span>
    <span class="message-success"><?php echo $uploadSuc;?></span>
  </form>
  <hr>
  <h4>Runterladen:</h4>
  <form action="/download.php" method="post" enctype="multipart/form-data">        
    <input type="submit" value="Download Konfiguration" name="submit">
    <input type='hidden' name='CMD' value='DOWNLOAD'>

    <!--<input type='hidden' name='FROM_PAGE' value='<?php basename($_SERVER['REQUEST_URI']);?>'>-->
  </form>
  <hr>
<!-- 
  <pre><?php print_r($app_config) ?></pre>
  <pre><?php var_dump($app_config) ?></pre> -->
  <h4>Aktive Konfiguration:</h4>
  <pre><?php $str = file_get_contents(getAppConfigFileName()); print_r($str); ?></pre>

<?php require_once(ROOT_DIR.'includes/partials/footer.php'); ?>