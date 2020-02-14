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

$credData = BGGHelper::bggGetCredData();

$google = new Google_Client();

$google->setClientId($credData->client_id);
$google->setClientSecret($credData->client_secret);
$google->setRedirectUri("http://localhost/cee-wp/wp-admin/admin.php?page=bbil-gmail-gsuit");
$google->setAccessType('offline'); //Added for Refresh Token
$google->setApprovalPrompt('force'); //Added for Refresh Token

// We only need permissions to compose and send emails
$google->addScope('https://www.googleapis.com/auth/gmail.compose');

$auth_url = $google->createAuthUrl();
if (!isset($_GET['code'])) {
	echo "<a href='".$auth_url."'>Click Here</a>";
}else {
    $token = $google->fetchAccessTokenWithAuthCode($_GET['code']);
    echo "<pre>";print_r($token);echo "</pre>";
}