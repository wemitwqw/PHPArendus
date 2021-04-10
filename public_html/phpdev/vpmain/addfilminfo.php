<?php
  require("../../../dbconfig.php");
  require("functions_main.php");
  require("functions_user.php");
  require("functions_film.php");
  $database = "$dbname";
  
  //kui pole sisseloginud
  if(!isset($_SESSION["userID"])){
	  //siis jõuga sisselogimise lehele
	  header("Location: page.php");
	  exit();
  }
  
  //väljalogimine
  if(isset($_GET["logout"])){
	  session_destroy();
	  header("Location: page.php");
	  exit();
  }
  
  $userName = $_SESSION["userFirstname"] ." " .$_SESSION["userLastname"];
  
  $firstname = null;
  $surname = null;
  $firstnameError = null;
  $surnameError = null;
  $birthDate = null;
  $birthDateError = null;
  $personNotice = null;
  
  $filmTitle = null;
  $filmTitleError = null;
  $currentDate = new DateTime("now");
  $currentYear = $currentDate->format("Y");
  $filmYear = $currentYear;
  $filmYearError = null;
  $filmDuration = 80;
  $filmDurationError = null;
  $filmDescription = null;
  $filmNotice = null;
  
  $profession = null;
  $professionError = null;
  $professionNotice = null;
  
  $person = null;
  $film = null;
  $filmProfession = null;
  $filmRole = null;
  $relationError = null;
  $relationNotice = null;
  
  //var_dump($_POST);
  
  if(isset($_POST["submitPerson"])){
	  if(isset($_POST["firstName"]) and !empty(test_input($_POST["firstName"]))){
		  $firstname = test_input($_POST["firstName"]);
	  } else {
		  $firstnameError = "Palun kirjutage eesnimi!";
	  }
	  
	  if(isset($_POST["surName"]) and !empty(test_input($_POST["surName"]))){
		  $surname = test_input($_POST["surName"]);
	  } else {
		  $surnameError = "Palun kirjutage eesnimi!";
	  }
	  
	  if(!empty($_POST["birthDate"])){
		  $birthDate = $_POST["birthDate"];
	  } else {
		  $birthDateError = "Palun valige filmitegelase sünnikuupäev!";
	  }
	  
	  if(empty($firstnameError) and empty($surnameError) and empty($birthDateError)){
		  $personNotice = addFilmPerson($firstname, $surname, $birthDate);
		  if($personNotice == 1){
			  $firstname = null;
			  $surname = null;
			  $birthDate = null;
			  $personNotice = "Uue isiku lisamine õnnestus!";
		  }
	  }
  }
  
  if(isset($_POST["submitFilm"])){
	  if(isset($_POST["filmTitle"]) and !empty(test_input($_POST["filmTitle"]))){
		  $filmTitle = test_input($_POST["filmTitle"]);
	  } else {
		  $filmTitleError = "Palun kirjutage filmi pealkiri!";
	  }
	  
	  if(isset($_POST["filmYear"]) and !empty($_POST["filmYear"])){
		  $filmYear = $_POST["filmYear"];
	  } else {
		  $filmYearError = "Palun määrake filmi valmimisaasta!";
	  }
	  
	  if(isset($_POST["filmDuration"]) and !empty($_POST["filmDuration"])){
		  $filmDuration = $_POST["filmDuration"];
	  } else {
		  $filmDurationError = "Palun määrake filmi valmimisaasta!";
	  }
	  
	  $filmDescription = test_input($_POST["filmDescription"]);
	  
	  if(empty($filmTitleError) and empty($filmYearError) and empty($filmDurationError)){
		  $filmNotice = addFilm($filmTitle, $filmYear, $filmDuration, $filmDescription);
		  if($filmNotice == 1){
			  $filmTitle = null;
			  $filmYear = null;
			  $filmDuration = null;
			  $filmDescription = null;
			  $filmNotice = "Uue filmi lisamine õnnestus!";
		  }
		  
	  }
  }
  
  if(isset($_POST["submitProfession"])){
	  if(isset($_POST["profession"]) and !empty(test_input($_POST["profession"]))){
		  $profession = test_input($_POST["profession"]);
	  } else {
		  $professionError = "Palun kirjutage ametinimetus!";
	  }
	    
	  if(empty($professionError)){
		  $professionNotice = addFilmProfession($profession);
		  if($professionNotice == 1){
			  $profession = null;
			  $professionNotice = "Uue ameti lisamine õnnestus!";
		  }
	  }
  }
  
  $personsHTML = readAllPersonsForSelect();
  $filmsHTML = readAllFilmsForSelect();
  $professionsHTML = readAllProfessionsForSelect();
  
  if(isset($_POST["submitRelation"])){
	  if(isset($_POST["person"]) and !empty($_POST["person"])){
		  $person = $_POST["person"];
	  } else {
		  $relationError .= "Palun vali filmitegelane! ";
	  }
	  
	  if(isset($_POST["film"]) and !empty($_POST["film"])){
		  $film = $_POST["film"];
	  } else {
		  $relationError .= "Palun vali film! ";
	  }
	  
	  if(isset($_POST["profession"]) and !empty($_POST["profession"])){
		  $filmProfession = $_POST["profession"];
	  } else {
		  $relationError .= "Palun vali amet! ";
	  }
	  
	  $filmRole = test_input($_POST["role"]);
	  
	  if(empty($relationError)){
		  $relationNotice = addFilmRelation($person, $film, $filmProfession, $filmRole);
		  if($relationNotice == 1){
			  $filmRole = null;
			  unset($_SESSION["filmPersonAdded"]);
			  unset($_SESSION["filmAdded"]);
			  unset($_SESSION["filmProfessionAdded"]);
			  $relationNotice = "Uue seose lisamine õnnestus!";
		  }
	  }
	}
  
  require("header.php");
