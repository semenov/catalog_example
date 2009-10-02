<?php

/**
 * Item model.
 */

class Item
{
    public $id = 0;
    public $name = '';
    public $params = array();
    
    public static function find($id)
    {
        $item = Database::find(
            'SELECT id, name, category_id FROM items WHERE id = ?', 
            $id, 'Item'
        );

        $params = Database::find_all(
            'SELECT id, name FROM params WHERE category_id = ?', 
            $item->category_id
        );
        
        $values = Database::find_all(
            'SELECT id, value, param_id FROM `values` WHERE item_id = ?', 
            $id
        );
        
        foreach ($values as $value)
        {
            $indexed_values[$value->param_id] = $value->value;
        }
        
        foreach ($params as &$param)
        {
            if (isset($indexed_values[$param->id]))
            {
                $param->value = $indexed_values[$param->id];
            }
            else
            {
                $param->value = '';
            }
            
        }
        
        $item->params = $params;
        
        return $item;       
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
                'UPDATE items SET name = ? WHERE id = ?',
                array($this->name, $this->id)
            );
        }
        else
        {
            Database::execute(
                'INSERT INTO items SET name = ?, category_id = ?',
                array($this->name, $this->category_id)
            );
            $this->id = Database::get_insert_id();
        }
        
        //'SELECT * FROM `values` WHERE item_id = ? AND param_id = ?',
        
        $values = Database::find_all(
            'SELECT id, param_id FROM `values` WHERE item_id = ?',
            array($this->id)
        );
        
        $indexed_values = array();
        
        foreach ($values as $v)
        {
            $indexed_values[$v->param_id] = $v->id;
        }
                    
        foreach ($this->params as $param)
        {           
            if (isset($indexed_values[$param->id]))
            {           
                Database::execute(
                    'UPDATE `values` SET value = ? WHERE id = ?',
                    array($param->value, $indexed_values[$param->id])
                );
            }
            else
            {
                Database::execute(
                    'INSERT INTO `values` SET value = ?, item_id = ?, param_id = ?',
                    array($param->value, $this->id, $param->id)
                );
            }
        }
    }

    public function delete()
    {
        Database::execute(
            'DELETE FROM items WHERE id = ?', 
            $this->id
        );
    }
}
?>
