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
$title = 'Cart';
$description = 'Show items in cart';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  foreach ($_SESSION['cart'] as $isbn => $qty) {
    if ($_POST[$isbn] == 0) {
      unset($_SESSION['cart'][$isbn]);
    } else {
      $_SESSION['cart'][$isbn] = $_POST[$isbn];
    }
  }
  $_SESSION['total_items'] = Cart::calculateItemsInCart($_SESSION['cart']);
  $_SESSION['total_price'] = $db->calculateTotalPrice($_SESSION['cart']);
}

?>
<!DOCTYPE html>
<html>
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
  	<?php $db->displayCart($_SESSION['cart']); ?>
  </div>
  <div class = "clear"></div>
  <div class = "footer container_12">
  	<span class = "copyright">books-shop.local 2014</span>
  </div>
</body>
</html>