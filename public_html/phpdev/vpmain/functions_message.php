<?php
function storeMessage($message){
	$notice = null;
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn->prepare("INSERT INTO vpmsg (userid, message) VALUES(?,?)");
	echo $conn->error;
	$stmt->bind_param("is", $_SESSION["userID"], $message);
	if($stmt->execute()){
		$notice = "Sõnum on salvestatud!";
	} else {
		$notice = "Sõnumit ei õnnestunud tehnilisel põhjusel salvestada! " .$stmt->error;
	}
	$stmt->close();
	$conn->close();
	return $notice;
}

function readAllMessages(){
	$notice = null;
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	//$stmt = $conn->prepare("SELECT message, created FROM vpmsg");
	$stmt = $conn->prepare("SELECT message, created FROM vpmsg WHERE deleted IS NULL");
    echo $conn->error;
	$stmt->bind_result($messageFromDb, $createdFromDb);
	$stmt->execute();
	while($stmt->fetch()){
		$notice .= "<p>" .$messageFromDb ." (Lisatud: " .$createdFromDb .")</p> \n";
	}
	if(empty($notice)){
		$notice = "<p>Otsitud sõnumeid pole!</p> \n";
	}
	
	$stmt->close();
	$conn->close();
	return $notice;
}

function readMyMessages(){
	$notice = null;
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	//$stmt = $conn->prepare("SELECT message, created FROM vpmsg");
	$stmt = $conn->prepare("SELECT message, created FROM vpmsg WHERE userid = ? AND deleted IS NULL");
    echo $conn->error;
	$stmt->bind_param("i", $_SESSION["userID"]);
	$stmt->bind_result($messageFromDb, $createdFromDb);
	$stmt->execute();
	while($stmt->fetch()){
		$notice .= "<p>" .$messageFromDb ." (Lisatud: " .$createdFromDb .")</p> \n";
	}
	if(empty($notice)){
		$notice = "<p>Otsitud sõnumeid pole!</p> \n";
	}
	
	$stmt->close();
	$conn->close();
	return $notice;
}