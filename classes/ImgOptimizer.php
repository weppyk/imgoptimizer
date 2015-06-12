
<?php

class ImgOptimizer 
{
	public $adresar, $origImgFolder;
	public $vstupniSoubor;
	public $vystupniSoubor;
	public $maxVelikost;
	public $komprimace;
	public function __construct($vstupniSoubor,$vystupniSoubor,$maxVelikost,$komprimace) 
	{
		$this->vstupniSoubor=$vstupniSoubor;
		$this->vystupniSoubor=$vystupniSoubor;
		$this->maxVelikost=$maxVelikost;
		$this->komprimace=$komprimace;
	}
	protected function imageCreateFromBmp($p_sFile) 
    { 
        //    Load the image into a string 
        $file    =    fopen($p_sFile,"rb"); 
        $read    =    fread($file,10); 
        while(!feof($file)&&($read<>"")) 
            $read    .=    fread($file,1024); 
        
        $temp    =    unpack("H*",$read); 
        $hex    =    $temp[1]; 
        $header    =    substr($hex,0,108); 
        
        //    Process the header 
        //    Structure: http://www.fastgraph.com/help/bmp_header_format.html 
        if (substr($header,0,4)=="424d") 
        { 
            //    Cut it in parts of 2 bytes 
            $header_parts    =    str_split($header,2); 
            
            //    Get the width        4 bytes 
            $width            =    hexdec($header_parts[19].$header_parts[18]); 
            
            //    Get the height        4 bytes 
            $height            =    hexdec($header_parts[23].$header_parts[22]); 
            
            //    Unset the header params 
            unset($header_parts); 
        } 
        
        //    Define starting X and Y 
        $x                =    0; 
        $y                =    1; 
        
        //    Create newimage 
        $image            =    imagecreatetruecolor($width,$height); 
        
        //    Grab the body from the image 
        $body            =    substr($hex,108); 

        //    Calculate if padding at the end-line is needed 
        //    Divided by two to keep overview. 
        //    1 byte = 2 HEX-chars 
        $body_size        =    (strlen($body)/2); 
        $header_size    =    ($width*$height); 

        //    Use end-line padding? Only when needed 
        $usePadding        =    ($body_size>($header_size*3)+4); 
        
        //    Using a for-loop with index-calculation instaid of str_split to avoid large memory consumption 
        //    Calculate the next DWORD-position in the body 
        for ($i=0;$i<$body_size;$i+=3) 
        { 
            //    Calculate line-ending and padding 
            if ($x>=$width) 
            { 
                //    If padding needed, ignore image-padding 
                //    Shift i to the ending of the current 32-bit-block 
                if ($usePadding) 
                    $i    +=    $width%4; 
                
                //    Reset horizontal position 
                $x    =    0; 
                
                //    Raise the height-position (bottom-up) 
                $y++; 
                
                //    Reached the image-height? Break the for-loop 
                if ($y>$height) 
                    break; 
            } 
            
            //    Calculation of the RGB-pixel (defined as BGR in image-data) 
            //    Define $i_pos as absolute position in the body 
            $i_pos    =    $i*2; 
            $r        =    hexdec($body[$i_pos+4].$body[$i_pos+5]); 
            $g        =    hexdec($body[$i_pos+2].$body[$i_pos+3]); 
            $b        =    hexdec($body[$i_pos].$body[$i_pos+1]); 
            
            //    Calculate and draw the pixel 
            $color    =    imagecolorallocate($image,$r,$g,$b); 
            imagesetpixel($image,$x,$height-$y,$color); 
            
            //    Raise the horizontal position 
            $x++; 
        } 
        
        //    Unset the body / free the memory 
        unset($body); 
        
        //    Return image-object 
        return $image; 
    } 


	public function optimizuj()
	{
		$delsiStrana='0';
		list($width,$height,$pripona) = getimagesize($this->vstupniSoubor);
        
		echo $width;echo $height;
//Nacte obrazek podle typu souboru
		
		switch ($pripona) {
			case '2': //.jpeg
				$img = imagecreatefromjpeg($this->vstupniSoubor); echo 'Pripona je jpeg';
				break;
			case '3': //.png
				$img = imagecreatefromPng($this->vstupniSoubor); echo 'Pripona je png';
                echo $this->vstupniSoubor;

				break;
			case '6': //.bmp
				$img = $this->imageCreateFromBmp($this->vstupniSoubor); echo 'Pripona je bmp';

				break;
			default:
				# code...
				break;
		}
	//	if (strpos($this->vstupniSoubor,'.jpeg'))
	//	{
	//		$img = imagecreatefromjpeg($this->vstupniSoubor);
	//		echo 'Pripona je .jpg<br>';
	//	}

	
        //vytvori pomer stranky
		$ratio=$height/$width;
        //najde vetsi stranu
        if ($width>=$height)
        {
            
                $newWidth=$this->maxVelikost;
                $newHeight=$this->maxVelikost*$ratio;
                if ($width<=$this->maxVelikost)
                    {$newWidth=$width; $newHeight=$height;}
        }   else
        {         
                $newWidth=$this->maxVelikost/$ratio;
                $newHeight=$this->maxVelikost;
                if($height<=$this->maxVelikost)
                    {$newHeight=$height; $newWidth=$width;}
        } 

 		// create a new temporary image
    	$tmp_img = imagecreatetruecolor($newWidth,$newHeight);
        $white = imagecolorallocate($tmp_img, 255,255,255);
        imagefilledrectangle($tmp_img, 0, 0, $newWidth,$newHeight, $white);
    	// copy and resize old image into new image
    	imagecopyresampled($tmp_img, $img, 0, 0, 0, 0,$newWidth,$newHeight, $width, $height );
 
		echo $delsiStrana;
    	
    
    	// use output buffering to capture outputted image stream
    	ob_start();
    	imagejpeg($tmp_img);
    	$i = ob_get_clean(); 
    	//vraci obrazek zpet jako hodnotu
    	return $i;
	}

    function saveFile($fileName, $string) {
        $fp = fopen($fileName, 'w');
        fwrite($fp, string);
        fclose($fp);

    }
	public function ulozOptImg(){
        //$vystupniSoubor=
        echo $this->vystupniSoubor;
        $this->saveFile($this->vystupniSoubor,$this->optimizuj);

	//	$fp = fopen ($this->vystupniSoubor,'w');
    //
    //	fwrite ($fp, $this->optimizuj());
    //	fclose ($fp);
	}
    public function ulozWebp(){
        // Create a blank image and add some text
        $im = imagecreatefromjpeg($this->vstupniSoubor);

        //$text_color = imagecolorallocate($im, 233, 14, 91);

        //imagestring($im, 1, 5, 5,  'WebP with PHP', $text_color);  

        // Save the image
        imagewebp($im, 'php.webp');

        // Free up memory
        //imagedestroy($im);
    }

public function ulozOrigImg()
       	{
		$pripona=zjistiPriponu();
        $content= file_get_contents($this->vstupniSoubor);
        $this->saveFile($this->origImgFolder.'/'.$this->vystupniSoubor,$content);
        echo '<br> originál byl uložen <br>';
		
	}
	public function optimizujAdresar($inputFolder, $outputFolder){

	}
	public function test(){
		echo '<b>Vstupní soubor je: </b>'.$this->vstupniSoubor.'<br>';
		echo '<b>Vystupní soubor je: </b>'.$this->vystupniSoubor.'<br>';
		echo 'Maximální velikost je: '.$this->maxVelikost.'<br>';
		echo 'Použita komprimace: '.$this->komprimace;

		echo '<br>Vystup byl odeslan...';
	}
}