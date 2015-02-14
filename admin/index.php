<?php
session_start();
spl_autoload_register(function($class)  {
  include '../classes/' . $class . '.class.php';
});

$view = new UserView;
$title = 'Login, please';
$description = 'Login form';

$db = new UserDB(DB::host, DB::username, DB::passwd, DB::db);

if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
	if (!$db->checkForm($_POST))  {
		$db->errorHandler('Fill all form fields!');
	}
	else  {
		$login = $db->clnStr(($_POST['login']));
		$passwd = $db->clnStr(($_POST['passwd']));
		try {
	    $db->login($login, $passwd);
		}
		catch (Exception $e) {
			$db->errorHandler($e->getMessage());
		}
	}
}
?>

<!DOCTYPE html>
<html>
  <?php $view->doHTMLHead($title, $description); ?>
<body>
  <div class = "header container_12">
    <h1>Admin panel</h1>
  </div>
  <div class = "clear"></div>
  <div class = "content container_12">
		<form action = "" method = "post">
		  <fieldset>
			  <legend>Log in, please</legend>
			  <label for = "login" class = "label">Login:</label>
			  <input type = "text" size = "20" name = "login"><br>
			  <label for = "passwd" class = "label">Password:</label>
			  <input type = "password" size = "20" name = "passwd"><br>
			  <input type = "submit" value = "Submit!" id = "add-item">
		  </fieldset>
		</form>
  </div>
</body>
</html>