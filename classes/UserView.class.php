<?php 

class UserView  {
  protected $charset = 'utf-8';
  protected $styles = array('/css/styles.css', '/css/960.css' );
  private $scripts = array(
                          '//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
                          '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js',
                          '/js/scripts.js'
                          );

  function __set($name, $value)  {
  	$this->name = $value; 
  }

  public function doAdminMenu($menuItems)  {
    if ((!is_array($menuItems)) || (count($menuItems) == 0))
      die ('No menu items available...');
    $itemId = 0;
    foreach ($menuItems as $link => $title)  {
      echo '<div class = "adm-button grid_3">';
      echo "<div id = 'item_$itemId' class = 'bgr'></div>"; 
      echo "<a href = '$link'>$title</a>";
      echo '</div>';
      $itemId++;
    }
  }

  public function doHTMLHead($title, $description)  {
  	echo "<head>\n";
    echo "  <title>$title</title>\n";
    echo "  <meta charset = '$this->charset'>\n";
    echo "  <meta name = 'description' content = '$description'>\n";
  	if (is_array($this->styles))  {
  		foreach ($this->styles as $style)  {
  			echo  "  <link rel = 'stylesheet' href = '$style'>\n";
  		}
  	}
  	if (is_array($this->scripts))  {
  		foreach ($this->scripts as $script)  {
  			echo  "  <script src = '$script' type = 'text/javascript'></script>\n";
  		}
  	}
  	echo "</head>\n";
  }

  public function doAdminPanelHeader($title)  {
    trim(strip_tags($title));
    echo '<div class = "header container_12">';
    echo "<h1 class = 'grid_8'>$title</h1>";
    if ($_SERVER['REQUEST_URI'] == '/admin/panel.php') {
      echo '<div class="logout"><a href="logout.php">Log out</a> | <a href="/index.php" target="_blank">View site</a></div>';
    } else {  
      echo '<div class="logout"><a href="panel.php" id="return">Return</a> | <a href="logout.php">Log out</a> | '.
           '<a href="/index.php" target="_blank">View site</a></div>';
         }
    echo '</div>';
  } 
  
  public function listCats($catsArray)  {
  	if (!is_array($catsArray) || count($catsArray) == 0)  {
  		echo "No categories added... yet.";
  	} else  {
	  	echo "<ul>\n";
	  	foreach  ($catsArray as $item)  {
	  		$catId = $item['cat_id'];
	  		$catName = $item['catname'];
	      echo "  <li><a href = 'index.php?id=$catId'>$catName</a></li>\n";
	  	}
	  	echo "</ul>\n";
	  }
  }

  public function listCatsToSel($catsArray)  {
    if (!is_array($catsArray) || count($catsArray) == 0)  {
      echo "<select><option value = 'no-cats'>No categories added... yet.</option></select>";
    } else  {
      echo "<select name = 'catid'>\n";
      foreach  ($catsArray as $item)  {
        $catId = $item['cat_id'];
        $catName = $item['catname'];
        echo "  <option value = '$catId'>$catName</option>\n";
      }
      echo "</select>\n";
    }
  }  

  public function listUsers($usersArray)  {
    if (!is_array($usersArray) || count($usersArray) == 0)  {
      echo "No users in database... yet.";
    } else  {
      echo "<table class = 'admin-tbl'>\n";
      foreach  ($usersArray as $item)  {
        $userName = $item['username'];
        echo "<tr><td>$userName</td><td><a href = 'user_manager.php?del=$userName' id = 'del-user'>Delete</a> | ".
                              "<a href = 'ch_pass.php?user=$userName'>Change password</a></td>\n";
      }
      echo "</table>\n";
      echo '<div class = "clear"></div>';
    }
  }

  public function listItemsFromCat($itemsArr)  {
    if (!is_array($itemsArr)  || (count($itemsArr) == 0))  {
      echo 'This category is empty';
      return false;
    }
    foreach($itemsArr as $item): ?>
      <div class = "item">
        <img src = "<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], "", $item['img_src']); ?>" width = "150" heigth = "220"/>
        <h3 class = "item-title"><a href = "show_book.php?isbn=<?php echo $item['isbn']; ?>"><?php echo $item['title']; ?></a></h3>
        <span class = "price-title">Price:</span>
        <span class = "price"><?php echo $item['price']; ?></span>
        <form class = "buy-item" action = "add_to_cart.php" method = "post">
          <input type = "text" size = "1" name = "amount" value = "1">
          <input type = "hidden" name = "isbn" value = "<?php echo $item['isbn']; ?>">
          <input type = "submit" value = "Add to cart">
        </form>
      </div>
      <?php endforeach;   
    }

