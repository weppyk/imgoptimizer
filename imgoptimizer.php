<!DOCTYPE html>

<html lang="cs-cz">
    <head>
            <meta charset="utf-8" />
            <title>OOP na devbooku</title>
    </head>

    <body>


<?php
	require('classes/ImgOptimizer.php');
	$optimizer = new ImgOptimizer('1.jpg','vystup3.jpg',600,75);
    $optimizer->origImgFolder='imagesOriginal';
    $optimizer->optImgFolder='images';
	$optimizer->test();
	$optimizer->ulozOptImg();
//	$optimizer->ulozWebp();
 //   $optimizer->ulozOrigImg('http://herniweb.cz/wp-content/uploads/divos3.jpg');

?>



    </body>
</html>