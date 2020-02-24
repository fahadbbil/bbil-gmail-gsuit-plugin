<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://blubirdinteractive.com/
 * @since             1.0.0
 * @package           Bbil_Gmail_Gsuit
 *
 * @wordpress-plugin
 * Plugin Name:       BBIL-Gmail-Gsuit
 * Plugin URI:        https://blubirdinteractive.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            BBIL
 * Author URI:        https://blubirdinteractive.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bbil-gmail-gsuit
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BBIL_GMAIL_GSUIT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bbil-gmail-gsuit-activator.php
 */
function activate_bbil_gmail_gsuit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bbil-gmail-gsuit-activator.php';
	Bbil_Gmail_Gsuit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bbil-gmail-gsuit-deactivator.php
 */
function deactivate_bbil_gmail_gsuit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bbil-gmail-gsuit-deactivator.php';
	Bbil_Gmail_Gsuit_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bbil_gmail_gsuit' );
register_deactivation_hook( __FILE__, 'deactivate_bbil_gmail_gsuit' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bbil-gmail-gsuit.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bbil_gmail_gsuit() {

	$plugin = new Bbil_Gmail_Gsuit();
	$plugin->run();

}
run_bbil_gmail_gsuit();

// Class files load
$classes = glob(plugin_dir_path( __FILE__ ).'class/autoload/*.php');
if ($classes) {
    foreach ($classes as $class) {
        require_once $class;
    }
}

require_once plugin_dir_path( __FILE__ ).'lib/vendor/autoload.php';

/*
Template Tag to send email
*/
function bgg_send_mail(){
	$bgg_send_mail = BGGHelper::bgg_send_mail();
}

/*
*/
if(!function_exists('wp_mail')){
    
    function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
            // Compact the input, apply the filters, and extract them back out

            /**
             * Filters the wp_mail() arguments.
             */
            $atts = apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );
            // echo "<pre>";print_r($atts);echo "</pre>";exit();
            if ( isset( $atts['to'] ) ) {
                    $to = $atts['to'];
            }

            if ( isset( $atts['subject'] ) ) {
                    $subject = $atts['subject'];
            }

            if ( isset( $atts['message'] ) ) {
                    $message = $atts['message'];
            }
            if ( isset( $atts['headers'] ) ) {
                    $headers = $atts['headers'];
            }

            if ( isset( $atts['attachments'] ) ) {
                    $attachments = $atts['attachments'];
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

		        return true;
		    }catch (\Exception $exception){
		        // echo $exception->getMessage();
		        $exception->getMessage();
		        return false;
		    }
    } 
}
