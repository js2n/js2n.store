<?php
session_start();

if (!isset($_SESSION['admin_user'])) {  
  header('Location: index.php');
}

spl_autoload_register(function($class) {
  include '../classes/' . $class . '.class.php';
});

$view = new UserView;
$db = new UserDB(DB::host, DB::username, DB::passwd, DB::db);

$title = 'The orders manager';
$description = 'Аdministration orders';
?>

<!DOCTYPE html>
<html>
<head>
	<?php $view->doHTMLHead($title, $description); ?>
</head>
<body>
  <?php $view->doAdminPanelHeader('The orders manager'); ?>
  <div class = "wrapper container_12">
  	<?php 
    try {
  	  $view->displayOrders($db->getOrders()); 
    }
    catch (Exception $e) {
    	$db->errorHandler($e->getMessage());
    }
  	?>
  </div>
</body>
</html>