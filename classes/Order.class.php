<?php

class Order extends UserDB  {  
  
  protected $orderData;
  protected $customerid;

  public function __construct(array $orderData) {
  	parent::__construct(DB::host, DB::username, DB::passwd, DB::db);
  	$this->orderData = $orderData;
  }

  public function checkCustomer()  
  {
  extract($this->orderData);
  $sql = "select customer_id from customers where email = '$email'";
  $res = $this->query($sql);
  
  if ($res->num_rows > 0) {
    $customer = $res->fetch_object();
    $this->customerid = $customer->customer_id;
  } else {
    $sql = "insert into customers (name, city, state, email, phone) values (?, ?, ?, ?, ?)";
    if (!$stmt = $this->prepare($sql))  {
      throw new Exception("Not prepare: $stmt->error");
      return false;
    }
    if (!$stmt->bind_param('sssss', $name, $city, $region, $email, $phone))  {
      throw new Exception("Not bind: $stmt->error");
      return false;
    }    
    if (!$stmt->execute())  {
      throw new Exception("Not exec!: $stmt->error");
      return false;
    }
  $this->customerid = $stmt->insert_id;
  }
  }

  public function insertOrderData() {
    $this->autocommit(FALSE);
    extract($this->orderData);
    @$date = date('Y-m-d');
    $amount = parent::calculateTotalPrice($_SESSION['cart']);
    $defaultShipStatus = 'PARTIAL';
    
    $sql = "insert into orders (customer_id, amount, date, order_status, ship_name, ship_city, ship_state) values (?, ?, ?, ?, ?, ?, ?)";
    if (!$stmt = $this->prepare($sql))  {
      throw new Exception("Not prepare: $stmt->error");
      return false;
	  }
    if (!$stmt->bind_param('idsssss', $this->customerid, $amount, $date, $defaultShipStatus, $name, $city, $region))  {
      throw new Exception("Not bind: $stmt->error");
      return false;
    }    
    if (!$stmt->execute())  {
      throw new Exception("Not exec!: $stmt->error");
      return false;
    }
    
    // before inserting order items we check the presens of the order in db ...
    $sql = "select order_id from orders where 
				    customer_id = '$this->customerid' and
				    date = '$date' and
				    order_status = '$defaultShipStatus' and 
				    ship_name = '$name' and
				    ship_city = '$city' and 
				    ship_state = '$region' and 
				    amount = ('" . $_SESSION['total_price'] . "')";
		$res = $this->query($sql);
		if ($res->num_rows > 0) {
			$order = $res->fetch_object();
			$order_id = $order->order_id;
		} else {
			return false;
		}

		// we will insert each book from ordered
		foreach ($_SESSION['cart'] as $isbn => $quantity) {
			$details = $this->getItemData($isbn);
			$sql = "delete from order_items where isbn = '$isbn' and order_id = '$order_id'";
			$this->query($sql);
			$sql = "insert into order_items values ('$order_id', '$isbn', '" . $details['price'] . "', '$quantity')";
			if (!$res = $this->query($sql)) return false;
		}
    $this->commit();
		$this->autocommit(TRUE);
		return true;
  }
}

