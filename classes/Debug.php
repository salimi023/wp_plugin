<?php
/**
 * Debugger Class
 */
class Debug
{
    private static $log_file = null;
    private static $error_file = null;

    public function __construct() {
        self::$log_file = dirname(__FILE__, 2) . '/logs/debug.log';
        self::$error_file = dirname(__FILE__, 2) . '/logs/error.log';
        self::error(); 
    }
    
    /** Loggings debug reports */
    public static function log($new_data)
    {        
        $source_page = $_SERVER['REQUEST_URI'];
        $date = date('Y-m-d H:i:s');
        
        $add_data = [
            'date' => $date,
            'page' => $source_page,
            'data' => $new_data
        ];
        
        $logged_data = json_decode(file_get_contents(self::$log_file), true);

        if (is_array($logged_data)) {
            array_push($logged_data, $add_data);
        } else {
            $logged_data[] = [
                'date' => $date,
                'page' => $source_page,
                'data' => $new_data
            ];
        }
        
        file_put_contents(self::$log_file, json_encode($logged_data, JSON_PRETTY_PRINT));        
    }

    /** Displaying logs */
    public static function report($type)
    {        
        if($type === 'debug') {
            $res = json_decode(file_get_contents(self::$log_file), true);
        }
        else {
            $res = file_get_contents(self::$error_file);
        }                       
        return $res;
    }

    /** Deleting logs */
    public static function delete($type) {
        $file = $type === 'debug' ? self::$log_file : self::$error_file;        
        file_put_contents($file, '');       
        $logs = file_get_contents($file);               
        $resp = empty($logs) ? 200 : 500;
        return $resp;
    }

    /** Logging Errors */
    private static function error() {
        error_reporting(E_ALL);
        ini_set("error_log", self::$error_file);
    }
}
