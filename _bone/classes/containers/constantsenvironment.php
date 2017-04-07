<?php
namespace _bone\classes\containers;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

class constantsEnvironment
{
	const _DOMAIN    = "_domain";

    const _SUBSYSTEM = "_subsystem";
	const _LANGUAGE  = "_language";
	const _DIRECTORY = "_directory";
	const _ARGUMENTS = "_arguments";

    const _REQUEST   = "_request";
}
?>