<?php

defined('C5_EXECUTE') or die("Access Denied.");


/*
	you can override system layouts here  - but we're not going to by default

	For example: if you would like to theme your login page with the Green Salad theme,
	you would uncomment the lines below and change the second argument of setThemeByPath
	to be the handle of the the Green Salad theme "greensalad"

*/

$v = View::getInstance();

Loader::library('3rdparty/mobile_detect');
$md = new Mobile_Detect();
if ($md->isMobile()) {
	$v->setThemeByPath('/page_not_found', 'full_stack_style_mobile');
}
else {
	$v->setThemeByPath('/page_not_found', 'full_stack_style');
}

/*
$v->setThemeByPath('/register', "yourtheme");
$v->setThemeByPath('/login', "yourtheme");
$v->setThemeByPath('/page_fobidden', "yourtheme");
*/
