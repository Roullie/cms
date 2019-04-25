<?php

	class Images{
		
		// Variables
		private $img_input;
		private $img_output;
		private $img_src;
		private $format;
		private $quality = 80;
		private $x_input;
		private $y_input;
		private $x_output;
		private $y_output;
		private $resize;

		// Set image
		public function set_img($img)
		{

			// Find format
			$ext = strtoupper(pathinfo($img, PATHINFO_EXTENSION));

			// JPEG image
			if(is_file($img) && ($ext == "JPG" OR $ext == "JPEG"))
			{

				$this->format = $ext;
				$this->img_input = ImageCreateFromJPEG($img);
				$this->img_src = $img;
				

			}

			// PNG image
			elseif(is_file($img) && $ext == "PNG")
			{

				$this->format = $ext;
				$this->img_input = ImageCreateFromPNG($img);
				$this->img_src = $img;

			}

			// GIF image
			elseif(is_file($img) && $ext == "GIF")
			{

				$this->format = $ext;
				$this->img_input = ImageCreateFromGIF($img);
				$this->img_src = $img;

			}

			// Get dimensions
			$this->x_input = imagesx($this->img_input);
			$this->y_input = imagesy($this->img_input);

		}

		// Set maximum image size (pixels)
		public function set_size($size = 585)
		{

			// Resize
			if($this->x_input > $size /*&& $this->y_input > $size*/)
			{

				// Wide
				if($this->x_input >= $this->y_input)
				{

					$this->x_output = $size;
					$this->y_output = ($this->x_output / $this->x_input) * $this->y_input;

				}

				// Tall
				//else
				//{

					//$this->y_output = $size;
					//$this->x_output = ($this->y_output / $this->y_input) * $this->x_input;

				//}

				// Ready
				$this->resize = TRUE;

			}

			// Don't resize
			else { $this->resize = FALSE; }

		}

		// Set image quality (JPEG only)
		public function set_quality($quality)
		{

			if(is_int($quality))
			{

				$this->quality = $quality;

			}

		}

		// Save image
		public function save_img($path)
		{

			// Resize
			if($this->resize)
			{

				$this->img_output = ImageCreateTrueColor($this->x_output, $this->y_output);
				ImageCopyResampled($this->img_output, $this->img_input, 0, 0, 0, 0, $this->x_output, $this->y_output, $this->x_input, $this->y_input);

			}

			// Save JPEG
			if($this->format == "JPG" OR $this->format == "JPEG")
			{

				if($this->resize) { imageJPEG($this->img_output, $path, $this->quality); }
				else { copy($this->img_src, $path); }

			}

			// Save PNG
			elseif($this->format == "PNG")
			{

				if($this->resize) { imagePNG($this->img_output, $path); }
				else { copy($this->img_src, $path); }

			}

			// Save GIF
			elseif($this->format == "GIF")
			{

				if($this->resize) { imageGIF($this->img_output, $path); }
				else { copy($this->img_src, $path); }

			}

		}

		// Get width
		public function get_width()
		{

			return $this->x_input;

		}

		// Get height
		public function get_height()
		{

			return $this->y_input;

		}

		// Clear image cache
		public function clear_cache()
		{

			@ImageDestroy($this->img_input);
			@ImageDestroy($this->img_output);

		}
		
		private $acceptedImages = array(
			"image/jpeg",
			"image/jpg",
			"image/png",
			"image/gif",
		);
		
		public function isImage( $type = "" ){
			
			return in_array($type,$this->acceptedImages);
			
		}
		
		public function extensionFromFileType( $type = "" ){
			
			switch($type){
				case 'image/jpeg':
				case 'image/jpg':
					return ".jpg";
				break;
				case 'image/png':
					return ".png";
				break;
				case 'image/gif':
					return ".gif";
				break;
				default:
					return "";
				break;
			}
			
		}
		
		function copyToSmall($image_name,$new_width,$new_height,$uploadDir,$moveToDir){
			$path = $uploadDir . '/' . $image_name;

			$mime = getimagesize($path);

			if($mime['mime']=='image/png') { 
				$src_img = imagecreatefrompng($path);
			}
			if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
				$src_img = imagecreatefromjpeg($path);
			}   

			$old_x          =   imageSX($src_img);
			$old_y          =   imageSY($src_img);

			if($old_x > $old_y) 
			{
				$thumb_w    =   $new_width;
				$thumb_h    =   $old_y*($new_height/$old_x);
			}

			if($old_x < $old_y) 
			{
				$thumb_w    =   $old_x*($new_width/$old_y);
				$thumb_h    =   $new_height;
			}

			if($old_x == $old_y) 
			{
				$thumb_w    =   $new_width;
				$thumb_h    =   $new_height;
			}

			$dst_img        =   ImageCreateTrueColor($thumb_w,$thumb_h);

			imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 


			// New save location
			$new_thumb_loc = $moveToDir . $image_name;

			if($mime['mime']=='image/png') {
				$result = imagepng($dst_img,$new_thumb_loc,8);
			}
			if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
				$result = imagejpeg($dst_img,$new_thumb_loc,80);
			}

			imagedestroy($dst_img); 
			imagedestroy($src_img);

			return $result;
		}
		
		function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 80){
			$imgsize = getimagesize($source_file);
			$width = $imgsize[0];
			$height = $imgsize[1];
			$mime = $imgsize['mime'];
		 
			switch($mime){
				case 'image/gif':
					$image_create = "imagecreatefromgif";
					$image = "imagegif";
					break;
		 
				case 'image/png':
					$image_create = "imagecreatefrompng";
					$image = "imagepng";
					$quality = 7;
					break;
		 
				case 'image/jpeg':
					$image_create = "imagecreatefromjpeg";
					$image = "imagejpeg";
					$quality = 80;
					break;
		 
				default:
					return false;
					break;
			}
			 
			$dst_img = imagecreatetruecolor($max_width, $max_height);
			$src_img = $image_create($source_file);
			 
			$width_new = $height * $max_width / $max_height;
			$height_new = $width * $max_height / $max_width;
			//if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
			if($width_new > $width){
				//cut point by height
				$h_point = (($height - $height_new) / 2);
				//copy image
				imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
			}else{
				//cut point by width
				$w_point = (($width - $width_new) / 2);
				imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
			}
			 
			$image($dst_img, $dst_dir, $quality);
		 
			if($dst_img)imagedestroy($dst_img);
			if($src_img)imagedestroy($src_img);
		}
		
	}