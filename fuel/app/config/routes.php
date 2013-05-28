<?php
return array(
	'_root_'  => 'page/page/index',  // The default route
	'_404_'   => 'welcome/404',    // The main 404 route
	
	// Module specific
	'pages/(:any)' => 'page/page/index/$1',
	'blogs' => 'blog/blog/',
	'blogs/post/(:any)' => 'blog/blog/post/$1',
);