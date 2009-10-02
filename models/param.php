<?php

/**
 * Parameter model.
 */
 
class Param
{
    public $id;
    public $name = '';
    public $category_id = 0;
    
    function __construct($id = NULL)
    {
        $this->id = $id;
    }
    
    public static function find($id)
    {       
        $statement = Database::execute(
            'SELECT id, name, category_id FROM params WHERE id = ?',  
            $id
        );
        
        $statement->setFetchMode(PDO::FETCH_CLASS, 'Param', array($id));
        return $statement->fetch(PDO::FETCH_CLASS); 
      
    }
    
    public function save()
    {
        if ($this->name == '')
        {
            throw new Exception("Name can't be empty");
        }    
        
        if ($this->id)
        {           
            Database::execute(
                'UPDATE params SET name = ? WHERE id = ?',
                array($this->name, $this->id)
            );
        }
        else
        {
            Database::execute(
                'INSERT INTO params SET name = ?, category_id = ?',
                array($this->name, $this->category_id)
            );
            $this->id = Database::get_insert_id();
        }
    }

    public function delete()
    {
        Database::execute(
            'DELETE FROM params WHERE id = ?', 
            $this->id
        );
    }
}
?>
