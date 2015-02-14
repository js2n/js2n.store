<?php

class Cart extends UserDB  {

	public function __construct()  {
		
    parent::__construct(DB::host, DB::username, DB::passwd, DB::db);

		if (!isset($_SESSION['cart']))  {
	  $_SESSION['cart'] = array();
	  $_SESSION['total_items'] = 0;
	  $_SESSION['total_price'] = 0.0;
		}
		
		$isbn = trim(strip_tags($_POST['isbn']));
		$amount = abs((int)$_POST['amount']);

		if (isset($_SESSION['cart'][$isbn]))  {
		  $_SESSION['cart'][$isbn] += $amount;
		  } else { 
		  	$_SESSION['cart'][$isbn] = $amount;
		  }
		$_SESSION['total_items'] = $this->calculateItemsInCart($_SESSION['cart']);
		$_SESSION['total_price'] = parent::calculateTotalPrice($_SESSION['cart']);
		header('Location:'.$_SERVER['HTTP_REFERER']);
	}

	public static function calculateItemsInCart(array $cartArr)  {
    $amount = 0;
    foreach ($cartArr as $key => $value)  {
      $amount += $value;
    }
    return $amount;
	}
}