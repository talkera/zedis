<?php

// This is the database connection configuration.
return array(
	//'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',

	'connectionString' => 'mysql:host=127.0.0.1;dbname=zedis',
	'emulatePrepare' => true,
	'username' => 'zedis',
	'password' => 'zedis',
	'charset' => 'utf8',
);