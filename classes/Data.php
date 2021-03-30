<?php
/**
 * Management of data (CRUD)
 */
class Data
{
    
    /**
     * Creating new data in db
     *
     * @param string $table - Name of db table
     * @param array $data - Data to be saved
     * @return boolean - Status message
     */
    public static function create($table, $data)
    {
        global $wpdb;
        $db_data = [];

        foreach ($data as $column => $value) {
            if ($column !== 'action' && $column !== 'nonce' && $column !== 'id') {
                $db_data[$column] = esc_sql($value);
            }
        }
            
        if (count($data) > 0) {
            $save_data = $wpdb->insert($wpdb->prefix . $table, $db_data);

            if ($save_data != false) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Reading data from database
     *
     * @param string $query - MySQL query to run on database
     * @param string $param - Parameter of prepared statement
     * @return array - Result of query
     */
    public static function read($query, $param = false)
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare($query, $param), ARRAY_A);
    }

    /**
     * Updating data
     *
     * @param string $table - Name of the db table
     * @param array $data - Updated data
     * @param array $id - The value of identification of db record
     * @return boolean - Status messages
     */
    public static function update($table, $data, $id)
    {
        global $wpdb;
        $db_data = [];

        foreach ($data as $column => $value) {
            if ($column !== 'action' && $column !== 'nonce' && $column !== 'id') {
                $db_data[$column] = esc_sql($value);
            }
        }        
            
        if (count($data) > 0) {
            $update_data = $wpdb->update($wpdb->prefix . $table, $db_data, $id);

            if ($update_data != false) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Deleting data from db
     *
     * @param string $table - Name of the db table
     * @param array $data - IDs of items to be deleted
     * @param string $param - Name of identifying parameter
     * @return boolean - status message
     */
    public static function delete($table, $data, $param)
    {
        global $wpdb;
        $data_string = '';

        foreach ($data as $d) {
            $data_string .= "$d, ";
        }
        $ready_data_string = rtrim($data_string, ', ');
        $data_table = $wpdb->prefix . $table;

        if (!empty($ready_data_string)) {
            $del = $wpdb->query("DELETE FROM $data_table WHERE $param IN ($ready_data_string)");

            if ($del != false) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Custom query
     *
     * @param string $sql - The custom MySQL query.
     * @param string|array|int|float $param - Parameters of the query if there is any.
     * @return array - Result of the query.
     */
    
    public static function custom($sql, $param = null)
    {
        global $wpdb;

        if ($param != null) {
            return $wpdb->get_results($wpdb->prepare($sql, $param), ARRAY_A);
        } else {
            return $wpdb->get_results($sql, ARRAY_A);
        }
    }    
}
