<?php

function readAllNews(){
	$notice = null;
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn->prepare("SELECT title, content, pictureid FROM vpeksamnews");
    echo $conn->error;
	$stmt->bind_result($newsTitleDB, $newsContentDB, $newsPictureIDDB);
	$stmt->execute();
	while($stmt->fetch()){
		$notice .= "<p>" .$newsTitleDB ." \n " .$newsTitleDB ."</p> \n";
	}
	if(empty($notice)){
		$notice = "<p>Otsitud sõnumeid pole!</p> \n";
	}
	
	$stmt->close();
	$conn->close();
	return $notice;

}