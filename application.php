<?php

require_once 'models/param.php';
require_once 'models/category.php';
require_once 'models/item.php';

/**
 * This class contains all actions of the application.
 * An action is called when an appropriate url is requested,
 * e.g. Application::show_category() is called
 * when /show_category is requsted.
 */

class Application 
{
    function __construct()
    {
        session_start();
    }
    
    /** 
     * Default action.
     */
     
    public function index()
    {
        return $this->show_category();
    }

    /** 
     * Lists subcategories and items contained in the category.
     */

    public function show_category()
    {       
        $category_id = $this->get_arg('id', 0);
        $category = new Category($category_id);
        
        $data['categories'] = $category->get_child_categories();      
        $data['items'] = $category->get_items();
        $data['breadcrumbs'] = $this->generate_breadcrumbs($category_id);
        $data['category_id'] = $category_id;
             
        return new View('category', $data);
    }    
    
    /** 
     * Displays information about the item.
     */    
    
    public function show_item()
    {
        $id = $this->get_arg('id', 0);
        $item = Item::find($id); 
        $data['item'] = $item;

        $category = new Category($item->category_id);
        $data['breadcrumbs'] = $this->generate_breadcrumbs(
            $item->category_id,
            array('/show_item?id='.$item->id => $item->name)
        );
                
        return new View('item', $data);     
    }    
    
    /** 
     * Displays a form to create a new item.
     */ 
     
    public function new_item()
    {
        $this->check_authorization();
        
        $category_id = $this->get_arg('category_id', 0);
        $category = new Category($category_id);
        
        $item = new Item();
        $item->params = $category->get_params();
        $item->category_id = $category_id; 
        $data['item'] = $item;
        $data['breadcrumbs'] = $this->generate_breadcrumbs($category_id);

        return new View('item_form', $data);     
    } 

    /** 
     * Displays a form to edit the item.
     */ 

    public function edit_item()
    {
        $this->check_authorization();
        
        $id = $this->get_arg('id', 0);
        $item = Item::find($id); 
        $data['item'] = $item;
        $data['breadcrumbs'] = $this->generate_breadcrumbs(
            $item->category_id,
            array('/show_item?id='.$item->id => $item->name)
        );

        return new View('item_form', $data);     
    } 

    /** 
     * Creates a new item or saves the modified one.
     */ 
     
    public function save_item()
    {
        $this->check_authorization();
        
        $item = new Item();
        $item->id = $this->get_arg('id', 0);
        $item->name = $this->get_arg('name', '');
        $item->category_id = $this->get_arg('category_id', 0);
        $params = $this->get_arg('params', array());

        $category = new Category($item->category_id);    
        $item->params = $category->get_params(); 
        
        $data['breadcrumbs'] = $this->generate_breadcrumbs(
            $item->category_id,
            array('/show_item?id='.$item->id => $item->name)
        );        

        foreach ($item->params as &$param)
        {
            if (isset($params[$param->id]))
            {
                $param->value = $params[$param->id];
            }
        } 

        try 
        {
            $item->save();
            return new Redirect('/show_item?id='.$item->id);  
        }
        catch (Exception $e)
        {
            $data['error'] = $e->getMessage();
            $data['item'] = $item;   
            
            return new View('item_form', $data);                       
        }
    }
    
    /** 
     * Removes an item.
     */     
    
    public function delete_item()
    {
        $this->check_authorization();
        
        $id = $this->get_arg('id', 0);
        
        
        $item = Item::find($id); 
        $item->delete();

        return new Redirect('/show_category?id='.$item->category_id);     
    }        
        
    /** 
     * Displays a form to create a new category.
     */ 
         
    public function new_category()
    {
        $this->check_authorization();
        
        $category = new Category();
        $category->parent_id = $this->get_arg('parent_id', 0);
        $data['category'] = $category;

        return new View('category_form', $data);           
    }
    
    /** 
     * Displays a form to edit the category.
     */     
    
    public function edit_category()
    {
        $this->check_authorization();
        
        $id = $this->get_arg('id', 0);
        $category = Category::find($id); 
        $data['category'] = $category;
        $data['breadcrumbs'] = $this->generate_breadcrumbs($id);        

        return new View('category_form', $data);           
    }     
    