?>

  <?php
    echo "<h1>" .$userName ." koolitöö leht</h1>";
  ?>
  <p>See leht on loodud koolis õppetöö raames
  ja ei sisalda tõsiseltvõetavat sisu!</p>
  <hr>
  <p><a href="?logout=1">Logi välja!</a> | Tagasi <a href="home.php">avalehele</a></p>
  <h2>Eesti filmid ja filmitegelased</h2>
  <p>Tagasi info <a href="showfilminfo.php">lehele</a>!</p>
  <hr>
  <h3>Uue filmitegelase lisamine</h3>
  
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="display:inline;">
	  <label>Eesnimi:</label>
	  <input name="firstName" type="text" value="<?php echo $firstname; ?>"><span><?php echo $firstnameError; ?></span><br>
      <label>Perekonnanimi:</label>
	  <input name="surName" type="text" value="<?php echo $surname; ?>"><span><?php echo $surnameError; ?></span><br>
	  <label>Sünniaeg: </label><input type="date" name="birthDate" value="<?php echo $birthDate; ?>"><span><?php echo $birthDateError; ?></span><br>
	  <input name="submitPerson" type="submit" value="Lisa filmitegelane"><span><?php echo $personNotice; ?></span>
  </form>
  <hr>
  
    <h3>Uue filmi lisamine</h3>
  
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="display:inline;">
	  <label>Filmi pealkiri:</label>
	  <input name="filmTitle" type="text" value="<?php echo $filmTitle; ?>"><span><?php echo $filmTitleError; ?></span><br>
      <label>Filmi valmimisaasta:</label>
	  <input name="filmYear" type="number" min="1912" max="<?php echo $currentYear; ?>" value="<?php echo $filmYear; ?>"><span><?php echo $filmYearError; ?></span><br>
	  <label>Filmi kestus (minutid): </label><input type="number" name="filmDuration" min="1" max="300" value="<?php echo $filmDuration; ?>"><span><?php echo $filmDurationError; ?></span><br>
	  <label>Filmi lühikokkuvõte</label><br>
	  <textarea rows="5" cols="100" name="filmDescription" placeholder="Lisa siia filmi lühitutvustus ..."><?php echo $filmDescription; ?></textarea><br>
	  <input name="submitFilm" type="submit" value="Lisa film"><span><?php echo $filmNotice; ?></span>
  </form>
  <hr>
  
  <h3>Uue filmiga seotud ameti lisamine</h3>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="display:inline;">
	  <label>Amet:</label>
	  <input name="profession" type="text" value="<?php echo $profession; ?>"><span><?php echo $professionError; ?></span>
      <input name="submitProfession" type="submit" value="Lisa amet"><span><?php echo $professionNotice; ?></span>
  </form>

	
	<hr>
	
	<h3>Tegelase ja filmi seose lisamine</h3>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="display:inline;">
	  <label>Filmitegelane: </label>
	  <select name="person">
	    <option value="" selected disabled>Vali filmitegelane</option>
		<?php echo $personsHTML; ?>
	  </select>
	  
	  <label> Film: </label>
	  <select name="film">
	    <option value="" selected disabled>Vali film</option>
		<?php echo $filmsHTML; ?>
	  </select>
	  
	  <label> Amet: </label>
	  <select name="profession">
	    <option value="" selected disabled>Vali amet</option>
		<?php echo $professionsHTML; ?>
	  </select>
	  
	  <label> Roll: </label>
	  <input name="role" type="text" value="<?php echo $filmRole; ?>" placeholder="Näitleja jaoks ka roll">
	  <br>
	  <input name="submitRelation" type="submit" value="Lisa seos"><span><?php echo $relationError .$relationNotice; ?></span>
	</form>

</body>
</html>





