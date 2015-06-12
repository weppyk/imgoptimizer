<?php
	$readFile='1.jpg';
	$saveFile='vystup.webp';
	imagewebp(imagecreatefromjpeg($readFile),$saveFile);
?>