<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://blubirdinteractive.com/
 * @since      1.0.0
 *
 * @package    Bbil_Gmail_Gsuit
 * @subpackage Bbil_Gmail_Gsuit/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
$getTokensData = BGGHelper::bggGetTokensData();
if (empty($getTokensData)) {

	if (!isset($_GET['code'])) {?>
		<div class="ownerAppDetails">
			<div class="form-group">
				<input type="text" placeholder="Client Id" id="client_id" value="782861306546-l28refg581e0bv4sa5kqqffc1itb7ekt.apps.googleusercontent.com">
			</div>
			<div class="form-group">
				<input type="text" placeholder="Client Secret" id="client_secret" value="DTWlTVEvS40GXmjIxI63sKET">
			</div>
			<div class="form-group">
				<input type="submit" value="Connect" id="connectApp" class="btn btn-primary">
			</div>
		</div>

		<script>
			(function($){
				var ajaxUrl =   "<?php echo admin_url('admin-ajax.php'); ?>";
				$(document).on("click","#connectApp",function(){
					var client_id 		= $("#client_id").val();
					var client_secret 	= $("#client_secret").val();
					var access_type 	= "offline";
					var response_type 	= 'code';
					var scope 			= "https://www.googleapis.com/auth/gmail.compose";
					var redirect_uri 	= "<?php echo site_url(); ?>/wp-admin/admin.php?page=bbil-gmail-gsuit";

					var ajaxData = {
			          'action'  		: 'bggAuth',
			          'client_id'  		: client_id,
			          'client_secret'	: client_secret,
			          'access_type'		: access_type,
			          'response_type'	: response_type,
			          'scope'			: scope,
			          'redirect_uri'	: redirect_uri
			        };

			        $.ajax({
						url: ajaxUrl,
						method: 'POST',
						data: ajaxData,
						success: function ( data ) {
							// console.log(data);
							var obj = JSON.parse(data);
		                	var app_status = obj.status;
		                	var authUrl = obj.authUrl;
		                	window.location.href = authUrl;
						},
		                error: function(e) {
		                	// alert("Something Went Wrong! Please try again later");
		               		console.log(e);
		                }
	            	});
				});
			})(jQuery);
		</script>
			
		
	<?php	
	}else {
		$credData 			= BGGHelper::bggGetCredData();
		$client_id 			= $credData->client_id;
		$client_secret 		= $credData->client_secret;	
		$code 				= $_GET['code']; 
		$token = BGGHelper::bggGetAuthToken($client_id,$client_secret,$code);
		// echo "<pre>";print_r($token);echo "</pre>";exit();
		if ($token['access_token']) {
		?>
			<script>
				(function($){
					$(document).on('ready',function(){
						var ajaxUrl 		=   "<?php echo admin_url('admin-ajax.php'); ?>";
						var access_token 	=   "<?php  echo $token['access_token']; ?>";
						var expires_in 		=   "<?php echo $token['expires_in']; ?>";
						var refresh_token 	=   "<?php echo $token['refresh_token']; ?>";
						var scope 			=   "<?php echo $token['scope']; ?>";
						var token_type 		=   "<?php echo $token['token_type']; ?>";
						var created 		=   "<?php echo $token['created']; ?>";

						var ajaxData = {
				          'action'  		: 'bggSaveToken',
				          'access_token'  	: access_token,
				          'expires_in'		: expires_in,
				          'refresh_token'	: refresh_token,
				          'scope'			: scope,
				          'token_type'		: token_type,
				          'created'			: created
				        };

				        $.ajax({
							url: ajaxUrl,
							method: 'POST',
							data: ajaxData,
							success: function ( data ) {
								// console.log(data);
								window.location.href = "<?php echo site_url(); ?>/wp-admin/admin.php?page=bbil-gmail-gsuit";
							},
			                error: function(e) {
			                	// alert("Something Went Wrong! Please try again later");
			               		console.log(e);
			                }
	            		});
					});
				})(jQuery);
			</script>
	<?php
		}	
	}
} else { ?>

<div class="ownerAppDetails">
	<div class="form-group">
		<input type="text" placeholder="Name" id="email_name" value="Test Name">
	</div>
	<div class="form-group">
		<input type="text" placeholder="Email" id="email_email" value="mail2technerd@gmail.com">
	</div>
	<div class="form-group">
		<input type="text" placeholder="Subject" id="email_subject" value="Great News">
	</div>
	<div class="form-group">
		<textarea placeholder="Message" id="email_message" cols="30" rows="10"></textarea>
	</div>
	<div class="form-group">
		<input type="submit" value="Send" id="sendMsg" class="btn btn-primary">
	</div>
</div>

<script>
	(function($){
		$("#sendMsg").on("click",function(){
			var ajaxUrl =   "<?php echo admin_url('admin-ajax.php'); ?>";
			var name 	= $("#email_name").val();
			var email 	= $("#email_email").val();
			var subject = $("#email_subject").val();
			var message = $("#email_message").val();

			var ajaxData = {
	          'action'  : 'bggSendMsg',
	          'name'  	: name,
	          'email'	: email,
	          'subject'	: subject,
	          'message'	: message
	        };

	        $.ajax({
				url: ajaxUrl,
				method: 'POST',
				data: ajaxData,
				success: function ( data ) {
					// console.log(data);
					var obj = JSON.parse(data);
                	var app_status = obj.status;
                	if (app_status == 200) {
                		alert("Mail Sent Successfully")
                	} else {
                		alert(data);
                	}
				},
                error: function(e) {
                	// alert("Something Went Wrong! Please try again later");
               		console.log(e);
                }
    		});
		});
	})(jQuery);
</script>
<?php	
}