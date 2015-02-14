<?php
session_start();

if (!isset($_SESSION['admin_user']))  
  header('Location: index.php');

spl_autoload_register(function($class)  {
  include '../classes/' . $class . '.class.php';
});

$view = new UserView;
$db = new UserDB(DB::host, DB::username, DB::passwd, DB::db);

$title = 'Change password';
$description = 'Change password for any user';

if (!isset($_GET['user']))
	header('Location: user_manager.php');
$user = trim(strip_tags($_GET['user']));

if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
	$oldPass = $db->clnStr($_POST['old_passwd']);
	$newPass = $db->clnStr($_POST['new_passwd']);
	$passAgain = $db->clnStr($_POST['passwd_again']);
  try  {
    if (!$db->checkForm($_POST))
    	throw new Exception("Fill all form fields!");
    if ($newPass !== $passAgain)
    	throw new Exception('The passwords must match!');
    if (strlen($newPass) < 6 || strlen($newPass) > 16) 
    	throw new Exception('Password must contain from 0 to 16 characters!');
    if ($db->changePasswd($user, $oldPass, $newPass))  
      throw new Exception("Password for \"$user\" was changed");
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
  <div class = "wrapper container_12">
  	<?php $view->doAdminPanelHeader("Change password for $user"); ?>
	  <form actiom = "" method = "post">
	      <fieldset>
	        <legend>Change password</legend>
	          <label for = "old_passwd" class = "label">Old password:</label>
	        	<input type = "password" name = "old_passwd" size = "25"><br>
	        	<label for = "new_passwd" class = "label">New password:</label>
	        	<input type = "password" name = "new_passwd" size = "25"><br>
	        	<label for = "passwd_again" class = "label">Password again:</label>
	        	<input type = "password" name = "passwd_again" size = "25"><br>
	        	<input type = "submit" value = "Add" id = "add-item">
	      </fieldset>
	    </form>
  </div>
</body>
</html>