<?php
error_reporting(E_ALL);

# -------------- GLOBALS --------------
$GLOBALS['errormsg'] = '';

# -------------- LOAD CONF ------------
function getConfig(){
	return parse_ini_file(ROOT_DIR.'conf/config.ini');
}

# ---------- AUTHENTICATION -----------

function &authLoader() {
	if( !isset( $_SESSION[ 'puggy' ] ) ) {
		$_SESSION[ 'puggy' ] = array();
	}
	return $_SESSION['puggy'];
}

function logIn($username, $password) {
	$puggyConf = getConfig();
	if ($username == $puggyConf['wi_user'] & $password == $puggyConf['wi_pass']) {
		$authSession =& authLoader();
		$authSession['puggy'] = $username;
		redirectTo(ROOT_DIR.'/');
	}
	else {
		redirectTo('login.php');
	}
}

function logOut() {
	$authSession =& authLoader();
	unset( $authSession['puggy']);
}

function isLoggedIn() {
	$authSession =& authLoader();
	return isset( $authSession['puggy'] );
}

function currentUser() {
	$authSession =& authLoader();
	return (isset($authSession['puggy']) ? $authSession['puggy'] : '');
}

# ----------- TOKEN SESSION -----------


function newSessionToken(){
	if(isset( $_SESSION['sessiontoken'])){
		destroySessionToken();
	}
	$_SESSION['sessiontoken'] = md5( uniqid() );
}

function destroySessionToken(){
	unset( $_SESSION['sessiontoken']);
}

function checkSessionToken($field_token, $session_token, $redirectUrl){
	if(!isset($field_token) && !isset($session_token) && $field_token !== $session_token){
		redirectTo(ROOT_DIR.$redirectUrl);
	}
}

# -------------- DATABASE --------------

function initDb($puggyConf){
  return mysqli_connect($puggyConf['db_addr'], $puggyConf['db_user'], $puggyConf['db_pass'], $puggyConf['db_name']);
}

function checkDatabaseInstallation(){
	$puggyConf = getConfig();
	if ($puggyConf['db_reqd'] == true) {
		$conn = initDb($puggyConf);
		if (!$conn) {
			redirectError('Could not connect to database. Please, try to <a href="install.php">install</a>.');
		}
		else {
			mysqli_close($conn);
		}
	}
}


# -------------- UTILS --------------

function currentPage() {
	return $_SERVER["PHP_SELF"];
}

function redirectTo($href){
		header( "Location: {$href}" );
	  exit;
}

function redirectError($errid) {
	$_SESSION['err'] = array('date' => date("Y-m-d H:i:s"), 'message' => $errid);
	redirectTo('error.php');
}

function serviceRestart() 
{
    $output = shell_exec("sudo /usr/bin/systemctl restart firestation-gateway.service");
    echo("<h1>".$output."</h1>");
}
function serviceLogs() 
{
    $output = shell_exec("sudo /usr/bin/journalctl -u firestation-gateway.service -n 20 --no-pager");
    return $output;
}

function getAppConfigFileName() {
  $puggyConf = getConfig();
  return $puggyConf['back_config'];
}

function getVersion() {
  $puggyConf = getConfig();
  return $puggyConf['version'];
}

function loadAppConfig() {
  $config_file = getAppConfigFileName();
  return yaml_parse_file($config_file);
}

function loadAppConfigFile($file) {
  return yaml_parse_file($file);
}

# if (file_exists("../config.yaml")) {

function saveAppConfig($config) {
  $config_file = getAppConfigFileName();
  return yaml_emit_file($config_file, $config, YAML_UTF8_ENCODING);
}

function checkAppConfig($cfg)
{
    # TODO: improve config check
    if (!array_key_exists("producers", $cfg)) {
        return false;
    }
    if (!array_key_exists("consumers", $cfg)) {
        return false;
    }
    return true;
}

function generateAppConfig(): array
{
    # TODO: make this more dynamically for new params or modules
    return array (
            'producers' => 
            array (
                0 => 
                array ( 
                    'name' => 'Genius',
                    'type' => 'genius',
                    'events' => 
                    array (
                        'genius_alarm' => NULL,
                        'genius_selftest' => NULL,
                        'genius_idle' => NULL,
                    ),
                    'params' =>
                    array (
                        'line' => 22,
                    ),
                ),
            ),
            'consumers' => 
            array ( 
                0 => 
                array ( 
                    'name' => 'Tetracontrol', 
                    'type' => 'tetracontrol', 
                    'params' => 
                    array ( 
                        'testmode' => true, 
                        'token' => 'TETRACONTROL-TOKEN', 
                        'url' => 'http://',
                    ),
                    'events' => 
                    array ( 
                        'genius_alarm' => 
                        array ( 
                            'type' => '', 
                            'dest' => '', 
                            'text' => '',
                        ),
                        'genius_selftest' => 
                        array ( 
                            'type' => '', 
                            'dest' => '', 
                            'text' => '',
                        ),
                        'genius_idle' => 
                        array ( 
                            'type' => '',
                            'dest' => '',
                            'text' => '',
                        ),
                    ),
                ),
                1 => 
                array ( 
                    'name' => 'Connect', 
                    'type' => 'connect', 
                    'params' => 
                    array ( 
                        'testmode' => true, 
                        'token' => 'FEUERSOFTWARE-TOKEN',
                        'url' => 'https://connectapi.feuersoftware.com',
                    ),
                    'events' => 
                    array ( 
                        'genius_alarm' => 
                        array ( 
                            'ric' => '',
                            'keyword' => '',
                            'facts' => '',
                            'address' =>
                            array (
                                'street' => '',
                                'housenumber' => '',
                                'zipcode'=> '',
                                'city' => '',
                            ),
                        ),
                    ),
                ),
            ),
        );
}

?>
