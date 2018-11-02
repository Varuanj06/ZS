<?php

	//copy('http://url.com/6478-95583-thickbox/95583.jpg', '/file.jpeg');

	$imageString = file_get_contents("http://url.com/6478-95583-thickbox/95583.jpg");
	//file_put_contents('downloads/image.jpg',$imageString);


	$thumbWidth = 240;
	$img = imagecreatefromstring( $imageString );
    
    $width = imagesx( $img );
    $height = imagesy( $img );

    // calculate thumbnail size
    $new_width = $thumbWidth;
    $new_height = floor( $height * ( $thumbWidth / $width ) );

    // create a new temporary image
    $tmp_img = imagecreatetruecolor( $new_width, $new_height );

    // copy and resize old image into new image 
    //imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height ); //old bad-quality
    imagecopyresampled( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height ); //ned better-quality



    // save thumbnail into a file
    imagejpeg( $tmp_img, 'downloads/image.jpg', 80 ); //added the ", 80" for better quality