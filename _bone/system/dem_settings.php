<?php
namespace _bone\system;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

use bootException, coreException;

use _bone\system\constants\constantsSetup
    as Setup;

use _bone\classes\containers\constantsEnvironment
    as Environment,
	_bone\classes\containers\constantsAjaxRequest
	as AjaxRequest,
    _bone\classes\containers\constantsNamespaces
    as Namespaces,
    _bone\classes\containers\constantsMoods
    as Moods;

use _bone\system\DEM_BootLoader
    as BootLoader;

use _bone\classes\containers\genericContainerConstants
    as ContainerConstants;

class DEM_Settings
{
    private static $intrusion_countermeasures = NULL;

	private static $environment_parameters = NULL;
    private static $environment_container  = [];

    private static $environment_instance = NULL;
    private static $ajaxRequest_instance = NULL;

    private static $constants =  [
        'setup'       => NULL,
        'environment' => NULL,
        'ajaxRequest' => NULL,
        'namespaces'  => NULL,
        'moods'       => NULL
    ];

    private static $bootLoader = NULL;

    private static $essential_environment_data =
	[
        Environment::_DOMAIN    => [
            Setup::SUBSYSTEM_alpha
        ],
        Environment::_SUBSYSTEM => [
            Setup::SUBSYSTEM_alpha, Setup::SUBSYSTEM_beta
        ]
    ];

    private static $available_environment_data =
	[
        Environment::_LANGUAGE   => [
            Setup::SUBSYSTEM_alpha => ['ru'],
            Setup::SUBSYSTEM_beta  => ['ru']
        ],
        Environment::_DIRECTORY  => [
            Setup::SUBSYSTEM_alpha => ['main', 'catalog', 'subcatalog', 'spares', 'item_details', 'promotions', 'promotion_details', 'tyre_fitting', 'how_to_buy', 'delivery_and_payment', 'news', 'news_detailed', 'contacts', 'search', 'cart'],
            Setup::SUBSYSTEM_beta  => ['authorization', 'item', 'upload_excel', 'add', 'news', 'spares', 'promotions']
        ],
        AjaxRequest::AR_LOCATION => [
            Setup::SUBSYSTEM_alpha => ['news', 'feedback', 'items_top', 'items_filter', 'items_filter_cars', 'items_filter_cars_main', 'navigation', 'cart', /*'order',*/ 'items_search', 'fast_search', 'rating'],
            Setup::SUBSYSTEM_beta  => ['process_item', 'process_excel', 'process_news', 'process_spares', 'promotions', 'set_currency_rate']
        ],
        AjaxRequest::AR_METHOD   => [
            Setup::SUBSYSTEM_alpha => ['get_news', 'send_feedback', 'get_items_top', 'filter_data', 'filter_cars', 'filter_data_cars', 'filter_cars_main', 'set_page', 'set_items_number', 'add_item', 'act', 'delivery_type', /*'make_order',*/ 'get_items_search', 'search', 'rate'],
            Setup::SUBSYSTEM_beta  => ['item_update', 'modification_delete', 'modification_add', 'delete_image', 'item_delete', 'excel_upload', 'excel_delete', 'news_update', 'spares_update', 'spares_add', 'spares_delete', 'add_promotions', 'delete_promotions', 'save_promotions', 'set_rate']
        ]
    ];

    private static $request_patterns =
    [
        Setup::SUBSYSTEM_alpha => [
            '#^main$#',
            '#^catalog$#',
            '#^subcatalog/rims$#', '#^subcatalog/tyres$#', '#^subcatalog/exclusive_rims$#', '#^subcatalog/exclusive_tyres$#',

            //'#^spares/rings$#', '#^spares/bolts$#', '#^spares/nuts$#', '#^spares/locks$#', '#^spares/logos$#',

            // BRANDS LANDING
            // '/^subcatalog\/rims\/brand\/[0-9_\-\p{L}]+$/iu',
            // '/^subcatalog\/tyres\/brand\/[0-9_\-\p{L}]+$/iu',
            // '/^subcatalog\/exclusive_rims\/brand\/[0-9_\-\p{L}]+$/iu',
            // '/^subcatalog\/exclusive_tyres\/brand\/[0-9_\-\p{L}]+$/iu',
            // END:BRANDS LANDING

            '/^subcatalog\/rims\/[0-9_\-\.\:\;\(\)\p{L}]+$/iu',
            '/^subcatalog\/tyres\/[0-9_\-\.\:\;\(\)\p{L}]+$/iu',
            '/^subcatalog\/exclusive_rims\/[0-9_\-\.\:\;\(\)\p{L}]+$/iu',
            '/^subcatalog\/exclusive_tyres\/[0-9_\-\.\:\;\(\)\p{L}]+$/iu',

            '#^spares$#',
            '#^item_details/rims/[0-9]+$#', '#^item_details/tyres/[0-9]+$#', '#^item_details/exclusive_rims/[0-9]+$#', '#^item_details/exclusive_tyres/[0-9]+$#',
            '#^promotions$#',
            '#^promotion_details/[0-9]+$#',
            '#^tyre_fitting$#',
            '#^how_to_buy$#',
            '#^delivery_and_payment$#',
            '#^news$#',
            '#^news_detailed/[0-9]+$#',
            '#^contacts$#',
            '#^search$#',
            '#^cart$#', '#^cart/order$#', '#^cart/result$#'
        ],
        Setup::SUBSYSTEM_beta => [
            '#^authorization#',
            '#^item/rims/[0-9]+#', '#^item/exclusive_rims/[0-9]+#', '#^item/tyres/[0-9]+#', '#^item/exclusive_tyres/[0-9]+#',
            '#^upload_excel#',
            '#^add#', '#^add/items/rims#', '#^add/items/exclusive_rims#', '#^add/items/tyres#', '#^add/items/exclusive_tyres#', '#^add/news#',
            '#^news/[0-9]+#',
            '#^spares/rings$#', '#^spares/bolts$#', '#^spares/nuts$#', '#^spares/locks$#', '#^spares/logos$#', '#^spares/pins$#',
            '#^promotions#'
        ]
    ];

