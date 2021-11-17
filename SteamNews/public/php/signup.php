<html>
<head>
  <title>SteamNews - Rejstracja</title>
	<link rel="stylesheet" type="text/css" href="../css/style.css">
  <script>
    function checkform()
    {
      if(document.getElementById("login").value!=undefined&&document.getElementById("password").value!=undefined&&document.getElementById("login").value!=""&&document.getElementById("password").value!="")
      {
        if(document.getElementById("password").value.length>=8)
        {
          if(document.getElementById("password").value.length<=22&&document.getElementById("login").value.length<=22)document.getElementById("Register").submit();
          else document.getElementById("message").innerText="Hasło i login mogą mieć najwyżej po 22 znaki";
        }
        else document.getElementById("message").innerText="Hasło musi składać się z co najmniej 8 znaków";
      }
      else document.getElementById("message").innerText="Wszystkie pola muszą być wypełnione";
    }
  </script>
  <?php

		if(isset($_POST['login'],$_POST['password']))
		{
      $connetion = @new mysqli('localhost','root','','rborzych');
      //$connetion = @new mysqli('localhost','rborzych','rborzych','rborzych');
      if($connetion->connect_errno) {die('Nie można połączyć się z serwerem');$message = "<B>Wystąpił błąd podczas łączenia się z serwerem</B><BR>";}
      if(isset($_POST["login"])&&isset($_POST["password"]))
      {
            $rs=$connetion->query('INSERT INTO `steamnewsclients`(`username`, `password`) VALUES ("'.$_POST["login"].'","'.$_POST["password"].'")');
            $message = "Użytkownik został zarejstrowany";
      }
    }
		else
			$message = false;
	?>
</head>
<body>
  <a href="../../index.php"><div id="returnbutton"><img src="../img/home.png" style="width: 75px;
    height: 75px;"/></div></a>
	<div id="login_box">
  <h1>Formularz rejstracyjny</h1>
  <form id="Register" method="POST">
    <label>Login: <input id="login" type="text" name="login"></label><br/><br/>
    <label>Hasło: <input id="password" type="password" name="password"></label><br/><br/>
    <input onClick="checkform()" type="Button" value="Zarejestruj się"><br/><br/>
  </form>
  <?php
  		echo $message ? $message : "";
		?>
  <div id="message"></div>
	</div>
</body>
</html> 