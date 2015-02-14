<?php

class DB extends Mysqli {
	const host = 'localhost';
	const username = 'books_user';
	const passwd = 'sindrom4ik';
	const db = 'books_shop';
	
	public function __construct($host, $user, $pass, $db)  {
		@parent::__construct($host, $user, $pass, $db);
		
		try  {
			if ($this->connect_error)  {
				throw new Exception("Error: $this->connect_error");
			}
		}
		catch (Exception $e)  {
      $this->errorHandler($e->getMessage());
	  }
	}

	public function __destruct()  {
    $this->close();
	}

	public function errorHandler($message)  {
		echo "<div id = 'error'>$message</div>";
	}
}

?>