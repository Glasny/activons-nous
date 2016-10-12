<?php

class Invitation {
	protected static $table = 'invitation';
	protected static $atrList = array('id'=>'int', 'date'=>'string', 'message'=>'string', 'user'=>'string', 'activite'=>'int');
	
	protected $id;
	protected $date;
	protected $message;
	protected $user;
	protected $activite;
	
}
