<?php 

/*
|-------------------------------------------------------------------------
| Configuration
|-------------------------------------------------------------------------
*/  
 
$config['layout'] = array('default' => 'Default');

if (defined('ENVIRONMENT'))
{
    if(ENVIRONMENT == 'development') {
    
		$config['domain'] = '';
		$config['databases']['default']['host'] = '';
		$config['databases']['default']['user'] = '';
		$config['databases']['default']['pass'] = '';
		$config['databases']['default']['name'] = '';
		$config['databases']['default']['port'] = '';
		$config['databases']['default']['driver'] = 'pdo_mysql';

    
    } else if(ENVIRONMENT == 'production') { 
	
    	$config['domain'] = '';
		$config['databases']['default']['host'] = '';
		$config['databases']['default']['user'] = '';
		$config['databases']['default']['pass'] = '';
		$config['databases']['default']['name'] = '';
		$config['databases']['default']['port'] = '';
		$config['databases']['default']['driver'] = 'pdo_mysql';

    }
 }