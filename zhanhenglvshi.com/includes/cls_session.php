<?php

if (!defined('IN_ECS')){die('Hacking attempt');}

class cls_session
{   private $db             = NULL;
	private $max_life_time  = 1800;
    private $session_table  = '';
	private $session_name   = 'ssid';
	private $sesskey     = '';

	private $session_expiry = '';
	private $session_md5    = '';

	private $session_cookie_path   = '/';
	private $session_cookie_domain = '';

	private $_ip   = '';
	private $_time = 0;


	public function __construct(&$db,$session_table){


		$GLOBALS['_SESSION'] = array();

		$this->_ip = $this->get_client_ip();
		$this->_time = time();
		$this->session_table      = $session_table;
		$this->db      = &$db;

		if (!empty($_COOKIE[$this->session_name]) || !empty($_GET[$this->session_name])){
			$tmp_sesskey = isset($_COOKIE[$this->session_name])?addslashes($_COOKIE[$this->session_name]):addslashes($_GET[$this->session_name]);
			$sess_id = substr($tmp_sesskey, 0, 32);
			if ($this->create_session_key($sess_id) == substr($tmp_sesskey, 32)){
				$this->sesskey = $sess_id;
			}
		}

		if ($this->sesskey){
			$this->load_session();
		}else{
			$this->create_sesskey();
			setcookie($this->session_name, $this->sesskey . $this->create_session_key($this->sesskey), 0, $this->session_cookie_path, $this->session_cookie_domain, false);
		}

		register_shutdown_function(array(&$this, 'close_session'));
	}

	public function create_sesskey(){
		$this->sesskey = md5(uniqid(mt_rand(), true));
		return $this->insert_session();
	}

	public function create_session_key($sesskey){
		$user_agent = !empty($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
		return sprintf('%08x', crc32( $user_agent . $sesskey ));
	}

	public function insert_session(){
		return $this->db->query("INSERT INTO ". $this->session_table ." (sesskey, expiry, ip, data) VALUES ('" . $this->sesskey . "', '". $this->_time ."', '". $this->_ip ."', 'a:0:{}')");
	}

	public function load_session(){
		global $auth_key;

		$session = $this->db->getRow("SELECT data, expiry FROM ". $this->session_table ." WHERE sesskey = '" . $this->sesskey . "'");
		if (empty($session)){
			$this->insert_session();
			$this->session_expiry = 0;
			$this->session_md5    = '801a1227336511726b75516e921156c9';
			$GLOBALS['_SESSION']  = array();
		}else{
			$this->session_md5    = md5($auth_key.$session['data']);
			$this->session_expiry = $session['expiry'];

			if($this->_time - $session['expiry'] <= $this->max_life_time){
				if (!empty($session['data'])){
				
					$GLOBALS['_SESSION']  = unserialize($session['data']);
				}
			}
		}
	}



	public function update_session(){
		global $auth_key;
		$data = serialize($GLOBALS['_SESSION']);
		if ($this->session_md5 == md5($auth_key.$data) && $this->_time < $this->session_expiry + 60){
			return true;
		}
		
		$data = addslashes($data);
		return $this->db->query("UPDATE ". $this->session_table ." SET expiry = '" . $this->_time . "', data = '$data' WHERE sesskey = '" . $this->sesskey . "'");
	}

	public function close_session(){
		$this->update_session();
		if (($this->_time % 10) == 0){
			$this->db->query("delete FROM ". $this->session_table ." WHERE expiry < " . ($this->_time - $this->max_life_time));
		}
		return true;
	}

	public function get_client_ip() {
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
		foreach ($matches[0] AS $xip) {
			if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
				$ip = $xip;
				break;
			}
		}
	}
	return $ip;
}



}

?>