<?php
namespace _bone\system\constants;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

class constantsSetup
{
    #kludgy
    const SUBSYSTEM_alpha_link = "http://skladkoles.dev";

	#Subsystems constants
	const SUBSYSTEM_alpha = "skladkoles";
    const SUBSYSTEM_beta  = "dem";

    const DB_PREFIX_alpha = "skladkoles_";
    const DB_PREFIX_beta  = "dem_";

	#System errors reporting mode (dev/rel)
	const ERROR_MODE = "dev";

    #Error message on exception in rel mode
    const SYSTEM_FAILURE = "This is the end";

	#Developer company
	const DEVELOPERS = "CHEERS";
    const DEVELOPERS_LINK = "http://cheersunlimited.com.ua";
}
?>
