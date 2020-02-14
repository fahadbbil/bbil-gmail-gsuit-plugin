<?php 
class BGGDb{
	
	function __construct(){}

	public static function bggCreateCredTable(){
		global $wpdb, $wnm_db_version;
		$sql = array();

		$credTable = $wpdb->prefix ."bgg_credentials";
		$tokenTable = $wpdb->prefix ."bgg_tokens";

		if( $wpdb->get_var("show tables like '". $credTable . "'") !== $credTable ) {
	        $sql[] = "CREATE TABLE ". $credTable . "   (
			        id int(11) NOT NULL AUTO_INCREMENT,
			        client_id varchar(256) NOT NULL,
			        client_secret varchar(128) NOT NULL,
			        updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
			        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			        PRIMARY KEY  (id)
		        ) ";
	    }

	    if( $wpdb->get_var("show tables like '". $tokenTable . "'") !== $tokenTable ) { 

	        $sql[] = "CREATE TABLE ". $tokenTable . "   (
			        id int(11) NOT NULL AUTO_INCREMENT,
			        access_token text NOT NULL,
			        expires_in varchar(128) NOT NULL,
			        refresh_token varchar(128) NOT NULL,
			        scope varchar(128) NOT NULL,
			        token_type varchar(128) NOT NULL,
			        updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
			        created_at int(11) NOT NULL,
			        PRIMARY KEY  (id)
		        ) ";
	    }

		if ( !empty($sql) ) {
	        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	        dbDelta($sql);
	        add_option("wnm_db_version", $wnm_db_version);
    	}
	}
	
	public static function deleteTable($tableName) {
	    global $wpdb;
	    if (is_array($tableName)) {
		    foreach ($tableName as $key => $value) {
		    	$dbTable = $wpdb->prefix .$value;
		    	$wpdb->query( "DROP TABLE IF EXISTS $dbTable" );
		    }
	    }
	}

	public static function getTableData($tableName) {
	    global $wpdb;
	    $tableName = $wpdb->prefix.$tableName;
	    $sql = "SELECT * FROM ". $tableName;
	    $results = $wpdb->get_results($sql);
		return $results;
	}

	public static function bggSetCred($client_id,$client_secret){
		global $wpdb;
		// $client_id = '782861306546-l28refg581e0bv4sa5kqqffc1itb7ekt.apps.googleusercontent.com';
		// $client_secret = 'DTWlTVEvS40GXmjIxI63sKET';
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

} new BGGDb();