    /** 
     * Creates a new category or saves the modified one.
     */
         
    public function save_category()
    {
        $this->check_authorization();
        
        $category = new Category();
        $category->id = $this->get_arg('id', 0);
        $category->name = $this->get_arg('name', '');
        $category->parent_id = $this->get_arg('parent_id', 0);

        try 
        {
            $category->save();
            return new Redirect('/edit_category?id='.$category->id);  
        }
        catch (Exception $e)
        {
            $data['error'] = $e->getMessage();
            $data['category'] = $category;   
            
            return new View('category_form', $data);                       
        }
    }    
    
    /** 
     * Removes the category.
     */  
         
    public function delete_category()
    {
        $this->check_authorization();
        
        $id = $this->get_arg('id', 0);
        $category = Category::find($id); 
        $category->delete();

        return new Redirect('/show_category?id='.$category->parent_id);     
    }     
    
    
    /** 
     * Displays a form to create a new param.
     */ 
         
    public function new_param()
    {
        $this->check_authorization();
        
        $param = new Param();
        $param->category_id = $this->get_arg('category_id', 0);
        $data['param'] = $param;

        return new View('param_form', $data);           
    }
    
    /** 
     * Displays a form to edit the param.
     */ 
         
    public function edit_param()
    {
        $this->check_authorization();
        
        $id = $this->get_arg('id', 0);
        $data['param'] = Param::find($id); 

        return new View('param_form', $data);           
    }     
    
    /** 
     * Creates a new param or saves the modified one.
     */
         
    public function save_param()
    {
        $this->check_authorization();
        
        $param = new Param();
        $param->id = $this->get_arg('id', 0);
        $param->name = $this->get_arg('name', '');
        $param->category_id = $this->get_arg('category_id', 0);

        try 
        {
            $param->save();
            return new Redirect('/edit_category?id='.$param->category_id);  
        }
        catch (Exception $e)
        {
            $data['error'] = $e->getMessage();
            $data['param'] = $param;   
            
            return new View('param_form', $data);                       
        }
    }  
    
    /** 
     * Removes the param.
     */    
    
    public function delete_param()
    {
        $this->check_authorization();
        
        $id = $this->get_arg('id', 0);
        $param = Param::find($id); 
        $param->delete();

        return new Redirect('/edit_category?id='.$param->category_id);     
    }     
    
    /** 
     * Displays a login form and performs the authentication.
     */   
         
    public function login()
    {
        $username = $this->get_arg('username', '');
        $password = $this->get_arg('password', '');
        global $config;
        
        $data = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            if ($username == $config['admin']['username'] && 
                $password == $config['admin']['password'])
            {
                $_SESSION['admin'] = true;
                return new Redirect('/');  
            }
            else
            {
                $data['error'] = 'Wrong login or password';
            }
        }
        
        return new View('login', $data);
    }      
    
    /** 
     * Logs the user out.
     */   
         
    public function logout()
    {
        unset($_SESSION['admin']);
        
        return new Redirect('/');
    } 
        
    /** 
     * Checks whether the user is authorized.
     */      
    
    protected function check_authorization()
    {
        if (empty($_SESSION['admin']))
        {
            throw new HttpError(HttpError::FORBIDDEN);
        }
    }
    
    /** 
     * Returns request parameter.
     * @param $name a name of the parameter.
     * @param $default_value a value to return if the parameter isn't set.
     */      

    protected function get_arg($name, $default_value)
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default_value;
    }
    
    /** 
     * Generates catalog breadcrumbs.
     * @param $category_id id of the category.
     * @param $tail an array containing elements to be apended to the breadcrumbs array.
     */      
    
    protected function generate_breadcrumbs($category_id, $tail = array())
    {
        $category = new Category($category_id);    
        $ancestors = $category->get_ancestors();

        $breadcrumbs = array();
        foreach ($ancestors as $ancestor)
        {
            $breadcrumbs['/show_category?id='.$ancestor->id] = $ancestor->name;
        }

        return array_merge($breadcrumbs, $tail);
    }           
     
}

?>
