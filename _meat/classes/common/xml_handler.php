<?php
namespace _meat\classes\common;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

use coreException;

use _bone\classes\containers\constantsEnvironment
    as Environment;

use _bone\system\DEM_Settings
    as Settings;

use stdClass;

class XML_Handler
{
    public $talk = NULL;

    public function get_xml($xml_file, $language)
    {
        $subsystem = Settings::get_environment_parameters()->{Environment::_SUBSYSTEM};

        $language_file = BASEPATH . "/[{$subsystem}]/xml/{$xml_file}.xml";

        if( !file_exists($language_file) ) {
            throw new coreException("Language file '{$xml_file}' is not available");
        }

        if( ($this->talk = $this->parse_xml_file($language, $language_file)) === FALSE ) {
            throw new coreException("Corrupted language file '{$xml_file}'");
        }

        return $this->talk;
    }

    # ----------------------------------------------------------------------------------------------------
    # |1.0| - XML data extraction and object mapping                                                     |
    # ----------------------------------------------------------------------------------------------------

    #|1.1|
    private function parse_xml_file($language, $language_file)
    {
        $input_object = file_get_contents($language_file);
        $input_object = preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~','$1',$input_object);

        if( ($input_object = simplexml_load_string($input_object, 'SimpleXMLElement', LIBXML_NOCDATA)) === FALSE ) {
            throw new coreException("Unable to create 'SimpleXMLElement'");
        }

        if( count($input_object->children()) === 0 ) {
            throw new coreException("XML file seems empty");
        }

        return $this->recursive_mapping($input_object->children(), $language);
    }

    #|1.2|
    private function recursive_mapping($input_object, $language)
    {
        $talk = new stdClass;

        foreach($input_object as $key => $value)
        {
            if( $value->$language ) {
                $talk->$key = (string)$value->$language;
            } else {
                $talk->$key = $this->recursive_mapping($value, $language);
            }
        }

        return $talk;
    }
}
?>