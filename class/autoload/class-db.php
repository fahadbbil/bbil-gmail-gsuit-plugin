<?php 
class BGGDb{
	
	function __construct(){}

	public static function bggCreateCredTable(){
		global $wpdb;
		$sql = "CREATE TABLE IF NOT EXISTS `". $wpdb->prefix ."bgg_credentials` (
		  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `client_id` varchar(128) NOT NULL ,
		  `client_secret` varchar(128) NOT NULL,
		  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
		);";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    	dbDelta( $sql );
	}
	
	public static function deleteTable($tableName) {
	    global $wpdb;
	    $tableName = $wpdb->prefix .$tableName;
	    $wpdb->query( "DROP TABLE IF EXISTS $tableName" );
	}

	public static function getTableData($tableName) {
	    global $wpdb;
	    $tableName = $wpdb->prefix.$tableName;
	    $sql = "SELECT * FROM ". $tableName;
	    $results = $wpdb->get_results($sql);
		return $results;
	}

	public static function bggSetCred(){
		global $wpdb;
		$client_id = '11363867946-kbnqo5p578qsep3f51c261v47qab2mcd.apps.googleusercontent.com';
		$client_secret = 'GOfPJ7X31dj-tCUIwbXDD29V';
		$query = $wpdb->insert($wpdb->prefix.'bgg_credentials', array(
			'client_id'		=>	$client_id,
			'client_secret'	=>	$client_secret
		));
	}
	public static function firstTimeInsert($data){
		global $wpdb;
		if ($data['access_token'] != "" && $data['token_type'] != "" && $data['expires_in'] != "" && $data['refresh_token'] != "" && $data['scope'] != "")  {
			$wpdb->insert($wpdb->prefix.'infusion_tokenDetails', array(
			    'access_token' => $data['access_token'],
			    'token_type' => $data['token_type'],
			    'expires_in' => $data['expires_in'], 
			    'refresh_token' => $data['refresh_token'], 
			    'scope' => $data['scope'], 
			));
			return 1;
		} else {
			return 0;
		}
	}

	public static function test(){
		return "string";
	}

} new BGGDb();