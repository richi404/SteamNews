<html>
<head>
  <title>SteamNews - Logowanie</title>
	<link rel="stylesheet" type="text/css" href="public/css/style.css">
	<?php
	session_start();
	if(isset($_POST['logout']))session_destroy();
	else if (isset($_SESSION["login"])){
  header("Location: public/php/news.php");
 	exit();
	}
		if(isset($_POST['login'],$_POST['password']))
		{
			//$connetion = @new mysqli('localhost','rborzych','rborzych','rborzych');
			$connetion = @new mysqli('localhost','root','','rborzych');
      if($connetion->connect_errno) die('Nie można połączyć się z serwerem');
      $rs=$connetion->query('SELECT `username` FROM `steamnewsclients` WHERE `username`="'.$_POST["login"].'" AND `password`="'.$_POST["password"].'"');
      $rec=$rs->fetch_array();
			if($rec['username']==$_POST["login"])
			{
				$_SESSION['login']=$_POST['login'];
				$_SESSION['password']=$_POST['password'];
				header("Location: public/php/news.php");
				exit();
			}
			else $error = "<B>Błędny login lub hasło!</B><BR>";
		}
		else
			$error = false;
	?>
</head>
<body>
	<div id="login_box">
  <h1>Podaj login i&nbsp;hasło</h1>
  <form method="POST">
    <label>Login: <input type="text" name="login"></label><br/><br/>
    <label>Hasło: <input type="password" name="password"></label><br/><br/>
    <input type="submit" value="Zaloguj się"><br/><br/>
		<?php
  		echo $error ? $error : "";
		?>
		<em>Nie masz konta? <a href="public/php/signup.php">Zarejestruj się.</a></em>
  </form>
	</div>
</body>
</html> 