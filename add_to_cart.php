<?php 

session_start();

spl_autoload_register(function ($class) {
  include 'classes/' . $class . '.class.php';
});

$db = new UserDB(DB::host, DB::username, DB::passwd, DB::db);
$view = new UserView;

if ($_SERVER['REQUEST_METHOD'] === 'POST')  {
	$cart = new Cart();
}  else  {
	header('Location: /index.php');
}
?>