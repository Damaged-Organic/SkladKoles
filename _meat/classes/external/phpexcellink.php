<?php
namespace _meat\classes\external;

use coreException;

class PHPExcelLink
{
    function __construct()
    {
        require_once dirname(__FILE__) . '/PHPExcel/PHPExcel/IOFactory.php';
    }

    public function read_file($filename, $exact_file)
    {
        if( ($reader_format = $this->get_reader_format($filename)) === NULL )
            throw new coreException("Not an Excel file type");

        $reader = \PHPExcel_IOFactory::createReader($reader_format);
        $reader->setReadDataOnly(TRUE);

        $excel = $reader->load($exact_file);

        foreach($excel->getWorksheetIterator() as $worksheet) {
            $file_data[$worksheet->getTitle()] = $worksheet->toArray();
        }

        return $file_data;
    }

    private function get_reader_format($filename)
    {
        $reader_format = NULL;

        switch( pathinfo($filename, PATHINFO_EXTENSION) )
        {
            case 'xls':
                $reader_format = "Excel5";
            break;

            case 'xlsx':
                $reader_format = "Excel2007";
            break;
        }

        return $reader_format;
    }
}
?>
