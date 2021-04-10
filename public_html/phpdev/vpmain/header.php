<?php 
  $pageHeaderHTML = "<!DOCTYPE html> \n";
  $pageHeaderHTML .= '<html lang="et">'. "\n";
  $pageHeaderHTML .= "<head> \n";
  $pageHeaderHTML .= "\t" .'<meta charset="utf-8">' ."\n \t<title>" .$userName ."</title> \n";
  $pageHeaderHTML .= "\t" ."<style> \n";
  $pageHeaderHTML .= "\t \t body{background-color: " .$_SESSION["bgColor"] ."; \n";
  $pageHeaderHTML .= "\t \t color: " .$_SESSION["txtColor"] ."\n";
  $pageHeaderHTML .= "\t }";
  $pageHeaderHTML .= "</style> \n";
  $pageHeaderHTML .= $toScript;
  $pageHeaderHTML .= "</head>";
  echo $pageHeaderHTML;
?>