<?php

/**
 * Contains base methods for working with the DB.
 */

class Database
{
    private static $db;
    
    /**
     * Returns database connection handler.
     */
     
    public static function get_connection()
    {
        if (!isset(self::$db)) 
        {
            global $config;
            self::$db = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['database']}", 
                $config['db']['username'], $config['db']['password']);
        }

        return self::$db;        
    }
    
    /**
     * Executes the query and returns PDOStatement.
     */
         
    public static function execute($sql, $params = array())
    {
        if (!is_array($params))
        {
            $params = array($params);
        }
        
        $db = self::get_connection();
        $statement = $db->prepare($sql);
        $statement->execute($params);
        return $statement;
    }
    
    /**
     * Returns last insert id.
     */
         
    public static function get_insert_id()
    {
        return self::get_connection()->lastInsertId();
    }    
    
    /**
     * Returns a single record mathcing the query.
     */    
    
    public static function find($sql, $params = array(), $class = NULL)
    {
        $statement = self::execute($sql, $params);
        return $statement->fetchObject($class);
    }
    
    /**
     * Returns all records mathcing the query.
     */        
    
    public static function find_all($sql, $params = array(), $class = NULL)
    {
        $statement = self::execute($sql, $params);
        if ($class)
        {
            return $statement->fetchAll(PDO::FETCH_CLASS, $class);
        }
        else
        {
            return $statement->fetchAll(PDO::FETCH_OBJ);
        }        
    }    

}

?>
