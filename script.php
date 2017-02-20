<?php
require('PaypalIPN.php');
error_log(print_r($_POST, TRUE));
// use PaypalIPN;
// $ipn = new PaypalIPN();
// Use the sandbox endpoint during testing.
// $ipn->useSandbox();

		$txn_id = $_POST['txn_id'];
		$payer_email = $_POST['payer_email'];
		$receiver_email = $_POST['receiver_email'];
		$item_name = $_POST['item_name'];
	        $custom = explode(",", $_POST['custom']);
		$user = $custom[0];
		$idplan = intval($custom[1]);
		$payement_amount = $_POST['mc_fee'];
		$payment_currency = $_POST['mc_currency'];
		$expire = new DateTime("now");
		$expire->add(new DateInterval('P1M'));
		$expire->format("Y-m-d H:i:s");
	
	//CONNECT DB
	$db = new PDO("mysql:host=HOST;dbname=DBNAME","USER","PASS");
				$stmt = $db->prepare("INSERT INTO pay (user,id,email,pack) VALUES(:user, :txn_id, :payer_email, :item_name)");
				$stmt->bindParam(':user', $user);
				$stmt->bindParam(':txn_id', $txn_id);
				$stmt->bindParam(':payer_email', $payer_email);
				$stmt->bindParam(':item_name', $item_name);
				$stmt->execute();

	$req = $db->prepare("UPDATE users SET expire=':expire', membership=':membership' WHERE username=':user'");
				$req=>execute(array(":expire" -> $expire,
						    ":membership" -> $idplan,
						    ":user" -> $user));
// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
 header("HTTP/1.1 200 OK");
?>