	public function __construct(containerConstants $environment_instance, containerConstants $ajaxRequest_instance)
	{
        self::$environment_instance = $environment_instance;
        self::$ajaxRequest_instance = $ajaxRequest_instance;

        self::$environment_parameters = new \stdClass;
        self::$environment_container  = self::$ajaxRequest_instance->obtain_constants_set() + self::$environment_instance->obtain_constants_set();

        self::$constants =  [
            'setup'       => new Setup,
            'environment' => new Environment,
            'ajaxRequest' => new AjaxRequest,
            'namespaces'  => new Namespaces,
            'moods'       => new Moods
        ];
    }

    public static function is_ajax_request()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    public static function assign_self()
    {
        return __CLASS__;
    }

    public static function register_intrusionCountermeasures(IntrusionCountermeasures $intrusion_countermeasures)
    {
        self::$intrusion_countermeasures = $intrusion_countermeasures;
    }

    public static function assign_intrusionCountermeasures()
    {
        if( empty(self::$intrusion_countermeasures) ) {
            throw new coreException("IntrusionCountermeasures are not yet registered");
        } else {
            return self::$intrusion_countermeasures;
        }
    }

    public static function register_bootLoader(BootLoader $bootLoader)
    {
        self::$bootLoader = $bootLoader;
    }

    public static function assign_bootLoader()
    {
        if( empty(self::$bootLoader) ) {
            throw new coreException("BootLoader is not yet registered");
        } else {
            return self::$bootLoader;
        }
    }

    public static function get_request_patterns()
    {
        return self::$request_patterns[self::$environment_parameters->{Environment::_SUBSYSTEM}];
    }

	public static function set_environment_parameters(array $parameters)
	{
        foreach($parameters as $parameter => $value)
		{
			if( in_array($parameter, self::$environment_container, TRUE) ) {
                self::$environment_parameters->{$parameter} = $value;
            } else {
                throw new bootException('Attempt to set non-existing environment property');
            }
		}
	}

    public static function get_environment_parameters()
    {
        return self::$environment_parameters;
    }

    public static function essential($type, $item_index = NULL)
    {
        $essential = NULL;

		if( !isset(self::$essential_environment_data[$type]) ) {
			throw new bootException('Such "essential_" property is not available');
		} else {
			$essential = self::$essential_environment_data[$type];
		}

        if( !is_null($item_index) )
        {
            if( !isset(self::$essential_environment_data[$type][$item_index]) ) {
                throw new bootException('Such "available_" property index is not actually available');
            } else {
                $essential = self::$essential_environment_data[$type][$item_index];
            }
        }

        return $essential;
    }

    public static function available($type, $item_index = NULL)
    {
        $available = NULL;

		if( !isset(self::$available_environment_data[$type][self::$environment_parameters->{Environment::_SUBSYSTEM}]) ) {
			throw new bootException('Such "available_" property is not actually available');
		} else {
			$available = self::$available_environment_data[$type][self::$environment_parameters->{Environment::_SUBSYSTEM}];
		}

        if( !is_null($item_index) )
        {
            if( !isset(self::$available_environment_data[$type][self::$environment_parameters->{Environment::_SUBSYSTEM}][$item_index]) ) {
                throw new bootException('Such "available_" property index is not actually available');
            } else {
                $available = self::$available_environment_data[$type][self::$environment_parameters->{Environment::_SUBSYSTEM}][$item_index];
            }
        }

        return $available;
    }

    public static function get_constants()
    {
        $filtered_constants = array_filter(self::$constants);

        if( empty($filtered_constants) ) {
            throw new coreException("Constants array is not yet defined");
        } else {
            return self::$constants;
        }
    }
}
?>
