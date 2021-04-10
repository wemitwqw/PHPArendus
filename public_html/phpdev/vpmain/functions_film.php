<?php
  $weekdayNamesET = ["esmaspäev", "teisipäev", "kolmapäev", "neljapäev", "reede", "laupäev", "pühapäev"];
  $monthNamesET = ["jaanuar", "veebruar", "märts", "aprill", "mai", "juuni", "juuli", "august", "september", "oktoober", "november", "detsember"];

  function showFullDataByPerson(){
	  $filmInfoHTML = null;
	  $conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	  $stmt = $conn->prepare("SELECT AMET.Nimetus, ISIK.Eesnimi, ISIK.Perekonnanimi, ISIK.Synniaeg, ISIK_FILMIS.Roll, FILM.Pealkiri, FILM.Aasta, FILM.Kestus, FILM.Sisukokkuv6te FROM ISIK JOIN ISIK_FILMIS ON ISIK.Isik_ID = ISIK_FILMIS.ISIK_Isik_ID JOIN FILM ON FILM.Film_ID = ISIK_FILMIS.FILM_Film_ID JOIN AMET ON ISIK_FILMIS.AMET_Amet_ID = AMET.Amet_ID");
	  echo $conn->error;
	  $stmt->bind_result($professionFromDb, $firstnameFromDb, $lastnameFromDb, $birthFromDb, $roleFromDb, $filmTitleFromDb, $filmYearFromDb, $filmDurationFromDb, $filmSummaryFromDb);
	  $stmt->execute();
	  while($stmt->fetch()){
		  $birthDate = new DateTime($birthFromDb);
		  $birthYear = $birthDate->format("Y");
		  $birthMonth = $birthDate->format("n");
		  $birthDay = $birthDate->format("j");
		  $birthDayDesc = $birthDay .". " .$GLOBALS["monthNamesET"][$birthMonth-1] ." " .$birthYear;
		  $filmInfoHTML .= "\t <li>" .$professionFromDb ." <strong>" .$firstnameFromDb ." " .$lastnameFromDb ."</strong>. Sündinud: " .$birthDayDesc .". ";
		  $filmInfoHTML .= '<br>Film: "' .$filmTitleFromDb .'", ' .$roleFromDb;
		  $filmInfoHTML .= "<br>Filmi valmimisaasta : " .$filmYearFromDb;
		  
		  $filmHours = round($filmDurationFromDb / 60, 0);
		  $filmMinutes = $filmDurationFromDb%60;
		  $filmDurationDesc = null;
		  if ($filmHours > 0){
		    if($filmHours == 1){
				$filmDurationDesc .= $filmHours ." tund ja ";
			} else {
				$filmDurationDesc .= $filmHours ." tundi ja ";
			}
			if($filmMinutes == 1){
				$filmDurationDesc .= $filmMinutes ." minut";
			} else {
				$filmDurationDesc .= $filmMinutes ." minutit";
			}
		  }
		  $filmInfoHTML .= ", kestus: " .$filmDurationDesc ."<br>";
		  $filmInfoHTML .= "Lühikokkuvõte: " .$filmSummaryFromDb ."</li> \n";
	  }
	  if($filmInfoHTML == null){
		  $filmInfoHTML = "<p>Andmebaasis pole ühtki filmi!</p>";
	  } else {
		  $filmInfoHTML = "<ul> \n" .$filmInfoHTML ."\n </ul> \n";
	  }
	  $stmt->close();
	  $conn->close();
	  return $filmInfoHTML;
  }
  
  function addFilmPerson($firstname, $surname, $birthDate){
	  $notice = null;
	  $conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	  $stmt = $conn->prepare("SELECT Isik_ID FROM ISIK WHERE Eesnimi=? and Perekonnanimi=? and Synniaeg=?");
	  echo $conn->error;
	  $stmt->bind_param("sss", $firstname, $surname, $birthDate);
	  $stmt->bind_result($idFromDb);
	  $stmt->execute();
	  if($stmt->fetch()){
		  $notice = "Selline isik on juba andmebaasis olemas!";
	  } else {
		  $stmt->close();
		  $stmt = $conn->prepare("INSERT INTO ISIK (Eesnimi, Perekonnanimi, Synniaeg) VALUES(?,?,?)");
		  echo $conn->error;
		  $stmt->bind_param("sss", $firstname, $surname, $birthDate);
		  if($stmt->execute()){
			  $notice = 1;
			  $_SESSION["filmPersonAdded"] = $stmt->insert_id;
		  } else {
			  $notice = "Uue isiku lisamisel tekkis tehniline tõrge: " .$stmt->error;
		  }
	  }
	  $stmt->close();
	  $conn->close();
	  return $notice;
  }
  
  function addFilm($filmTitle, $filmYear, $filmDuration, $filmDescription){
	  $notice = null;
	  $conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	  $stmt = $conn->prepare("SELECT Film_ID FROM FILM WHERE Pealkiri=? and Aasta=?");
	  echo $conn->error;
	  $stmt->bind_param("si", $filmTitle, $filmYear);
	  $stmt->bind_result($idFromDb);
	  $stmt->execute();
	  if($stmt->fetch()){
		  $notice = "Selline film on juba andmebaasis olemas!";
	  } else {
		  $stmt->close();
		  $stmt = $conn->prepare("INSERT INTO FILM (Pealkiri, Aasta, Kestus, Sisukokkuv6te) VALUES(?,?,?,?)");
		  echo $conn->error;
		  $stmt->bind_param("siis", $filmTitle, $filmYear, $filmDuration, $filmDescription);
		  if($stmt->execute()){
			  $notice = 1;
			  $_SESSION["filmAdded"] = $stmt->insert_id;
		  } else {
			  $notice = "Uue filmi lisamisel tekkis tehniline tõrge: " .$stmt->error;
		  }
	  }
	  $stmt->close();
	  $conn->close();
	  return $notice;
  }
  
  function addFilmProfession($profession){
	  $notice = null;
	  $conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	  $stmt = $conn->prepare("SELECT Amet_ID FROM AMET WHERE Nimetus=?");
	  echo $conn->error;
	  $stmt->bind_param("s", $profession);
	  $stmt->bind_result($idFromDb);
	  $stmt->execute();
	  if($stmt->fetch()){
		  $notice = "Selline amet on juba andmebaasis olemas!";
	  } else {
		  $stmt->close();
		  $stmt = $conn->prepare("INSERT INTO AMET (Nimetus) VALUES(?)");
		  echo $conn->error;
		  $stmt->bind_param("s", $profession);
		  if($stmt->execute()){
			  $notice = 1;
			  $_SESSION["filmProfessionAdded"] = $stmt->insert_id;
		  } else {
			  $notice = "Uue ameti lisamisel tekkis tehniline tõrge: " .$stmt->error;
		  }
	  }
	  $stmt->close();
	  $conn->close();
	  return $notice;
  }
  
  function readAllPersonsForSelect(){
	  $personsHTML = null;
	  $maxPersonId = 0;
	  if(isset($_SESSION["filmPersonAdded"]) and !empty($_SESSION["filmPersonAdded"])){
		  $maxPersonId = $_SESSION["filmPersonAdded"];
	  }
	  $conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	  $stmt = $conn->prepare("SELECT Isik_ID, Eesnimi, Perekonnanimi, Synniaeg FROM ISIK ORDER BY Perekonnanimi");
	  echo $conn->error;
	  $stmt->bind_result($idFromDb, $firstnameFromDb, $lastnameFromDb, $birthFromDb);
	  $stmt->execute();
	  while($stmt->fetch()){
	  	  $personsHTML .= '<option value="' .$idFromDb .'"';
		  if($idFromDb == $maxPersonId){
			  $personsHTML .= " selected";
		  }
		  $personsHTML .= ">" .$firstnameFromDb . " " .$lastnameFromDb .", sündinud: " .$birthFromDb ."</option> \n";
	  }
	  $stmt->close();
	  $conn->close();
	  return $personsHTML;
  }
  
  function readAllFilmsForSelect(){
	  $filmHTML = null;
	  $maxFilmId = 0;
	  if(isset($_SESSION["filmAdded"]) and !empty($_SESSION["filmAdded"])){
		  $maxFilmId = $_SESSION["filmAdded"];
	  }
	  $conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	  $stmt = $conn->prepare("SELECT Film_ID, Pealkiri, Aasta FROM FILM ORDER BY Pealkiri");
	  echo $conn->error;
	  $stmt->bind_result($idFromDb, $filmTitleFromDB, $filmYearFromDb);
	  $stmt->execute();
	  while($stmt->fetch()){
	  	  $filmHTML .= '<option value="' .$idFromDb .'"';
		  if($idFromDb == $maxFilmId){
			  $filmHTML .= " selected";
		  }
		  $filmHTML .= ">" .$filmTitleFromDB . ", valminud: " .$filmYearFromDb ."</option> \n";
	  }
	  $stmt->close();
	  $conn->close();
	  return $filmHTML;
  }
  
  function readAllProfessionsForSelect(){
	  $professionHTML = null;
	  $maxProfessionId = 0;
	  if(isset($_SESSION["filmProfessionAdded"]) and !empty($_SESSION["filmProfessionAdded"])){
		  $maxProfessionId = $_SESSION["filmProfessionAdded"];
	  }
	  $conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	  $stmt = $conn->prepare("SELECT Amet_ID, Nimetus FROM AMET ORDER BY Nimetus");
	  echo $conn->error;
	  $stmt->bind_result($idFromDb, $professionFromDb);
	  $stmt->execute();
	  while($stmt->fetch()){
	  	  $professionHTML .= '<option value="' .$idFromDb .'"';
		  if($idFromDb == $maxProfessionId){
			  $professionHTML .= " selected";
		  }
		  $professionHTML .= ">" .$professionFromDb ."</option> \n";
	  }
	  $stmt->close();
	  $conn->close();
	  return $professionHTML;  
  }
  
  function addFilmRelation($person, $film, $profession, $filmRole){
	  $notice = null;
	  $conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	  $stmt = $conn->prepare("SELECT Isik_filmis_ID FROM ISIK_FILMIS WHERE ISIK_Isik_ID=? and FILM_Film_ID=? and AMET_Amet_ID=? and Roll=?");
	  echo $conn->error;
	  $stmt->bind_param("iiis", $person, $film, $profession, $filmRole);
	  $stmt->bind_result($idFromDb);
	  $stmt->execute();
	  if($stmt->fetch()){
		  $notice = "Selline seos on juba andmebaasis olemas!";
	  } else {
		  $stmt->close();
		  $stmt = $conn->prepare("INSERT INTO ISIK_FILMIS (ISIK_Isik_ID, FILM_Film_ID, AMET_Amet_ID, Roll) VALUES(?,?,?,?)");
		  echo $conn->error;
		  $stmt->bind_param("iiis", $person, $film, $profession, $filmRole);
		  if($stmt->execute()){
			  $notice = 1;
		  } else {
			  $notice = "Uue seose lisamisel tekkis tehniline tõrge: " .$stmt->error;
		  }
	  }
	  $stmt->close();
	  $conn->close();
	  
	  return $notice;
  }