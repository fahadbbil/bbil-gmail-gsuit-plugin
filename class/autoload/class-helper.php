<?php 
class BGGHelper {

	public static function bggGetCredData(){
		$getCredData = BGGDb::getTableData('bgg_credentials');
		return $getCredData['0'];
	}

	public static function bggGetTokensData(){
		$getTokensData = BGGDb::getTableData('bgg_tokens');
		return $getTokensData;
	}

	public static function bggGetAuthToken($client_id,$client_secret,$code){
		$google 	= new Google_Client();

		$redirect_uri = site_url()."/wp-admin/admin.php?page=bbil-gmail-gsuit";

		$google->setClientId($client_id);
		$google->setClientSecret($client_secret);
		$google->setRedirectUri($redirect_uri);
		$google->setAccessType('offline'); //Added for Refresh Token
		$google->setApprovalPrompt('force'); //Added for Refresh Token

		// We only need permissions to compose and send emails
		$google->addScope('https://www.googleapis.com/auth/gmail.compose');
	    return $token = $google->fetchAccessTokenWithAuthCode($_GET['code']);
	    // echo "<pre>";print_r($token);echo "</pre>";
	}

	public static function bggSetAccessToken($token,$refresh_token,$credData){
		// echo "<pre>";print_r($credData);echo "</pre>";exit();
		$client_id		= $credData->client_id;
        $client_secret 	= $credData->client_secret;
        $redirect_uri 	= site_url()."/wp-admin/admin.php?page=bbil-gmail-gsuit";
		
		$google = new Google_Client();
		$google->setClientId($client_id);
        $google->setClientSecret($client_secret);
        $google->setRedirectUri($redirect_uri);
        $google->setAccessType('offline'); //Added for Refresh Token
        $google->setApprovalPrompt('force'); //Added for Refresh Token

        // We only need permissions to compose and send emails
        $google->addScope('https://www.googleapis.com/auth/gmail.compose');

		if ($token != '') {
            $google->setAccessToken($token);

            if($google->isAccessTokenExpired()) {

                if($refresh_token!=''){
                    $google->refreshToken($refresh_token);
                }

                $new_token=$google->getAccessToken();
            }
        }
        return $google;
	}

	public static function bgg_send_mail(){
		$getTokensData = $this->bggGetTokensData();
		$getTokensData = $getTokensData['0'];
		$token = $getTokensData->access_token;
		$refresh_token = $getTokensData->refresh_token;

		$credData = $this->bggGetCredData();
		$setToken = $this->bggSetAccessToken($token,$refresh_token,$credData);
		return $setToken;
	} 
}
new BGGHelper();