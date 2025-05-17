<?
include_once 'config.php';
if(!isset($_REQUEST['totalelo']) || !isset($_REQUEST['amount']))
	die('check parametres');+

$totalelo = (int)$_REQUEST['totalelo'];
$amount = (int)$_REQUEST['amount'];

$token = $_GET['token'];
if($token != ADMIN_TOKEN)
	die('');

file_put_contents('push.json', json_encode(['time' => time(), 'amount' => $amount, 'totalelo' => $totalelo]));
die('Animation is running');