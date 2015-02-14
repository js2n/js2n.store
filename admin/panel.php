<?php
session_start();

if (!isset($_SESSION['admin_user']))  
  header('Location: index.php');

spl_autoload_register(function($class)  {
  include '../classes/' . $class . '.class.php';
});

$view = new UserView;
$db = new UserDB(DB::host, DB::username, DB::passwd, DB::db);

$title = 'Admin panel';
$description = 'Administrate shop items, users, categories';
$menuItems = array(
	                  'cat_manager.php'  => 'Categories manager',
	                  'user_manager.php' => 'Users manager',
	                  'items_manager.php'=> 'Items manager',
	                  'order_manager'    => 'Orders manager'
	                );
?>
<!DOCTYPE html>
<html>
<head>
<?php $view->doHTMLHead($title, $description); ?>
</head>
<body>
  <div class = "header container_12">
    <h1 class = "grid_8">Books-Shop: admin panel</h1>
    <div class = "logout"><a href = "logout.php">Log out</a> | <a href = "/index.php">View site</a></div> 
  </div>
  <div class = "clear"></div>
  <div class = "wrapper container_12">
  	<?php $view->doAdminMenu($menuItems); ?>
  </div>
</body>
</html>
