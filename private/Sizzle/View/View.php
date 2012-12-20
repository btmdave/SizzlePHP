<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    View
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\View;
use Sizzle\View\ViewException,
    Sizzle\Loader\Loader,
    Sizzle\Controller\Controller;

/**
 * View
 *
 * @category   Sizzle
 * @package    View
 * @author     David Squires <dave@bluetopmedia.com>
 */
class View {

    /**
     * Source of the view file
     * @var string
     */
    protected $view;
    
    /**
     * Config
     * @var array
     */
    private $config   = array();
    
    /**
     * Variables assigned to a view
     * @var array
     */
    private $vars     = array();
    
    /**
     * Instance of the controller
     * @var object
     */
    private $c_instance;
    
    /**
     * Name of a defined layout
     * @var string
     */
    private $layout;
    
    /**
     * Construct.  
     * Load our config settings.
     */
    public function __construct() {
        
        $this->config = Loader::loadConfig();
    }
    
    /**
     * Magically retreive a set parameter value.
     * We also receive the instance of the controller to allow all instantiated objects 
     * from the controller to become accessable within the view.
     * 
     * @param string $key
     */
    public function __get($key) 
    { 	

        if(array_key_exists($key, $this->vars)) {
            return $this->vars[$key];
        }
        
        $this->c_instance =& Controller::getInstance();
        if(isset($this->c_instance->$key)){
        	return $this->c_instance->$key;
        }
    }
       
    
    /**
     * Magically set a variable to be used within a view.   
     * We define this in our controllers.
     * 
     * <code>
     * $this->View->item_name = 'Something';
     * 
     * //$this->item_name is now accessable within our views.
     * 
     * </code>
     * 
     * @param unknown_type $name
     * @param unknown_type $val
     */
    public function __set($name, $val) 
    {
        $this->vars[$name] = $val;
    } 
    
    /**
     * Magically check if a variable is set.
     * @param string $name
     */
    public function __isset($name)
    {
    	return isset($this->vars[$name]);
    }    

    /**
     * 
     * Render the view
     * 
     * @param  array|string     $views    name of view or array of views to render
     * @param  boolean          $layout   name of layout to render within.  Uses config's default layout if undefined.
     * @param  boolean          $return   return the content to a variable, instead of displaying on the page (used with AJAX requests)
     * @throws ViewException
     * @return string                     rendered content from the view(s)
     */
    public function render($views = null, $layout = true, $return = false)
    {
    
        if(is_array($views)) {
            
          foreach($views as $view) { 
                 
                $view_dir = str_replace('_','/', $view);
                $view_file = '../private/application/views/'.$view_dir.'.phtml';
              
                if (!file_exists($view_file)){
                    throw new ViewException('View was not found: '.$view_file);
                }
                
                ob_start();
                include $view_file;
                $this->view[$view] = ob_get_contents();
                ob_end_clean(); 
         
          }
          
        } elseif($views) {
            
                $view_dir = str_replace('_','/', $views);
                $view_file = '../private/application/views/'.$view_dir.'.phtml';
                
                if (!file_exists($view_file)){
                    throw new ViewException('View was not found: '.$view_file);
                }
                
                ob_start();
                include $view_file;
                $this->view = ob_get_contents();
                ob_end_clean();

        }
        
        if($layout) {
            $this->bindLayout();
        } else {
            if($return){
                return $this->view;
            }else{
                echo $this->view;
            }
        }
    }
    
    /**
     * Used within the layout to load a helper layout.  
     * <code>
     *  $this->loadLayoutHelper('navigation');
     * </code>
     * @param  string         $name
     * @throws ViewException
     */
    public function loadLayoutHelper($name = null)
    {
        
        $helper_dir = str_replace('_','/', $name);
        $helper_file = '../private/application/layouts/helpers/'.$helper_dir.'.phtml';
        
        if (!file_exists($helper_file)){
            throw new ViewException('Layout helper was not found: '.$helper_file);
        }
        
        ob_start();
        include_once $helper_file;
        $helper = ob_get_contents();
        ob_end_clean();
        
        return $helper;
        
    }
    
    /**
     * Set the layout to be binded with the View
     * @param string $layout
     */
    public function setLayout($layout)
    {
    	$this->layout = $layout;
    }
    
    /**
     * We bind the actually layout to together, by including it.
     * @param  string         $name
     * @throws ViewException
     * @see    $this->render()
     */
    public function bindLayout() 
    {
       
        if (!isset($this->config['layout'])) {
        	return;
        }

        $layout = (!empty($this->layout)) ? $this->config['layout'][$this->layout] : $this->config['layout']['default'];
        $layout_file = '../private/application/layouts/'.ucwords($layout).'.phtml';

        if (!file_exists($layout_file)){
            throw new ViewException('Layout was not found: '.$layout_file);
        } 
         
        include_once $layout_file;
    }
}