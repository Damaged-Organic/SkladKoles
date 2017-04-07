<?php
namespace _meat\classes\common;

if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

use coreException;

class Pagination
{
    private $parameters = array(
        'table'            => NULL,
        'records_per_page' => NULL,
        'pages_step'       => NULL,
        'current_page'     => 1
    );
    private $records_number = NULL;
    private $pages_number   = NULL;

    private function get_pages_number($records_per_page, $records_number)
    {
        return ( ($records_per_page !== 0) && ($records_number !== 0) ) ? (int)ceil($records_number / $records_per_page) : FALSE;
    }

    private function is_page_exists($current_page, $pages_number)
    {
        return ( in_array($current_page, range(1, $pages_number)) ) ? TRUE : FALSE;
    }

    private function get_side_buttons($current_page, $pages_number)
    {
        $side_buttons['page_prev'] = ( ($current_page - 1) > 0 ) ? $current_page - 1 : NULL;
        $side_buttons['page_next'] = ( ($current_page + 1) <= $pages_number ) ? $current_page + 1 : NULL;

        return $side_buttons;
    }

    private function build_pagination_menu($pages_step, $current_page, $pages_number)
    {
        if( !$pages_number ) {
            return FALSE;
        }

        $pagination = array();

        if( in_array($current_page, range(1, $pages_step)) )
        {
            if( $pages_number <= $pages_step ) {
                $pagination = range(1, $pages_number);
            } else {
                $pagination = array_merge(range(1, $pages_step+1), array('separator',$pages_number));
            }
        } elseif( in_array($current_page, range($pages_number-$pages_step+1, $pages_number)) ) {
            $pagination = array_merge(
                array(1,'separator'),
                range($pages_number-$pages_step, $pages_number)
            );
        } elseif( in_array($current_page, range($pages_step+1, $pages_number-$pages_step)) ) {
            $side_step = ceil($pages_step / 2) - 1;

            $pagination = array_merge(
                array(1,'separator'),
                range($current_page - $side_step, $current_page + $side_step),
                array('separator',$pages_number)
            );
        }

        return ( !empty($pagination) ) ? $pagination : FALSE;
    }

    public function set_parameters($parameters)
    {
        $parameters = array_filter($parameters);
        if( !empty($parameters) ) $this->parameters = array_merge($this->parameters, $parameters);

        if( ($this->records_number = $parameters['records_number']) === FALSE ) {
            throw new coreException("Invalid records number");
        }

        if( ($this->pages_number = $this->get_pages_number($this->parameters['records_per_page'], $this->records_number)) === FALSE ) {
            throw new coreException("Invalid pages number");
        }

        //:TODO: fallbacks to default if user parameters are toooo big!

        return $this;
    }

    public function set_current_page($current_page)
    {
        if( $current_page ) {
            if( $this->is_page_exists($current_page, $this->pages_number) ) {
                $this->parameters['current_page'] = $current_page;
            } else {
                return FALSE;
            }
        }

        return $this;
    }

    public function handle_pagination()
    {
        $pagination['current_page']     = $this->parameters['current_page'];
        $pagination['first_record']     = ($this->parameters['records_per_page'] * $this->parameters['current_page']) - $this->parameters['records_per_page'];
        $pagination['records_per_page'] = $this->parameters['records_per_page'];
        $pagination['side_buttons']     = $this->get_side_buttons($this->parameters['current_page'], $this->pages_number);

        $pagination['navigation_items']    = $this->build_pagination_menu($this->parameters['pages_step'], $this->parameters['current_page'], $this->pages_number);
        $pagination['navigation_required'] = ( count($pagination['navigation_items']) > 1 ) ? TRUE : FALSE;

        return array_filter($pagination) ? $pagination : FALSE;
    }
}
?>