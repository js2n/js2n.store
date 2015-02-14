<?php

session_start();

if (!isset($_SESSION['admin_user']))  
  header('Location: index.php');

spl_autoload_register(function($class)  {
  include '../classes/' . $class . '.class.php';
});

class SearchDB extends UserDB  {
  
  function listItemsPerPage($pageNum = 0, $search)  {
    $currentPageItems = 5;
    $sql = "select * from books";
    $res = $this->query($sql);
    $itemsCnt = $res->num_rows;
    $sql = "select * from books where title like '$search%'";
    $res = $this->query($sql);
    $findedItemsCnt = $res->num_rows;
    if  ($findedItemsCnt == 0)  {
    	echo "<p>По запросу \"$search\" ничего не найдено</p>";
    	return false;
    }

    echo '<table>';
    while($row = $res->fetch_assoc())  {
        $catId = $row['cat_id'];
        $isbn = $row['isbn'];
        $author = $row['author'];
        $title = $row['title'];
        $price = $row['price'];
        $description = $row['description'];
        echo "<tr><td>$catId</td><td>$isbn</td><td>$author</td><td>$title</td><td>$price</td>".
        "<td><a href = 'search_items.php?del=$isbn'>Delete</a> | <a href = 'edit_item.php?edit=$isbn'>Edit</a><td></tr>";
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
}

$view = new UserView;
$db = new SearchDB(DB::host, DB::username, DB::passwd, DB::db);

$title = 'Search items';
$description = 'Search and edit shop items';

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
  $search = $db->clnStr($_POST['search']);
  try {
  	if (!$db->checkForm($_POST))
  		throw new Exception("You haven't entered search terms!");
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
	<?php $view->doAdminPanelHeader('Search'); ?>
  <div class = "wrapper container_12">
	  <form action = "search_items.php" method = "post">
    	<fieldset>
		    <legend>Search</legend>
		    <label for = "title" class = "label">Item name</label>
		    <input type = "text" name = "search" size = "25">
		    <input type = "submit" value = "Search">
			</fieldset>    	
    </form>
    <?php 
    if (isset($_GET['from']))  {
            $pageNum = abs((int)$_GET['from']);
            $db->listItemsPerPage($pageNum, 'p'); 
          }  else $db->listItemsPerPage(0, $search);
    ?>
    </div>
  </div>
</body>
</html>