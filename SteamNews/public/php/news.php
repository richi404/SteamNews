<?php
session_start();
if (!isset($_SESSION["login"])){
  header("Location: ../../index.php");
 exit();
}
?>
<html>
<head>
  <title>SteamNews</title>
  <link rel="stylesheet" type="text/css" href="../css/style.css">
  <script>
  function showhide(a)
  {
    if(a.innerHTML=="Show"){
      a.parentElement.parentElement['style'].height="auto";
      a.innerHTML="Hide";}
    else {a.innerHTML="Show";a.parentElement.parentElement['style'].height="300px";}
  }
  </script>
</head>
<body>
<div id="option_bar">
<form method="POST" action="../../index.php?">
<input type="submit" name="logout" value="Wyloguj"/>
</form>
<form method="POST" action="news.php">
<input type="text" name="game_name"/>
<input type="submit" name="search_game_submit" value="Znajdź"/>
</form>
<form method="POST" action="news.php">
<select name="chose_game_select" id="chose_game_select">
<?php
  function steamsearch($a)
  {
    for($z=1;$z<4;$z++)
    {
      $doc = new DOMDocument();
      @$doc->loadHTMLFile("https://store.steampowered.com/search?term=".$a."&page=".$z);
      $xml = simplexml_import_dom($doc);
      $list = $xml->xpath('//span[@class="title"]');
      for($i=0;$i<sizeof($list);$i++)
      {
        $el=(Array)$list[$i];
       echo '<option>'.end($el).'</option>';
      }
    }
  }
  function steamadd($a)
  {
      $connetion = @new mysqli('localhost','root','','rborzych');
      if($connetion->connect_errno) die('Nie można połączyć się z serwerem');
      $rs=$connetion->query('SELECT `chosen` FROM `steamnewsclients` WHERE `username`="'.$_POST["login"].'" AND `password`="'.$_POST["password"].'"');
      $rec=$rs->fetch_array();
      $pieces = explode(",", $rec[0]);
      $repeat=false;
      for($i=0;$i<sizeof($pieces);$i++)
      {
        if($pieces[$i]==$a)$repeat=true;
      }
      if(!$repeat)
      {
        if($rec[0]!="")$tmp=$rec[0].",".$a;
        else $tmp=$a;
        $rs=$connetion->query('UPDATE `steamnewsclients` SET `chosen`="'.$tmp.'" WHERE `username`="'.$_POST["login"].'" AND `password`="'.$_POST["password"].'"');
      }
  }
  function steamdelete($a)
  {
      $connetion = @new mysqli('localhost','root','','rborzych');
      if($connetion->connect_errno) die('Nie można połączyć się z serwerem');
      $rs=$connetion->query('SELECT `chosen` FROM `steamnewsclients` WHERE `username`="'.$_SESSION["login"].'" AND `password`="'.$_SESSION["password"].'"');
      $rec=$rs->fetch_array();
      $pieces = explode(",", $rec[0]);
      $tmp="";
      for($i=0;$i<sizeof($pieces);$i++)
      {
        if($pieces[$i]!=$a&&$tmp!="")$tmp=$tmp.",".$pieces[$i];
        else if($pieces[$i]!=$a)$tmp=$pieces[$i];
      }
      $rs=$connetion->query('UPDATE `steamnewsclients` SET `chosen`="'.$tmp.'" WHERE `username`="'.$_SESSION["login"].'" AND `password`="'.$_SESSION["password"].'"');
  }
  if(isset($_POST['game_name']))
  {
    steamsearch($_POST['game_name']);
  } 
  if(isset($_POST['add_game']))
  {
    steamadd($_POST['chose_game_select']);
  }
  if(isset($_POST['delete_game']))
  {
    echo $_POST['delete_news'];
    steamdelete($_POST['delete_news']);
  } 
?>
</select>
<input type="submit" name="add_game" value="Dodaj"/>
<?php
  echo '<input type="hidden" name="login" value="'.$_SESSION['login'].'"/>';
  echo '<input type="hidden" name="password" value="'.$_SESSION['password'].'"/>';
?>
</form>
<form method="POST" action="news.php">
<select name="delete_news" id="delete">
<?php
  $connetion = @new mysqli('localhost','root','','rborzych');
  if($connetion->connect_errno) die('Nie można połączyć się z serwerem');
  $rs=$connetion->query('SELECT `chosen` FROM `steamnewsclients` WHERE `username`="'.$_SESSION["login"].'" AND `password`="'.$_SESSION["password"].'"');
  $rec=$rs->fetch_array();
  $pieces = explode(",", $rec[0]);
  foreach($pieces as $value)
  {
    echo '<option>'.$value.'</option>';
  }
?></select><input type="submit" name="delete_game" value="Delete"/></form>
</div>
<div id="news">
<?php
class OneNew { 
  private $date; 
  private $name; 
  private $content;
  private function bake($title)
  {
      echo '<div class="item"><div class="newstitle">'.$title.'</div>';
      echo '<div class="item_name">'.$this->date." ".$this->name.'<button onClick="showhide(this)">Show</button></div>';
      echo '<div class="item_data"><br/>'.$this->content.'</div>';
      echo '</div>';
  }
  function __construct($Data, $title) {
      preg_match('!<h2>(.*?)<\/h2>!', $Data, $this->date);
      preg_match('!<h1>(.*?)<\/h1>!', $Data, $this->name);
      preg_match('!<\/h1>(.*?)<div class="hr">!', $Data, $this->content);
      $this->name=$this->name[0];
      $this->content=$this->content[0];
      $this->date=$this->date[0];
      $this->bake($title);
  }
}
function main($a)
{
  $doc = new DOMDocument();
  @$doc->loadHTMLFile("https://store.steampowered.com/search?term=".$a);
  $xml = simplexml_import_dom($doc);
  $list = $xml->xpath('//span[@class="title" and text()="'.$a.'"]/../../../@data-ds-appid');
  @$el=(Array)$list{0}->{'data-ds-appid'};
  @$url = 'https://store.steampowered.com/app/'.$el[0].'/';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_COOKIE, "birthtime=28801; path=/; domain=store.steampowered.com");
  curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);
  $result=preg_replace('/\s+/', ' ', $result);
  $result=preg_replace('/\s+/', ' ', $result);
  preg_match_all('!<div class="post [^>]+>(.*?)<span class="more">!', $result, $result);
  $result=$result[1];
  if(isset($result[0]))
  {
    $new = new OneNew($result[0],$a);
    $new2 = new OneNew($result[1],$a);
  }
}
foreach($pieces as $value)
{
  main($value);
}
?>
</div>
</body>
</html>
