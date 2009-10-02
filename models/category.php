<?php

/**
 * Category model.
 */
 
class Category
{
    public $id;
    public $name = '';
    public $parent_id = 0;
    
    function __construct($id = NULL)
    {
        $this->id = $id;
    }
    
    public function get_child_categories()
    {
        return Database::find_all(
            'SELECT id, name FROM categories WHERE parent_id = ? ORDER BY name', 
            $this->id
        );
    }
    
    public static function find($id)
    {
        if ($id == 0)
            return new Category(0);
        
        $statement = Database::execute(
            'SELECT id, name, parent_id FROM categories WHERE id = ?', 
            $id
        );
        
        $statement->setFetchMode(PDO::FETCH_CLASS, 'Category', array($id));
        return $statement->fetch(PDO::FETCH_CLASS);        
    }    
    
    public function get_ancestors()
    {
        $db = Database::get_connection();
        $statement = $db->prepare(
            'SELECT id, name, parent_id FROM categories WHERE id = ?'
        );
        
        $category_id = $this->id;
        
        $ancestors = array();
        
        while ($category_id != 0)
        {
            $statement->execute(array($category_id));
            $category = $statement->fetch(PDO::FETCH_OBJ);
            array_unshift($ancestors, $category);
            $category_id = $category->parent_id;
        }
        
        $root = new stdClass();
        $root->id = 0;
        $root->name = 'Catalog';
        array_unshift($ancestors, $root);    

        return $ancestors;
    }
    
    public function get_items()
    {       
        return Database::find_all(
            'SELECT id, name FROM items WHERE category_id = ? ORDER BY name', 
            $this->id
        );        
    }   
    
    public function get_params()
    {
        return Database::find_all(
            "SELECT id, name, '' AS value FROM params WHERE category_id = ?", 
            $this->id
        );           
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
                'UPDATE categories SET name = ? WHERE id = ?',
                array($this->name, $this->id)
            );
        }
        else
        {
            Database::execute(
                'INSERT INTO categories SET name = ?, parent_id = ?',
                array($this->name, $this->parent_id)
            );
            $this->id = Database::get_insert_id();
        }
    }  
    
    public function delete()
    {
        if ($this->id != 0)
        {
            Database::execute(
                'DELETE FROM categories WHERE id = ?', 
                $this->id
            );
        }
    }      
}

?>
