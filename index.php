<?php

session_start();

if (!isset($_SESSION['cart']))  {
    $_SESSION['cart'] = array();
    $_SESSION['total_items'] = 0;
    $_SESSION['total_price'] = 0.0;
}

spl_autoload_register(function ($class) {
  include 'classes/' . $class . '.class.php';
});

$db = new UserDB(DB::host, DB::username, DB::passwd, DB::db);
$view = new UserView;
$title = 'PHP books shop';
$description = 'Main page of books shop';

if (($_SERVER['REQUEST_URI'] == '/index.php') || ($_SERVER['REQUEST_URI'] == '/'))  {
  $catId = 1;
  try {
    $cat = $db->getCatData($catId);
  }
  catch (Exception $e)  {
    $db->errorHandler($e->getMessage());
  }  
}

if (isset($_GET['id']))  {
  $catId = abs((int)$_GET['id']);
  try {
    $cat = $db->getCatData($catId);
  }
  catch (Exception $e)  {
    $db->errorHandler($e->getMessage());
  }  
}
?>

<!DOCTYPE html>
<html>
  <meta name = "description" content = "asdfasdfasdf">
  <?php $view->doHTMLHead($title, $description); ?>
<body>
	<div class = "header container_12">
	  <div class = "logo grid_4">
	    <h1><a href = "/index.php">Books shop!</a></h1>
	  </div>
	  <?php $view->showMiniCart(); ?>
  </div>
  <div class = "clear"></div>
  <div class = "content container_12">
  	<div class = "sidebar grid_2">
  			<?php $view->listCats($db->getCats()); ?>
  	</div>
  	<div class = "items-area grid_9 prefix_1">
  		<?php if (isset($cat['catname'])) { 
        echo "<h2>{$cat['catname']}</h2>";
        $view->listItemsFromCat($db->getItemsFromCat($catId));
        }  else  {
          echo "<h2>No such category</h2>";
        } 
        ?>
  	</div>
  </div>
  <div class = "clear"></div>
  <div class = "footer container_12">
  	<span class = "copyright">books-shop.local 2014</span>
  </div>
</body>
</html>