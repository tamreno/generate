<?php
/**
 * @package tamreno/generate
 * @subpackage File
 * @author: Tam Bieszczad
 * @license 
 */
namespace tamreno\generate;

/**
 * Generate a file.
 * 
 * This class is to generate a file in either .txt or .csv format from data 
 * supplied to it, which can then be output to a browser or saved to a folder.
 */
class File
{
    /** @var array $_header The values to display on the header row, if any. */
    private $_header = null;
    
    /** @var array $_rows The rows of data to be displayed. */
    private $_rows = [];
    
    /** @var array $_ignoreDataColumns The list of columns of data to ignore 
     * when importing data into the File in bulk. */
    private $_ignoreDataColumns = null;
    
    /** @var string $_type This is the file type or extention. */
    private $_type = 'csv';
    
    /** @var string $_delimiter This is the delimiter character to separate 
     * values. */
    private $_delimiter = ',';
    
    /**
     * File constructor
     * 
     * @return $this
     */
    public function __construct(){
        $_headers = func_get_args();
        if(!empty($_headers)){
            call_user_func_array(array($this,"setHeader"), $_headers);
        }
        return $this;
    }
    
    /**
     * Sets the file type to 'txt'
     * 
     * @param string $delimiter Indicates how the fields will be delimited. 
     * Some options are pipe, comma, tab, semi-colon, or whatever you 
     * enter. <br /><br /> Default is semi-colon.
     * @return $this
     */
    public function setTypeTxt($delimiter = ';'){
        $this->_type = 'txt';
        $this->_delimiter = $delimiter;
        return $this;
    }
    
    /**
     * Sets the file type to 'csv'
     * 
     * @return $this
     */
    public function setTypeCsv(){
        $this->_type = 'csv';
        return $this;
    }

    /**
     * Set the header row for the file
     * 
     * @return $this object
     */
    public function setHeader(){
        //Get all cells passed to setHeader
        $_headers = func_get_args();
        $this->_header = $_headers;
        return $this;
    }

    /**
     * Set the field(s) that should be ignored in the $data array (i.e. addDataArray($data)) that will be 
     * processed into the file.
     * 
     * @return $this
     */
    public function ignoreDataColumns(){
        $_ignoreCols = func_get_args();
        foreach($_ignoreCols as $i){
            $this->_ignoreDataColumns[$i] = true;
        }
        return $this;
    }
    
    /**
     * To manually set a row of data in the event the data needs to be manipulated
     * between what is retrieved from the db and what is presented in the file.
     * 
     * @return $this
     */
    public function setRow(){
        $_row = func_get_args();
        $this->_rows[] = $_row;
        return $this;
    }

    /**
     * Process the $data array into the file rows.
     * 
     * @param array $data
     * @return $this
     */
    public function addDataArray($data){
        foreach($data as $row){
            $this->_addDataRow($row);
        }
        return $this;
    }
    
    /**
     * Process the data row and add non filtered data to the file rows.
     * 
     * @param array $row
     */
    private function _addDataRow($row){
        if(!empty($this->_ignoreDataColumns)){
            $this->_filterIgnoredData($row);
        }else{
            $this->_rows[] = $row;
        }
    }
    
    /**
     * Filter row data to exclude ignored columns.
     * 
     * @param array $row
     */
    private function _filterIgnoredData($row){
        $_filteredRow = array();
        foreach($row as $key => $val){
            if(empty($this->_ignoreDataColumns[$key])){
                $_filteredRow[] = $val;
            }
        }
        $this->_rows[] = $_filteredRow;
    }
    
    /**
     * Outputs the file to the browser in either txt or csv format as indicated 
     * by the _type.
     * 
     * @param string $fileName The name you wish to have attributed to the file.
     */
    public function outputToBrowser($fileName = 'file'){
        switch($this->_type){
            case 'txt':
                $this->outputTXT($fileName);
                break;
            case 'csv':
                $this->outputCSV($fileName);
                break;
        }
    }
    
    /**
     * Output the data provided into a CSV file to the browser for viewing or
     * download.
     * 
     * @param type $fileName The name you wish to give to the file.
     */
    private function outputCSV($fileName){        
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=$fileName.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        $file = fopen('php://output', 'w');
        if(!empty($this->_header)){
            fputcsv($file, $this->_header);
        }
        foreach($this->_rows as $row){
            fputcsv($file, $row);              
        }
        fclose($file);
        exit();
    }
    
    /**
     * Output the data provided into a TXT file to the browser for viewing or
     * download.
     * 
     * @param type $fileName
     */
    private function outputTXT($fileName){
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=$fileName.txt");
        header("Pragma: no-cache");
        header("Expires: 0");
        $file = fopen('php://output', 'w');
        fputs($file, $this->generateContent());
        fclose($file);
        exit();
    }
    
    /**
     * Generates the content of the file by combining the header row and the 
     * data supplied to the File object.
     * 
     * @return string $_content The header and data rows of the file.
     */
    private function generateContent(){
        $_content = '';
        if(!empty($this->_header)){
            $_content .= implode($this->_delimiter, $this->_header) . "\r\n";
        }
        
        foreach($this->_rows as $row){
            $_content .= implode($this->_delimiter, $row) . "\r\n";
        }
        return $_content;
    }
    
    /**
     * Save file to a folder location on the server.
     * 
     * @param type $filePath The path, including the file name, where you wish 
     * to save the file.
     * @param type $mode Append ('a') or write ('w').
     */
    public function save($filePath, $mode='w'){
        $fh = fopen($filePath,$mode);
        if(!$fh){
            switch($mode){
                case 'a':
                    die("File Append error: file can't be opened ($filePath)");
                case 'w':
                    die("File Write error: file can't be created ($filePath)");
            }
        }
        fwrite($fh, $this->generateContent());
        fclose($fh);
    }
    
    /**
     * Return the CSV object vars to show on screen. This is for development
     * purposes if you want to see the full set of File object properties.
     * 
     * @return string
     */
    public function showObject(){
        echo '<pre>'.print_r($this,true).'</pre>';
    }
}