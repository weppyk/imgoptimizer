<!DOCTYPE html>

<html lang="cs-cz">
    <head>
            <meta charset="utf-8" />
            <title>OOP na devbooku</title>
    </head>

    <body>


<?php
	require('classes/ImgOptimizer.php');
	$optimizer = new ImgOptimizer('imagesOriginal/1.jpg','images/vystup1.jpg',600,75);
    $optimizer->origImgFolder='imagesOriginal';
	$optimizer->test();
	$optimizer->ulozOptImg();
	$optimizer->ulozWebp();
    $optimizer->ulozOrigImg();

?>



    </body>
</html>