<?php 

/* 	File Uploader for Dscourse
 *
 *
 *
*/

 
 
 	/*** Connect to Database ***/
	define('MyConst', TRUE);									// Avoids direct access to config.php
	include "config.php"; 


 // function for other files (coming soon)
 
 $file_type 	= $_POST['type']; 
 $file_category	= $_POST['category'];
 $target 		= 'data/uploads/' . $file_category . 'img';  
 
 if(isset($file_type == 'image')) {						// If the file type is image
	$file_name = $_FILES['file']['name'];
	$tmp_dir = $_FILES['file']['tmp_name'];

	try
	{
		if(!preg_match('/(gif|jpe?g|png)$/i', $file_name) || 				// Check if file has correct extensions
			!preg_match('/^(image)/', $_FILES['file']['type']) ||			// Check if this is image file
			$_FILES['file']['size'] > 300000) 								// Check if size is bigger than 300KB
		{
			throw new Exception("There was an error uploading this file, please try again.");
			exit;
		}
		
		move_uploaded_file($tmp_dir, $target . $file_name);
		echo $target; 
	}
		
	catch(Exception $e)
		{
			echo $e->getMessage();
		}
}
 
 
 
 
 
 
 
 
 
 
 /* End of file upload.php */