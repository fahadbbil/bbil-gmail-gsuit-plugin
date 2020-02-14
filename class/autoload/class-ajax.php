<?php
class BGGAjax {
	function __construct() {

		add_action( 'wp_ajax_bggAuth', [$this, 'bggAuth'] );
		add_action( 'wp_ajax_nopriv_bggAuth', [$this, 'bggAuth'] );

		add_action( 'wp_ajax_bggSaveToken', [$this, 'bggSaveToken'] );
		add_action( 'wp_ajax_nopriv_bggSaveToken', [$this, 'bggSaveToken'] );

		add_action( 'wp_ajax_bggSendMsg', [$this, 'bggSendMsg'] );
		add_action( 'wp_ajax_nopriv_bggSendMsg', [$this, 'bggSendMsg'] );
	}

	function bggAuth() {
		// echo "<br>";print_r($_POST);echo "</pre>";exit();
		$client_id 		= $_POST['client_id'];
		$client_secret 	= $_POST['client_secret'];
		$redirect_uri 	= $_POST['redirect_uri'];
		$access_type 	= $_POST['access_type'];
		$scope 			= $_POST['scope'];
		$response_type 	= $_POST['response_type'];

		$authUrl = "https://accounts.google.com/o/oauth2/auth?response_type=".$response_type."&access_type=".$access_type."&client_id=".$client_id."&redirect_uri=".$redirect_uri."&state&scope=".$scope."&approval_prompt=force";
		$insertCred = BGGDb::bggSetCred($client_id,$client_secret);
		$api_status = 200;
		echo json_encode(['status'=>$api_status,'authUrl'=>$authUrl]);
		wp_die();
	}

	function bggSaveToken(){
		// echo "<pre>";print_r($_POST);echo "</pre>";exit();
		global $wpdb;
		$query = $wpdb->insert($wpdb->prefix.'bgg_tokens', array(
			'access_token'		=>	json_encode(array("access_token"=>$_POST['access_token'],"expires_in"=>$_POST['expires_in'],"refresh_token"=>$_POST['refresh_token'],"scope"=>$_POST['scope'],"token_type"=>$_POST['token_type'],"created_at"=>$_POST['created'])),
			'expires_in'		=>	$_POST['expires_in'],
			'refresh_token'		=>	$_POST['refresh_token'],
			'scope'				=>	$_POST['scope'],
			'token_type'		=>	$_POST['token_type'],
			'created_at'		=>	$_POST['created'],
		));
		if ($query == 1) {
			echo json_encode(['status'=>200]);
		} else {
			echo json_encode(['status'=>401]);
		}
		wp_die();
	}

	function bggSendMsg(){
		// echo "<pre>";print_r($_POST);echo "</pre>";exit();
		try{
			$getTokensData = BGGHelper::bggGetTokensData();
			$getTokensData = $getTokensData['0'];
			$token = $getTokensData->access_token;
			$refresh_token = $getTokensData->refresh_token;

			$credData = BGGHelper::bggGetCredData();

			$setToken = BGGHelper::bggSetAccessToken($token,$refresh_token,$credData);
			// echo "<pre>";print_r($setToken);echo "</pre>";exit();

	        $service = new Google_Service_Gmail($setToken);

	        $boundary = uniqid(rand(), true);
	        $subjectCharset = $charset = 'utf-8';

	        $from_email='mail2technerd@gmail.com';
	        $from_name="Test";
	        $to_email = $_POST['email'];
	        

	        // $EmailBody = SendMails::encodeText($EmailBody); // Encoding special characters
	        $EmailBody = $_POST['message']; // Encoding special characters


	        /*$strRawMessage = "From: Email <$from_email> \r\n";
	        $strRawMessage .= "To:" .  implode(',',$to_email)  . "\r\n";
	        if(count($cc_email)!=0){
	            $strRawMessage .= "Cc:" .  implode(',',$cc_email)  . "\r\n";
	        }
	        if(count($bcc_email)!=0){
	            $strRawMessage .= "Bcc:" .  implode(',',$bcc_email)  . "\r\n";
	        }
	        $strRawMessage .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
	        $strRawMessage .= "MIME-Version: 1.0\r\n";
	        $strRawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
	        $strRawMessage .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
	        $strRawMessage .= "$EmailBody\r\n";*/

	        $strRawMessage = "From: Email <$from_email> \r\n";
	        $strRawMessage .= "To:" .  $to_email  . "\r\n";
	        
	        

	        $strRawMessage .= 'Subject: =?' . $subjectCharset . '?B?' . base64_encode($_POST['subject']) . "?=\r\n";
	        $strRawMessage .= 'MIME-Version: 1.0' . "\r\n";
	        $strRawMessage .= 'Content-type: Multipart/Mixed; boundary="' . $boundary . '"' . "\r\n";
	        $strRawMessage .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";

	        // if(count($attachments) !=0){ // Adding attachment (if any)
	        //     $filePath = implode(',',$attachments);
	        //     $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
	        //     $mimeType = finfo_file($finfo, $filePath);
	        //     $fileName = '';
	        //     foreach($attachments as $attachment){
	        //         $fileInfo = explode('/',$attachment);
	        //         $fileName .= $extension = end($fileInfo).',';
	        //     }
	        //     $fileData = base64_encode(file_get_contents($filePath));

	        //     $strRawMessage .= "\r\n--{$boundary}\r\n";
	        //     $strRawMessage .= 'Content-Type: '. $mimeType .'; name="'. $fileName .'";' . "\r\n";
	        //     $strRawMessage .= 'Content-ID: <' . $from_email . '>' . "\r\n";
	        //     $strRawMessage .= 'Content-Description: ' . $fileName . ';' . "\r\n";
	        //     $strRawMessage .= 'Content-Disposition: attachment; filename="' . $fileName . '"; size=' . filesize($filePath). ';' . "\r\n";
	        //     $strRawMessage .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
	        //     $strRawMessage .= chunk_split(base64_encode(file_get_contents($filePath)), 76, "\n") . "\r\n";
	        //     $strRawMessage .= '--' . $boundary . "\r\n";
	        // }

	        $strRawMessage .= "\r\n--{$boundary}\r\n";
	        $strRawMessage .= 'Content-Type: text/plain; charset=' . $charset . "\r\n";
	        $strRawMessage .= 'Content-Transfer-Encoding: 7bit' . "\r\n\r\n";

	        $strRawMessage .= "--{$boundary}\r\n";
	        $strRawMessage .= 'Content-Type: text/html; charset=' . $charset . "\r\n";
	        $strRawMessage .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
	        $strRawMessage .= $EmailBody . "\r\n";


	        //$mime = rtrim(strtr(base64_encode($strRawMessage), '+/', '-_'), '=');
	        $mime = strtr(base64_encode($strRawMessage), '+/', '-_');
	        $msg = new Google_Service_Gmail_Message();
	        $msg->setRaw($mime);
	        $service->users_messages->send("me", $msg);

	        echo json_encode(["status"=>200]);
	    }catch (\Exception $exception){
	        echo $exception->getMessage();
	    }
		wp_die();
	}
}
new BGGAjax();