<?php

function simpleCaptcha($config = array()) {

    // Check for GD library
    if( !function_exists('gd_info') ) {
        throw new Exception('Required GD library is missing');
    }

    $bg_path = dirname(__FILE__) . '/../images/';
    $font_path = dirname(__FILE__) . '/../assets/fonts/';

    // Default values
    $captcha_config = array(
        'code' => '',
        'min_length' => 5,
        'max_length' => 7,
        'img_width' => 200,
        'img_height' => 50,
        'fonts' => array(
            $font_path . 'times_new_yorker.ttf',
            $font_path . 'monofont.ttf',
            /*$font_path . 'Surabanglus.ttf',
            $font_path . 'Pigeon Wing.otf',
            $font_path . 'Blue lines.ttf',
            $font_path . 'Chefs Slice Novice.ttf',
            $font_path . 'Davys-Ribbons Regular.ttf',
            $font_path . 'Franklin05 Becker.ttf',
            $font_path . 'FranksHand Regular.ttf'
            $font_path . 'SS Barracuda St.ttf',
            $font_path . 'BlackDahlia.ttf',
            $font_path . 'Freakshow.ttf',
            $font_path . 'Small Town Skyline.ttf',
            $font_path . 'Skyscraper_by_Klyukin.ttf',
            $font_path . 'Insektofobiya.otf',*/
            
        ),
        'characters' => 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghjkmnprstuvwxyz23456789',
        'min_font_size' => 22,
        'max_font_size' => 28,
        'backgroundcolor' => '#fff',
        'color' => array('#f00','#000'),
        'angle_min' => 0,
        'angle_max' => 5,
        'shadow' => true,
        'shadow_color' => '#fff',
        'shadow_offset_x' => -1,
        'shadow_offset_y' => 1,
        'noicelines' => 7,
        'noicecolor' => '#162453',
        'noicedots' => 15
    );

    // Overwrite defaults with custom config values
    if( is_array($config) ) {
        foreach( $config as $key => $value ) $captcha_config[$key] = $value;
    }

    // Restrict certain values
    if( $captcha_config['min_length'] < 1 ) $captcha_config['min_length'] = 1;
    if( $captcha_config['angle_min'] < 0 ) $captcha_config['angle_min'] = 0;
    if( $captcha_config['angle_max'] > 10 ) $captcha_config['angle_max'] = 10;
    if( $captcha_config['angle_max'] < $captcha_config['angle_min'] ) $captcha_config['angle_max'] = $captcha_config['angle_min'];
    if( $captcha_config['min_font_size'] < 10 ) $captcha_config['min_font_size'] = 10;
    if( $captcha_config['max_font_size'] < $captcha_config['min_font_size'] ) $captcha_config['max_font_size'] = $captcha_config['min_font_size'];

    // Generate CAPTCHA code if not set by user
    if( empty($captcha_config['code']) ) {
        $captcha_config['code'] = '';
        $length = mt_rand($captcha_config['min_length'], $captcha_config['max_length']);
        while( strlen($captcha_config['code']) < $length ) {
            $captcha_config['code'] .= substr($captcha_config['characters'], mt_rand() % (strlen($captcha_config['characters'])), 1);
        }
    }

    // Generate HTML for image src
    if ( strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) ) {
        $image_src = substr(__FILE__, strlen( realpath($_SERVER['DOCUMENT_ROOT']) )) . '?_CAPTCHA&amp;t=' . urlencode(microtime());
        $image_src = '/' . ltrim(preg_replace('/\\\\/', '/', $image_src), '/');
    } else {
        $_SERVER['WEB_ROOT'] = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']);
        $image_src = substr(__FILE__, strlen( realpath($_SERVER['WEB_ROOT']) )) . '?_CAPTCHA&amp;t=' . urlencode(microtime());
        $image_src = '/' . ltrim(preg_replace('/\\\\/', '/', $image_src), '/');
    }

    $_SESSION['_CAPTCHA']['config'] = serialize($captcha_config);

    return array(
        'code' => $captcha_config['code'],
        'image_src' => $image_src
    );

}

if( !function_exists('ImageTTFCenter') ) {
	function ImageTTFCenter($image, $text, $font, $size, $angle = 8) 
	{
		$xi = imagesx($image);
		$yi = imagesy($image);
		$box = imagettfbbox($size, $angle, $font, $text);
		$xr = abs(max($box[2], $box[4]));
		$yr = abs(max($box[5], $box[7]));
		$x = intval(($xi - $xr) / 2);
		$y = intval(($yi + $yr) / 2);
		return array($x, $y);	
	}
}

if( !function_exists('hexToRGB') ) {
	function hexToRGB($colour)
	{
			if ( $colour[0] == '#' ) {
					$colour = substr( $colour, 1 );
			}
			if ( strlen( $colour ) == 6 ) {
					list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
			} elseif ( strlen( $colour ) == 3 ) {
					list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
			} else {
					return false;
			}
			$r = hexdec( $r );
			$g = hexdec( $g );
			$b = hexdec( $b );
			return array( 'r' => $r, 'g' => $g, 'b' => $b );
	}	
}	


if( !function_exists('hex2rgb') ) {
    function hex2rgb($hex_str, $return_string = false, $separator = ',') {
        $hex_str = preg_replace("/[^0-9A-Fa-f]/", '', $hex_str); // Gets a proper hex string
        $rgb_array = array();
        if( strlen($hex_str) == 6 ) {
            $color_val = hexdec($hex_str);
            $rgb_array['r'] = 0xFF & ($color_val >> 0x10);
            $rgb_array['g'] = 0xFF & ($color_val >> 0x8);
            $rgb_array['b'] = 0xFF & $color_val;
        } elseif( strlen($hex_str) == 3 ) {
            $rgb_array['r'] = hexdec(str_repeat(substr($hex_str, 0, 1), 2));
            $rgb_array['g'] = hexdec(str_repeat(substr($hex_str, 1, 1), 2));
            $rgb_array['b'] = hexdec(str_repeat(substr($hex_str, 2, 1), 2));
        } else {
            return false;
        }
        return $return_string ? implode($separator, $rgb_array) : $rgb_array;
    }
}

