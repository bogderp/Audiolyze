<?php
if (isset($_POST)) {
	$to = 'support@spotigraphbeta.com'; 
	$subject = 'New Feature Request';
	$message = 'Name: ' . $_POST['submitName'] . "\r\n\r\n";
	$message .= $_POST['submitRequest'];
	$headers = "From: bogdanpozderca@spotigraphbeta.com\r\n";
	$headers .= 'Content-Type: text/plain; charset=utf-8';
	$email = filter_input(INPUT_POST, 'submitEMail', FILTER_VALIDATE_EMAIL);
	if ($email) {
	   $headers .= "\r\nReply-To: $email";
	}
	$success = mail($to, $subject, $message, $headers, '‑bogdanpozderca@gmail.com');
	echo $success;
}
?>