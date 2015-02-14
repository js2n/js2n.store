<?php

class UserDB extends DB  {

  function checkForm($formData)  {
      foreach ($formData as $key => $value)  {
          if ((!isset($key)) || ($value == '')) return false;
      }
      return true;
  }

  public function checkEmail($email)  {
    if (preg_match('/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/', $email))  {
      return true;
    }  else  {
      return false;
    }
  }
	
  function clnStr($str)  {
    return $this->escape_string(strip_tags(trim($str)));
  }
	
  function login($login, $passwd)  {
    $passwd = crypt($passwd, 'lsd');
    $sql = 'select username, passwd from admin where username = ? and passwd = ?';
    
    if (!$stmt = $this->prepare($sql))  {
    	throw new Exception("Not prepare");
    }
    if (!$stmt->bind_param('ss', $login, $passwd))  {
    	throw new Exception("Not bind");
    }    
    if (!$stmt->execute())  {
    	throw new Exception("Not exec!");
    }
    $stmt->store_result();    
    
    if ($stmt->num_rows === 1)  {
      $_SESSION['admin_user'] = $login;
      header('Location: panel.php');  
    }  
    else  {
      throw new Exception("No such user!");
      }
	}
    
  function getCats()  {
      $sql = 'select cat_id, catname from categories';
      $res = $this->query($sql);
      $catArray = array();
          while ($row = $res->fetch_array(MYSQLI_ASSOC))  {
              $catArray[] = $row;
          }
      return $catArray;
  }

  function delCat($id)  {
    $sql = "delete from categories where cat_id = ?";
    $stmt = $this->prepare($sql);
    $stmt->bind_param('i', $id);
    if (!$stmt->execute())  {
      throw new Exception('Unable to delete category =(');
      return false;
      }     
    return true;
  }

  function addCat($title)  {
    $sql = "insert into categories (catname) values (?)";
    
    if (!$stmt = $this->prepare($sql))  {
      throw new Exception("Not prepare");
    }
    if (!$stmt->bind_param('s', $title))  {
      throw new Exception("Not bind");
    }    
    if (!$stmt->execute())  {
      throw new Exception("Not exec!");
    }
    return true;      
  }

  function addItem($isbn, $title, $author, $catId, $price, $uploadFileName, $shortDescription, $description)  {
    $sql = "select title from books where isbn = '$isbn'";
    $res = $this->query($sql);
    if($res->num_rows > 0)  {
      throw new Exception("This book is exist!");
      return false;
    }
    
    $sql = "insert into books (isbn, title, author, cat_id, price, img_src, shortdescription, description) values (?, ?, ?, ?, ?, ?, ?, ?)";
    
    if (!$stmt = $this->prepare($sql))  {
      throw new Exception("Not prepare: $stmt->error");
    }
    if (!$stmt->bind_param('sssidsss', $isbn, $title, $author, $catId, $price, $uploadFileName, $shortDescription, $description))  {
      throw new Exception("Not bind: $stmt->error");
    }    
    if (!$stmt->execute())  {
      throw new Exception("Not exec!: $stmt->error");
    }
    return true;      
  }

  function delItem($isbn)  {
    $sql = "delete from books where isbn = ?";
    $stmt = $this->prepare($sql);
    $stmt->bind_param('s', $isbn);
    if (!$stmt->execute())  {
      throw new Exception('Unable to delete item=(');
      return false;
      }     
    return true;  
  }
  
  function listItemsPerPage($pageNum = 0)  {
    $currentPageItems = 5;
    $sql = "select * from books";
    $res = $this->query($sql);
    $itemsCnt = $res->num_rows;
    $sql = "select b.author, b.title, b.isbn, b.price, b.img_src, c.catname from books as b, categories as c where b.cat_id = c.cat_id
            order by author limit $pageNum, $currentPageItems";
    $res = $this->query($sql);

    echo '<table>';
    while($row = $res->fetch_assoc())  {
        $catName = $row['catname'];
        $isbn = $row['isbn'];
        $author = $row['author'];
        $title = $row['title'];
        $price = $row['price'];
        $img = str_replace($_SERVER['DOCUMENT_ROOT'], "", $row['img_src']);
        echo "<tr><td><img src = '$img' alt = '$title' width = '60' height = '80'></td><td>$catName</td><td>$isbn</td><td>$author</td><td>$title</td><td>$price</td>".
        "<td><a href = 'items_manager.php?del=$isbn'>Delete</a> | <a href = 'items_manager.php?edit=$isbn'>Edit</a><td></tr>";
      }
    echo '</table>';
    
    echo '<div class = "pages-cntr">';
    $from = 0;
    $counter = 1;
    while($from < $itemsCnt)  {
      echo "<a href = '{$_SERVER['PHP_SELF']}?from=$from'>$counter</a>";
      $from += $currentPageItems;
      $counter++;
    }
    echo '</div>';  
  }

