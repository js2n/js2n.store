<?php

session_start();

if (!isset($_SESSION['admin_user']))  
  header('Location: index.php');

spl_autoload_register(function($class)  {
  include '../classes/' . $class . '.class.php';
});


$view = new UserView;
$db = new UserDB(DB::host, DB::username, DB::passwd, DB::db);

$title = 'Manage items';
$description = 'Add, delete and edit shop items';

if (isset($_GET['edit']))  {
  $uniqueValToEdit = trim(strip_tags($_GET['edit'])); 
  try  {
    $item = $db->getItemData($uniqueValToEdit);
  }
  catch (Exception $e)  {
    $db->errorHandler($e->getMessage());
  }
}

if (isset($_GET['del']))  {
  $isbn = strip_tags($_GET['del']);
	try  {
	  $db->delItem($isbn);
	  
	}
	catch (Exception $e)  {
		$db->errorHandler($e->getMessage());
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
  
  $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/items-images';
  $isbn = $db->clnStr($_POST['isbn']);
  $title = $db->clnStr($_POST['title']);
  $author = $db->clnStr($_POST['author']);
  $catId = $db->clnStr($_POST['catid']);
  $price = $db->clnStr($_POST['price']);
  $shortDescription = $db->clnStr($_POST['shortdescription']);
  $description = $db->clnStr($_POST['description']);
  
  try  {
    switch ($_FILES['image']['error'])  {
      case 0: $uploadFile = $db->mvFile($_FILES['image'], $uploadDir);
      break;
      case 1: throw new Exception('Exceeded the maximum file size');
      break;
      case 2: throw new Exception('Exceeded the maximum file size in the HTML form');
      break;
      case 3: throw new Exception('Was sent only part of a file');
      break;
      case 4: throw new Exception('File to send was not selected');
      break;
      default: throw new Exception('Unknown error..');
    }    

    if (!$db->checkForm($_POST))
      throw new Exception('Fill all form fields!');
    
    if (isset($item))  {
      if (!$db->updateItem($isbn, $title, $author, $catId, $price, $uploadFile, $shortDescription, $description))
         throw new Exception('Unable to update item. Try again later...');
         header('Location: items_manager.php');
    }  else  {
        if (!$db->addItem($isbn, $title, $author, $catId, $price, $uploadFile, $shortDescription, $description))  
          throw new Exception('Unable to add item. Try again later...');
      }
  }
  catch (Exception $e)  {
    $db->errorHandler($e->getMessage());
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <?php $view->doHTMLHead($title, $description); ?>
</head>
<body>
  <?php $view->doAdminPanelHeader('Items manager'); ?>
  <div class = "wrapper container_12">
    <form action = "" method = "post" enctype = "multipart/form-data">
      <fieldset>
      	<legend>Add item</legend>
        <label for = "isbn" class = "label">ISBN</label>
      	<input type = "text" name = "isbn" size = "25" value = "<?php echo isset($item) ? $item['isbn'] : ''; ?>"><br>
      	<label for = "title" class = "label">Title</label>
      	<input type = "text" name = "title" size = "25" value = "<?php echo isset($item) ? $item['title'] : ''; ?>"><br>
      	<label for = "author" class = "label">Author</label>
      	<input type = "text" name = "author" size = "25" value = "<?php echo isset($item) ? $item['author'] : ''; ?>"><br>
      	<label for = "category" class = "label">Category</label>
      	<?php  $view->listCatsToSel($db->getCats()); ?><br>
      	<label for = "price" class = "label">Price</label>
      	<input type = "text" name = "price" size = "25" value = "<?php echo isset($item) ? $item['price'] : ''; ?>"><br>
        <input type = "hidden" name = "MAX_FILE_SIZE" value = "2000000">
        <label for = "image" class = "label">Image</label>
        <input type = "file" name = "image" size = "25" value = "<?php echo isset($item) ? $item['image'] : ''; ?>"><br>
      	<label for = "shortdescription" class = "label">Short description</label>
        <textarea name = "shortdescription" cols = "60" rows = "5"><?php echo isset($item) ? $item['shortdescription'] : ''; ?></textarea><br>
        <label for = "description" class = "label">Description</label>
      	<textarea name = "description" cols = "60" rows = "10"><?php echo isset($item) ? $item['description'] : ''; ?></textarea><br>
      	<input type = "submit" value = "<?php echo isset($item) ?  'Update' : 'Add new item'; ?>" id = "add-item">
      </fieldset>    
    </form>
    
    <form action = "search_items.php" method = "post">
    	<fieldset>
		    <legend>Search</legend>
		    <label for = "title" class = "label">Item name</label>
		    <input type = "text" name = "search" size = "25">
		    <input type = "submit" value = "Search">
			</fieldset>    	
    </form>
    <div class = "admin-shop-items">
    <?php 
    if (isset($_GET['from']))  {
            $pageNum = abs((int)$_GET['from']);
            $db->listItemsPerPage($pageNum); 
          }  else $db->listItemsPerPage();
    ?>
    </div>
  </div>
</body>
</html>