  public function displaySingleItem($itemData)  {
    if (is_array($itemData)): ?>
    <div class = "single-item">
      <div class = "img-and-price">
      <img src = "<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], "", $itemData['img_src']); ?>" width = "180" heigth = "250"/>
      <span class = "single-price-title">Price:</span>
      <span class = "single-price"><?php echo $itemData['price']; ?></span>
      <form class = "buy-item" action = "add_to_cart.php" method = "post">
        <input type = "text" size = "1" name = "amount" value = "1">
        <input type = "hidden" name = "isbn" value = '<?php echo $itemData['isbn']; ?>'>
        <input type = "submit" value = "Add to cart">
      </form>
      </div>
      <h3 class = "single-item-title"><?php echo $itemData['title']; ?></h3>
      <p class = "single-item-shortdescription"><?php echo $itemData['shortdescription']; ?></p>
      <p class = "single-item-description"><?php echo $itemData['description']; ?></p>
    </div>
    <?php endif;     
  }

  public function showMiniCart()  { ?>
  <div class = "cart grid_3 prefix_5">
    <a href = "cart.php">View cart</a><br>
    <span class = "items-in-cart"><?php echo "Товаров в корзине: " . $_SESSION['total_items'] . " (" . 
                                              $_SESSION['total_price'] . " р.)"; ?></span>
  </div>
  <?php }
  
  public function displayCheckoutForm() { ?>
    <fieldset>
      <legend>Order data:</legend>
      <label for = "name" class = "label">Your name:</label>
      <input type = "text" name = "name"><br>
      <label for = "phone" class = "label">Phone:</label>
      <input type = "text" name = "phone"><br>
      <label for = "email" class = "label">Your email:</label>
      <input type = "text" name = "email"><br>
      <label for = "region" class = "label">Your region:</label>
      <input type = "text" name = "region"><br>
      <label for = "city" class = "label">Your city:</label>
      <input type = "text" name = "city"><br>
      <input type = "submit" value = "Make order!" id = "make-order">
    </fieldset>
  <?php }
  
  public function displayOrders($orders) 
  {
    if ($orders === false) {
      echo 'No orders ... yet.';
    } else {
      echo '<table class="order-table">';
      echo '<tbody>';
      echo '<tr><th>Order ID</th><th>Customer ID</th><th>Date</th><th>Amount</th><th>Order Status</th>' . 
           '<th>Ship name</th><th>Ship city</th><th>Ship state</th></tr>';
      foreach($orders as $order) {
        echo '<tr class="order-tr">';
        echo "<td>{$order['order_id']}</td><td>{$order['customer_id']}</td><td>{$order['date']}</td><td>{$order['amount']}</td>" . 
              "<td>{$order['order_status']}</td><td>{$order['ship_name']}</td><td>{$order['ship_city']}</td><td>{$order['ship_state']}</td>";
        $db = new UserDB(DB::host, DB::username, DB::passwd, DB::db);
        $orderItems = $db->getOrderItems((int)$order['order_id']);
        foreach ($orderItems as $items) {
          echo "<tr id='details'><td>ISBN: {$items['isbn']}" . 
               "; Quantity: {$items['quantity']}; " . 
               "Item price: {$items['item_price']}<td></tr>";
          echo '</tr>';
        }
      }
      echo '</tbody></table>';
    }
  }
  
  public function listCustomers($customers)
  {
    if ($customers === false) {
      echo 'No customers ... yet.';
    } else {
      echo '<table>';
      echo '<tbody>';
      echo '<tr><th>Customer ID</th><th>Name</th><th>City</th><th>State</th><th>Email</th>' . 
           '<th>Phone</th>';
      foreach($customers as $customer) {
        echo "<tr><td>{$customer['customer_id']}</td><td>{$customer['name']}</td><td>{$customer['city']}</td>" .
             "<td>{$customer['state']}</td><td>{$customer['email']}</td><td>{$customer['phone']}</td></tr>";
      }
    }
  }   
}