<?php

// ...
// ...
// ...
// ...
// ...
// ...
// ...
// ...
// ...
// ...
// ...
// ...
// ...

// Add at the end
// vllt funktionierts ja auch nur für localhost nicht???!

/** Enable my local themes and plugins repository FFS!!! */
add_filter( 'http_request_host_is_external', 'allow_my_custom_host', 10, 3 );
function allow_my_custom_host( $allow, $host, $url ) {
    if ( in_array($host, array('localhost', 'my.api.url') ) )
    $allow = true;
    return $allow;
}