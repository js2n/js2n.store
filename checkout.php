<?php

session_start();

spl_autoload_register(function($class) {
  include 'classes/' . $class . '.class.php';
});

$db = new UserDB(DB::host, DB::username, DB::passwd, DB::db);
$view = new UserView;

$title = 'Checkout';
$description = 'Make order';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (!$db->checkForm($_POST))
      throw new Exception ("Fill all form data!");
    if (!$db->checkEmail($db->clnStr($_POST['email'])))
      throw new Exception ("Incorrect email! Try again, please ...");
    $orderData = array();
    foreach ($_POST as $key => $value) {
      $orderData[$key] = $db->clnStr($value);
    }
    $order = new Order($orderData);
  }
  catch (Exception $e) {
    echo $db->errorHandler($e->getMessage());
  }
  if (isset($order)) {
    try {
      $customerid = $order->checkCustomer();
      $insResult = $order->insertOrderData();
    }
    catch (Exception $e) {
      echo $db->errorHandler($e->getMessage());
    }
  }
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
  	<?php if (isset($insResult) && ($insResult == true)): 
     $_SESSION['total_items'] = 0;
     $_SESSION['total_price'] = 0;
     $_SESSION['cart'] = array();
    ?>
    <div class="items-area grid_9 prefix_1">
      <h2>An order was send!</h2>
    </div>
    <?php else:
  	  $db->displayCart($_SESSION['cart'], false, false); 
  	  $view->displayCheckoutForm();
  	  endif;
    ?>
  </div>
  <div class = "clear"></div>
  <div class = "footer container_12">
  	<span class = "copyright">books-shop.local 2014</span>
  </div>
</body>
</html>