// Draw the image
if( isset($_GET['_CAPTCHA']) ) {

    session_start();

    $captcha_config = unserialize($_SESSION['_CAPTCHA']['config']);
    if( !$captcha_config ) exit();

    unset($_SESSION['_CAPTCHA']);

    // Pick random background, get info, and start captcha
    /*$background = $captcha_config['backgrounds'][mt_rand(0, count($captcha_config['backgrounds']) -1)];
    list($bg_width, $bg_height, $bg_type, $bg_attr) = getimagesize($background);

    $captcha = imagecreatefrompng($background);
	$capcolor = $captcha_config['color'][mt_rand(0, count($captcha_config['color']) -1)];
    $color = hex2rgb($capcolor);
    $color = imagecolorallocate($captcha, $color['r'], $color['g'], $color['b']);*/
    $imgHeight = $captcha_config['img_height'];
    $imgWidth = $captcha_config['img_width'];
    $capcolor = $captcha_config['color'][mt_rand(0, count($captcha_config['color']) -1)];
    $textColor=hex2rgb($capcolor);	
	$fontSize = $imgHeight * 0.75;
	
	$im = imagecreatetruecolor($imgWidth, $imgHeight);	
	$textColor = imagecolorallocate($im, $textColor['r'],$textColor['g'],$textColor['b']);			
	
	$backgroundColor = $captcha_config['backgroundcolor'];
	$backgroundColor = hex2rgb($backgroundColor);
	$backgroundColor = imagecolorallocate($im, $backgroundColor['r'],$backgroundColor['g'],$backgroundColor['b']);
	
	$noiceLines = $captcha_config['noicelines'];
	$noiceColor = $captcha_config['noicecolor'];
	if($noiceLines>0){
		$noiceColor=hex2rgb($noiceColor);	
		$noiceColor = imagecolorallocate($im, $noiceColor['r'],$noiceColor['g'],$noiceColor['b']);
		for( $i=0; $i<$noiceLines; $i++ ) {				
			imageline($im, mt_rand(0,$imgWidth), mt_rand(0,$imgHeight),
			mt_rand(0,$imgWidth), mt_rand(0,$imgHeight), $noiceColor);
		}
	}				
	
	$noiceDots = $captcha_config['noicedots'];			
	if($noiceDots>0){/* generating the dots randomly in background */
		for( $i=0; $i<$noiceDots; $i++ ) {
			imagefilledellipse($im, mt_rand(0,$imgWidth),
			mt_rand(0,$imgHeight), 3, 3, $textColor);
		}
	}		
    

    // Determine text angle
    $angle = mt_rand( $captcha_config['angle_min'], $captcha_config['angle_max'] ) * (mt_rand(0, 1) == 1 ? -1 : 1);

    // Select font randomly
    $font = $captcha_config['fonts'][mt_rand(0, count($captcha_config['fonts']) - 1)];

    // Verify font file exists
    if( !file_exists($font) ) throw new Exception('Font file not found: ' . $font);
	//$noiceColor=hex2rgb('#000');
    //Set the font size.
    //imageline($captcha, mt_rand(0,$bg_width), mt_rand(0,$bg_height),mt_rand(0,$bg_width), mt_rand(0,$bg_height), $noiceColor);
    $font_size = mt_rand($captcha_config['min_font_size'], $captcha_config['max_font_size']);
    
    imagefill($im,0,0,$backgroundColor);
    list($x, $y) = ImageTTFCenter($im, $captcha_config['code'], $font, $font_size);
	//imagettftext($im, $fontSize, 0, $x, $y, $textColor, $font, $text);
    
    //$text_box_size = imagettfbbox($font_size, $angle, $font, $captcha_config['code']);

    // Determine text position
    /*$box_width = abs($text_box_size[6] - $text_box_size[2]);
    $box_height = abs($text_box_size[5] - $text_box_size[1]);
    $text_pos_x_min = 0;
    $text_pos_x_max = ($bg_width) - ($box_width);
    $text_pos_x = mt_rand($text_pos_x_min, $text_pos_x_max);
    $text_pos_y_min = $box_height;
    $text_pos_y_max = ($bg_height) - ($box_height / 2);
    if ($text_pos_y_min > $text_pos_y_max) {
        $temp_text_pos_y = $text_pos_y_min;
        $text_pos_y_min = $text_pos_y_max;
        $text_pos_y_max = $temp_text_pos_y;
    }
    $text_pos_y = mt_rand($text_pos_y_min, $text_pos_y_max);*/

    // Draw shadow
    if( $captcha_config['shadow'] ){
        $shadow_color = hex2rgb($captcha_config['shadow_color']);
        $shadow_color = imagecolorallocate($im, $shadow_color['r'], $shadow_color['g'], $shadow_color['b']);
        imagettftext($im, $font_size, $angle, $x + $captcha_config['shadow_offset_x'], $y + $captcha_config['shadow_offset_y'], $shadow_color, $font, $captcha_config['code']);
    }

    // Draw text
    imagettftext($im, $font_size, $angle, $x, $y, $textColor, $font, $captcha_config['code']);

    // Output image
    header("Content-type: image/png");
    imagepng($im);

}
