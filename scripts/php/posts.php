<?php

/*
 * Post related database functions
 * 
 */
 
 	define('MyConst', TRUE);	// Avoids direct access to config.php
	include "config.php"; 
	
	
	$action = $_POST['action'];
	
	if($action == 'addPost'){
			$post = $_POST['post'];
			
			$postFromId		= 	$post['postFromId'];
			$postAuthorId	= 	$post['postAuthorId'];
			$postMessage	= 	$post['postMessage'];
			$postType		= 	$post['postType'];
			$postSelection	= 	$post['postSelection'];			
			$postMedia		= 	$post['postMedia'];
			$postMediaType  = 	$post['postMediaType'];
												
			$addPostQuery = mysql_query("INSERT INTO posts (postFromId, postAuthorId, postMessage, postType, postSelection, postMedia, postMediaType) VALUES('".$postFromId."', '".$postAuthorId."', '".$postMessage."','".$postType."','".$postSelection."','".$postMedia."','".$postMediaType."')"); 
			
			$postID = mysql_insert_id();
			
			if($addPostQuery)
			{
				echo $postID; 
				
			} else 
			{
				echo " Post NOT added. "; 
				
			}

	}	
	