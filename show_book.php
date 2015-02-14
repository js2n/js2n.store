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

if (isset($_GET['isbn']))  {
	$isbn = $db->clnStr($_GET['isbn']);
  try  { 
	  $itemData = $db->getItemData($isbn);
  }
  catch (Exception $e)  {
      	$db->errorHandler($e->getMessage());
      } 
}  else  {
	header('Location: /index.php');
}  

if (!isset($itemData)) {
  $title = "No such product!";
  $description = "Unable to find this product in a database";
} else {
  $title = $itemData['title'] . ' - ' . $itemData['author'];
  $description = $itemData['shortdescription'];
  } 

?>

<!DOCTYPE html>
<html>
  <?php $view->doHTMLHead($title, $description); ?>
<body>
	<div class = "header container_12">
	  <div class = "logo grid_4">
	    <h1>Books shop!</h1>
	  </div>
	  <?php $view->showMiniCart(); ?>
  </div>
  <div class = "clear"></div>
  <div class = "content container_12">
  	<div class = "sidebar grid_2">
  			<?php $view->listCats($db->getCats()); ?>
  	</div>
  	<div class = "items-area grid_9 prefix_1">
  		<?php	
      if(!isset($itemData))  {
      	echo "No such item...";
      }  else  {
   		    $view->displaySingleItem($itemData);
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