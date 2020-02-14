<?php
class BGGAjax {
	function __construct() {

		add_action( 'wp_ajax_bggAuth', [$this, 'bggAuth'] );
		add_action( 'wp_ajax_nopriv_bggAuth', [$this, 'bggAuth'] );

		add_action( 'wp_ajax_bggSaveToken', [$this, 'bggSaveToken'] );
		add_action( 'wp_ajax_nopriv_bggSaveToken', [$this, 'bggSaveToken'] );
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
		echo json_encode(['status'=>$api_status,'authUrl'=>$authUrl,'client_id'=>$client_id,'client_secret'=>$client_secret,'access_type'=>$access_type,'scope'=>$scope,'response_type'=>$response_type]);
		wp_die();
	}

	function bggSaveToken(){
		echo "<pre>";print_r($_POST);echo "</pre>";exit();
		wp_die();
	}
}
new BGGAjax();