  function addUser($userName, $passwd)  {
    $passwd = crypt($passwd, 'lsd');
    $sql = "select username from admin where username = '$userName'";
    if(!$res = $this->query($sql))
      throw new Exception ('Database connection problem, try again later...');
    if ($res->num_rows > 0)  {
      throw new Exception("User $userName already exist");
      return false;
    }  else  {
        $sql = "insert into admin (username, passwd) values (?,?)";
          if (!$stmt = $this->prepare($sql))  {
          throw new Exception("Not prepare");
        }
        if (!$stmt->bind_param('ss', $userName, $passwd))  {
          throw new Exception("Not bind");
        }    
        if (!$stmt->execute())  {
          throw new Exception("Not exec!");
        }
        return true;
      }
    }
  
  function getUsers()  {
    $sql = 'select username from admin';
    $res = $this->query($sql);
    $usersArray = array();
        while ($row = $res->fetch_array(MYSQLI_ASSOC))  {
            $usersArray[] = $row;
        }
    return $usersArray;
  }

  function delUser($userName)  {
    $sql = "delete from admin where username = ?";
    $stmt = $this->prepare($sql);
    $stmt->bind_param('s', $userName);
    if (!$stmt->execute())  {
      throw new Exception('Unable to delete user =(');
      return false;
      }     
    return true;
  }

  function changePasswd($user, $oldPass, $newPass)  {
    $oldPass = crypt($oldPass, 'lsd');
    $newPass = crypt($newPass, 'lsd');
    
    $sql = "select username, passwd from admin where username = '$user' and passwd = '$oldPass'";
    
    if (!$res = $this->query($sql))
      throw new Exception("Database connection problem...");
    $usersCnt = $res->num_rows;
    if ($usersCnt == 1)  {
      $sql = "update admin set passwd = '$newPass' where username = '$user'";
      if(!$this->query($sql))
        throw new Exception("Database connection problem...");
      return true;
    } else {
       throw new Exception("No such user!");
     }
  }

  function getItemData($uniqueVal)  {
    $sql = "select * from books where isbn = '$uniqueVal'";
    $item = array();
    
    if(!$res = $this->query($sql))  { 
      throw new Exception ("Something wrong in your query...");
      return false; 
    }

    if ($res->num_rows == 0)  {
      throw new Exception ("No such item in database!");
      return false;  
      }  
    
    while ($row = $res->fetch_assoc())  {
      $item['isbn'] = $row['isbn'];
      $item['title'] = $row['title']; 
      $item['author'] = $row['author']; 
      $item['cat_id'] = $row['cat_id']; 
      $item['price'] = $row['price'];
      $item['img_src'] = $row['img_src'];
      $item['shortdescription'] = $row['shortdescription']; 
      $item['description'] = $row['description'];     
    }   
    return $item;
  }

  function updateItem($isbn, $title, $author, $catId, $price, $uploadFileName, $shortDescription, $description)  {
    
    $sql = "update books set isbn = ?, title = ?, author = ?, cat_id = ?, price = ?, img_src = ?, shortdescription = ?, description = ? where isbn = ?";
    
    if (!$stmt = $this->prepare($sql))  {
      throw new Exception("Not prepare: $this->error");
    }
    if (!$stmt->bind_param('sssidssss', $isbn, $title, $author, $catId, $price, $uploadFileName, $shortDescription, $description, $isbn))  {
      throw new Exception("Not bind: $stmt->error");
    }    
    if (!$stmt->execute())  {
      throw new Exception("Not exec!: $stmt->error");
    }
    return true;      
  }

