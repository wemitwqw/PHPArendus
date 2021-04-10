<?php
	require("../../../dbconfig.php");
	require("functions_main.php");
	require("functions_user.php");
	require("functions_pic.php");
	require("classes/PicUpload.class.php");
	$database = "if19_vladislav_pr_1";
  
	//kui pole sisseloginud
	if(!isset($_SESSION["userID"])){
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
	
	//võtame vastu saadetud info
	$rating = $_REQUEST["rating"];
	$photoID = $_REQUEST["photoID"];
	
	$response = saveRating($photoID, $rating);
	echo $response;

	
	/* samamoodi saada pildi id
	siis vaja andmebaasi [hendust ja sessioonimuutujaid
	[hendus andmebaasi, k]igepealt salvestad hinde *pildi id, kasutaja id, kes hindas ja hinne.
	Siis j'rgmine SQL lause, loed keskmise hinde ja tagastad selle echo k'suga. */