<?php

$msg = "";
  if ($_SERVER["REQUEST_METHOD"] == "POST")
  {
    if (isset($_POST["CMD"]) && $_POST['CMD'] == 'UPLOAD')
    {
      if (isset($_FILES['fileToUpload'])) 
      {
        $msg_class = "message-success";
        if (empty($_FILES["fileToUpload"]["tmp_name"])) {
            $msg = "Fehler: Keine Datei ausgewählt.";
            $msg_class = "message-error";
        } else {
            $cfg = load_config($_FILES["fileToUpload"]["tmp_name"]);
            $check = false;
            if (is_array($cfg)) {
                $check = check_config($cfg);
            }
            
            # TODO: check for valid file
            if ($cfg == false || $check == false) {
                $msg = "Fehler: Ungültige Datei";
                $msg_class = "message-error";
            } else {
                $result = save_config($config_file, $cfg);
                if ($result == true) {
                    $msg = "Konfiguration gespeichert.";
                    service_restart();
                    header("refresh: 3;");
                } else {
                    $msg = "Fehler: Konfiguration NICHT gespeichert. (interner Fehler)";
                    $msg_class = "message-error";
                }
            }
        }
      }
    }
  }
  
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    echo "<pre>\r\n";
    echo htmlspecialchars(print_r($_FILES, 1));
    echo "</pre>\r\n";

    if (empty($_FILES["fileToUpload"]["tmp_name"])) {
        echo "Fehler: Keine Datei ausgewählt.</br>";
    } else {
        $cfg = load_config($_FILES["fileToUpload"]["tmp_name"]);
        $check = false;
        if (is_array($cfg)) {
            $check = check_config($cfg);
        }
        
        # TODO: check for valid file
        if ($cfg == false || $check == false) {
            echo "Fehler: Ungültige Datei</br>";
        } else {
            $result = save_config("/tmp/foobar.yaml", $cfg);
            if ($result == true) {
                echo "Konfiguration gespeichert.</br>";
                # service_restart();
            } else {
                echo "Fehler: Konfiguration NICHT gespeichert. (interner Fehler)";
            }
        }
        header("Location: /");
    }
} else {
    echo "Invalid request.";
}

?> 