  function getCatData($uniqueVal)  {
    $sql = "select cat_id, catname from categories where cat_id = '$uniqueVal'";
    $cat = array();
    
    if(!$res = $this->query($sql))  {
      throw new Exception ("Something wrong in your query...");
      return false;
    }
    
    if ($res->num_rows == 0)  {
      throw new Exception ("No such category!");
      return false;  
      }  

    while ($row = $res->fetch_assoc())  {
      $cat['cat_id'] = $row['cat_id'];
      $cat['catname'] = $row['catname']; 
    }   
    return $cat;
  }
  
  function updateCat($newCatName, $catId)  {
    
    $sql = "update categories set catname = ? where cat_id = ?";
    
    if (!$stmt = $this->prepare($sql))  {
      throw new Exception("Not prepare: $this->error");
    }
    if (!$stmt->bind_param('si', $newCatName, $catId))  {
      throw new Exception("Not bind: $stmt->error");
    }    
    if (!$stmt->execute())  {
      throw new Exception("Not exec!: $stmt->error");
    }
    return true;      
  }

  function mvFile($file, $uploadDir)  {
    if (!is_uploaded_file($file['tmp_name']))  {
      throw new Exception('Well, well... Possible file upload attack');
      return false;
    }
    if (!getimagesize($file['tmp_name']))  {
      throw new Exception('You trying upload a not image file. Try again..');
      return false;
    }

    $now = time();
    while(file_exists($uploadFileName = $uploadDir . '/' . $now . '-' . $file['name'])) $now++;
    if (!move_uploaded_file($file['tmp_name'], $uploadFileName))  {
      throw new Exception('Unable move file from tmp_dir');
      return false;
    }
    return $uploadFileName;
  }

  function getItemsFromCat($catId)  {
    $items = array();
    $sql = "select * from books where cat_id = '$catId'";
    $res = $this->query($sql);
    while ($row = $res->fetch_assoc())  {
      $items[] = $row; 
    }
    return $items;
  }

  public function displayCart(array $cart, $changeQty = true, $showButtons = true)  {
    $back = '/index.php';
    if (array_count_values($cart)): ?>
    <form action = "" method = "post" class = "grid_10">
    <table class = "cart">
      <thead>
        <tr>
          <th></th>
          <th>Product</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Total Amount</th>
        </tr>
      </thead>
      <?php
        foreach ($cart as $isbn => $qty) :?>
          <?php $itemData = $this->getItemData($isbn); ?>
          <tr>
            <td><img src = '<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], "", $itemData['img_src']); ?>' width = '50' height = '80'></td>
            <td><a href = 'show_book.php?isbn=<?php echo $itemData['isbn']; ?>'><?php echo $itemData['title'] . ' - ' . $itemData['author']; ?></td>
            <td><?php echo $itemData['price']; ?></td>
            <td><?php if ($changeQty): ?><input type = "text" name = "<?php echo $isbn; ?>" value = "<?php echo $qty; ?>" size = "1"></td>
            <?php 
            else : echo $qty; 
            endif;
            ?>
            <td><?php echo $itemData['price'] * $qty; ?></td>
          </tr>
        <?php endforeach; ?>
    </table>
      <?php if ($showButtons) : ?>
      <input type = "submit" value = "Save quantity!" class = "checkout">
      <a href = "checkout.php" class = "cart-button">Go to checkout!</a>
      <a href = 'index.php?id=<?php echo $itemData['cat_id'];?>' class = "cart-button">Continue shopping</a>
      <?php endif; ?>
    <?php else : ?>
    </form>  
      <p>Ваша корзина пуста...<br><a href = '<?php echo $back; ?>'>Поискать еще?</a></p>
    <?php endif;    
  }

  public function calculateTotalPrice(array $cartArr)  {
    $totalPrice = 0.0;
    foreach ($cartArr as $isbn => $amount)  {
      $sql = "select price from books where isbn = '$isbn'";
      $res = $this->query($sql);
      $res = $res->fetch_assoc();
      $price = $res['price'];
      $totalPrice += $price * $amount;
    }
    return $totalPrice;
  } 
}
?>