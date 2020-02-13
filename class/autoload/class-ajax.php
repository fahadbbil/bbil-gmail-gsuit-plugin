<?php
class BGGAjax {
	function __construct() {

		add_action( 'wp_ajax_authenticateWithCred', [$this, 'authenticateWithCred'] );
		add_action( 'wp_ajax_nopriv_authenticateWithCred', [$this, 'authenticateWithCred'] );
	}

	function authenticateWithCred() {
		// echo "<br>";print_r($_POST);echo "</pre>";exit();
		$client_id = $_POST['client_id'];
		$client_secret = $_POST['client_secret'];
		$redirect_uri = $_POST['redirect_uri'];

		if ($client_id != "" && $client_secret != "" && $redirect_uri != "") {
			$api_status = 200;
			$authUrl = "https://signin.infusionsoft.com/app/oauth/authorize?client_id=".$_POST['client_id']."&client_secret=".$_POST['client_secret']."&redirect_uri=".$_POST['redirect_uri']."&response_type=code&scope=full";
			
		} else {
			$api_status = 401;
		}
		echo json_encode(['status'=>$api_status,'client_id'=>@$client_id,'client_secret'=>@$client_secret,'authUrl'=>@$authUrl]);
		wp_die();
	}
}
new BGGAjax();