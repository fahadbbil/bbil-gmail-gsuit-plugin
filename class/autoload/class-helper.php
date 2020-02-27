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

	public static function bgg_customize_wp_mail($args){
		if ( isset( $args['to'] ) ) {
		  $to = $args['to'];
		}

	    if ( isset( $args['subject'] ) ) {
	      $subject = $args['subject'];
	    }

	    if ( isset( $args['message'] ) ) {
	      $message = $args['message'];
	    }
	    if ( isset( $args['headers'] ) ) {
	      $headers = $args['headers'];
	    }

	    if ( isset( $args['attachments'] ) ) {
	      $attachments = $args['attachments'];
	    }

	    if ( ! is_array( $attachments ) ) {
	      $attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
	    }

	    // Headers
	    $cc       = array();
	    $bcc      = array();
	    $reply_to = array();

	    if ( empty( $headers ) ) {
	      	$headers = array();
	    } else {
			if ( ! is_array( $headers ) ) {
			// Explode the headers out, so this function can take both
			// string headers and an array of headers.
			$tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
			} else {
				$tempheaders = $headers;
			}

	      	$headers = array();

			// If it's actually got contents
			if ( ! empty( $tempheaders ) ) {
			      // Iterate through the raw headers
				foreach ( (array) $tempheaders as $header )
				{
					if ( strpos( $header, ':' ) === false ) {
						if ( false !== stripos( $header, 'boundary=' ) ) {
						  $parts    = preg_split( '/boundary=/i', trim( $header ) );
						  $boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
						}
						continue;
					}
					// Explode them out
					list( $name, $content ) = explode( ':', trim( $header ), 2 );

					// Cleanup crew
					$name    = trim( $name );
					$content = trim( $content );

					switch ( strtolower( $name ) ) {
						case 'from':
							$bracket_pos = strpos( $content, '<' );
							if ( $bracket_pos !== false ) {
							// Text before the bracketed email is the "From" name.
								if ( $bracket_pos > 0 ) {
								        $from_name = substr( $content, 0, $bracket_pos - 1 );
								        $from_name = str_replace( '"', '', $from_name );
								        $from_name = trim( $from_name );
								}

							    $from_email = substr( $content, $bracket_pos + 1 );
							    $from_email = str_replace( '>', '', $from_email );
							    $from_email = trim( $from_email );

						  // Avoid setting an empty $from_email.
						  } elseif ( '' !== trim( $content ) ) {
						    $from_email = trim( $content );
						  }
						        break;
						case 'content-type':
							if ( strpos( $content, ';' ) !== false ) {
								list( $type, $charset_content ) = explode( ';', $content );
								$content_type                   = trim( $type );
								if ( false !== stripos( $charset_content, 'charset=' ) ) {
								        $charset = trim( str_replace( array( 'charset=', '"' ), '', $charset_content ) );
								} elseif ( false !== stripos( $charset_content, 'boundary=' ) ) {
								        $boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset_content ) );
								        $charset  = '';
								}

							// Avoid setting an empty $content_type.
							} elseif ( '' !== trim( $content ) ) {
								$content_type = trim( $content );
							}
							break;
						case 'cc':
							$cc = array_merge( (array) $cc, explode( ',', $content ) );
							break;
						case 'bcc':
							$bcc = array_merge( (array) $bcc, explode( ',', $content ) );
							break;
						case 'reply-to':
							$reply_to = array_merge( (array) $reply_to, explode( ',', $content ) );
							break;
						default:
							// Add it to our grand headers array
							$headers[ trim( $name ) ] = trim( $content );
							break;
					}
				}
			}
	    }

	    try{
			$getTokensData = BGGHelper::bggGetTokensData();
			$getTokensData = $getTokensData['0'];
			$token = $getTokensData->access_token;
			$refresh_token = $getTokensData->refresh_token;

			$credData = BGGHelper::bggGetCredData();

			$setToken = BGGHelper::bggSetAccessToken($token,$refresh_token,$credData);

			$service = new Google_Service_Gmail($setToken);

			$boundary = uniqid(rand(), true);
			$subjectCharset = $charset = 'utf-8';

			$from_email= $from_email;
			$from_name= "Test";

			if (is_array($to)) {
				$to_email = $to['0'];
				$cc = array_slice($to,1);
			} else {
				$to_email = $to;
			}

			if (!empty($reply_to)) {
				if (is_array($reply_to)) {
					$reply_to = $reply_to['0'];
				} else {
					$reply_to = $reply_to;
				}
			}

			$reply_to = $reply_to;
			$strSesFromEmail = $from_email;

			$EmailBody = $message; 

			$strRawMessage = "";
			$strRawMessage .= 'From: Email <" '. $strSesFromEmail . ">" . "\r\n";
			$strRawMessage .= "To:" .  $to_email  . "\r\n";

			if(count($cc)!=0){
				$strRawMessage .= "Cc:" .  implode(',',$cc)  . "\r\n";
			}

			if (!empty($reply_to)) {
				$strRawMessage .= "Reply-To:" .  $reply_to  . "\r\n";
			}

			$strRawMessage .= 'Subject: =?' . $subjectCharset . '?B?' . base64_encode($subject) . "?=\r\n";
			$strRawMessage .= 'MIME-Version: 1.0' . "\r\n";
			$strRawMessage .= 'Content-type: Multipart/Mixed; boundary="' . $boundary . '"' . "\r\n";
			$strRawMessage .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";

			if(count($attachments) !=0){ // Adding attachment (if any)
				$filePath = implode(',',$attachments);
				$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
				$mimeType = finfo_file($finfo, $filePath);
				$fileName = '';
				foreach($attachments as $attachment){
				    $fileInfo = explode('/',$attachment);
				    $fileName .= $extension = end($fileInfo).',';
			}
				$fileData = base64_encode(file_get_contents($filePath));

				$strRawMessage .= "\r\n--{$boundary}\r\n";
				$strRawMessage .= 'Content-Type: '. $mimeType .'; name="'. $fileName .'";' . "\r\n";
				$strRawMessage .= 'Content-ID: <' . $from_email . '>' . "\r\n";
				$strRawMessage .= 'Content-Description: ' . $fileName . ';' . "\r\n";
				$strRawMessage .= 'Content-Disposition: attachment; filename="' . $fileName . '"; size=' . filesize($filePath). ';' . "\r\n";
				$strRawMessage .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
				$strRawMessage .= chunk_split(base64_encode(file_get_contents($filePath)), 76, "\n") . "\r\n";
				$strRawMessage .= '--' . $boundary . "\r\n";
			}

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

			function cf7_success_callback($result){
				global $messages;
				$success_message = $messages['mail_sent_ok'];
				$result['status'] = "mail_sent";
				$result['message'] = $success_message;
				return $result;
			}
			
			global $bgg_settings_value;
			if ($bgg_settings_value == 1) {
				add_filter('wpcf7_ajax_json_echo','cf7_success_callback');
			}
	    } catch (\Exception $exception){
			$exception->getMessage();
			function cf7_failed_callback($result){
				global $messages;
				$failed_message = $messages['mail_sent_ng'];
				$result['status'] = "mail_failed";
				$result['message'] = $failed_message;
				return $result;
			}

			global $bgg_settings_value;
			if ($bgg_settings_value == 1) {
 				add_filter('wpcf7_ajax_json_echo','cf7_failed_callback');
			}
	    }
  	}
}
new BGGHelper();