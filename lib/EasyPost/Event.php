<?php

namespace EasyPost;

class Event 
{
	public static function retrive(){
		$inputJSON = file_get_contents('php://input');
		return json_decode( $inputJSON, TRUE );
	}
}
?>