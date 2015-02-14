<?php

session_start();

if (!isset($_SESSION['admin_user']))  
  header('Location: index.php');

spl_autoload_register(function($class)  {
  include '../classes/' . $class . '.class.php';
});

class CatsView extends UserView  {
  
  public function listCats($catsArray)  {
  	if (!is_array($catsArray) || count($catsArray) == 0)  {
  		echo "No categories added... yet.";
  	} else  {
	  	echo "<table class = 'admin-tbl'>";
	  	foreach  ($catsArray as $item)  {
	  		$catId = $item['cat_id'];
	  		$catName = $item['catname'];
	      echo "<tr><td>$catName</td>".
	           "<td><a href = 'cat_manager.php?del=$catId'>Delete</a>".
	           " | <a href = 'cat_manager.php?edit=$catId'>Edit</a>\n</td></tr>";
	  	}
	  	echo "</table>\n";
	  }
  }
}

$view = new CatsView;
$db = new UserDB(DB::host, DB::username, DB::passwd, DB::db);

$title = 'Manage categories';
$description = 'Add, delete and edit categiries';

if (isset($_GET['edit']))  {
  $uniqueValToEdit = trim(strip_tags($_GET['edit'])); 
  try  {
    $catData = $db->getCatData($uniqueValToEdit);
  }
  catch (Exception $e)  {
    $db->errorHandler($e->getMessage());
  }
}

if (isset($_GET['del']))  {
  $idToDel = (int)$_GET['del'];
	try  {
	  $db->delCat($idToDel);
	  header('Location: cat_manager.php');
	}
	catch (Exception $e)  {
		$db->errorHandler($e->getMessage());
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
  $catTitle = $db->clnStr($_POST['title']);
  try  {
    if (!$db->checkForm($_POST))
      throw new Exception('Fill category name!');
    if (isset($catData))  {
      if(!$db->updateCat($catTitle, $uniqueValToEdit))
        throw new Exception('Unable to update category. Try again later...');
        header('Location: cat_manager.php');
    }  else  {
        if (!$db->addCat($catTitle))  
          throw new Exception('Unable to add category. Try again later...');
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
  <?php $view->doAdminPanelHeader('Categories manager'); ?>
  <div class = "wrapper container_12">
  	<?php $view->listCats($db->getCats()); ?>
    <form actiom = "" method = "post">
      <label for = "title"><?php echo isset($catData) ? 'Rename category' : 'Add category:'; ?></label>
    	<input type = "text" name = "title" size = "25" value = "<?php echo isset($catData) ? $catData['catname'] : ''; ?>">
    	<input type = "submit" value = "<?php echo isset($catData) ? 'Rename' : 'Add'; ?>">
    </form>
  </div>
</body>
</html>