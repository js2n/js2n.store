<?php

session_start();

spl_autoload_register(function ($class) {
  include '../classes/' . $class . '.class.php';
});  

$db = new UserDB(DB::host, DB::username, DB::passwd, DB::db);
$view = new UserView;
$title = 'Customers manager';
$description = 'Customers manager';

?>
<!DOCTYPE html>
<html>
<head>
	<?php $view->doHTMLHead($title, $description); ?>
</head>
<body>
  <?php $view->doAdminPanelHeader('Customers'); ?>
  <div class = "wrapper container_12">
	  <?php 
	  try 
	  {
	    $view->listCustomers($db->getCustomers());
	  }
	  catch (Exception $e)
	  {
	    $db->errorHandler($e->getMessage());
	  } 
	  ?>
	</div>
</body>
</html>