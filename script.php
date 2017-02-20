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
		$user = $_POST['custom'];
		$payement_amount = $_POST['mc_fee'];
		$payment_currency = $_POST['mc_currency']; 
	
	//CONNECT DB
	$db = new PDO("mysql:host=HOST;dbname=DBNAME","USER","PASS");
				$stmt = $db->prepare("INSERT INTO pay (user,id,email,pack) VALUES(:user, :txn_id, :payer_email, :item_name)");
				$stmt->bindParam(':user', $user);
				$stmt->bindParam(':txn_id', $txn_id);
				$stmt->bindParam(':payer_email', $payer_email);
				$stmt->bindParam(':item_name', $item_name);
				$stmt->execute();

	$req = $db->prepare("UPDATE users SET expire=':expire', membership=':membership' WHERE username='$user'");
				$req->execute(array(":expire" -> 'DATE_ADD(NOW(), new DateInterval('P1M'))',
						    "membership" -> $idplan));


// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
 header("HTTP/1.1 200 OK");
?>

######################################################################

<?php 
require('PaypalIPN.php');
use PaypalIPN;
$ipn = new PaypalIPN();
// Use the sandbox endpoint during testing.
$ipn->useSandbox();
$verified = $ipn->verifyIPN();
if ($verified) {
    $txn_id = $_POST['txn_id'];
	$payer_email = $_POST['payer_email'];
	$item_name = $_POST['item_name'];
	$user = $_POST['custom'];
	$payement_amount = $_POST['mc_gross'];
	//CONNECT DB
	$db = new PDO("mysql:host=HOST;dbname=DBNAME","USER","PASS");
		//Historique de commandes			
			$req = $db->query('SELECT * FROM plans WHERE price='.$payement_amount.' LIMIT 1');
			$d = $req->fetch(PDO::FETCH_ASSOC);
			if(!empty($d)){
				$idplan = $d['ID'];
			$db->query("UPDATE users SET expire=DATE_ADD(NOW(), INTERVAL 1 MONTH), membership='$idplan' WHERE username = '$user'");
			$db->query("INSERT INTO pay (user,id,email,pack) VALUES('$user','$txn_id','$payer_email','$item_name')");
			}else{
			
			}
}
// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
header("HTTP/1.1 200 OK");
?>
