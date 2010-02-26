<?php
/**
 * @version	1.5
 * @package	Tienda
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2009 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

JLoader::import( 'com_tienda.models._base', JPATH_ADMINISTRATOR.DS.'components' );

/**
 * TODO Couldn't this also be moved to admin/models with the others?
 * @author Rafael Diaz-Tushman
 *
 */
class TiendaModelSessioncarts extends TiendaModelBase
{
    /**
     * Fetches cart items from the session.
     * @return array
     */ 
    function getList()
    {
        $mainframe =& JFactory::getApplication();
        $list = $mainframe->getUserState( 'usercart.cart' );
        if (empty($list)) 
        {
            $list = array();
        }
        
        foreach(@$list as $item)
        {
        	JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tienda'.DS.'models' );
	        $model = JModel::getInstance('Products', 'TiendaModel');
	        $model->setId( (int) $item->product_id );
	        $product = $model->getItem();
	        $item->product_name = $product->product_name;
	        $item->product_price = $product->price;
        
            $item->attributes = array(); // array of each selected attribute's object
            $attributes_names = array();
            $attibutes_array = explode(',', $item->product_attributes);
            foreach ($attibutes_array as $attrib_id)
            {
                // load the attrib's object
                $table = JTable::getInstance('ProductAttributeOptions', 'TiendaTable');
                $table->load( $attrib_id );
                // update the price
                $item->product_price = $item->product_price + floatval( "$table->productattributeoption_prefix"."$table->productattributeoption_price");
                // store a csv of the attrib names
                $attributes_names[] = JText::_( $table->productattributeoption_name ); 
            }
            $item->attributes_names = implode(', ', $attributes_names);
        }
        
        return $list;
    }

    function getTable()
    {  
        return new TiendaSessionCart;
    }
}

/**
 * Class to make sessionCart impersonate a JTable object
 * 
 * TODO Couldn't this just be stored in the admin/tables folder as the others?
 *
 *
 */
class TiendaSessionCart 
{
    public $user_id;
    public $product_id;
    public $product_name;
    public $product_price;
    public $product_qty;
    
    function save()
    {
        $mainframe =& JFactory::getApplication();
        $cart = $mainframe->getUserState( 'usercart.cart' );
        foreach (@$cart as $item) 
        {
            if ($item->product_id == $this->product_id && $item->product_attributes == $this->product_attributes) 
            {
            	$item->product_qty = $this->product_qty;
            }

            // be sure that product_attributes is sorted numerically
	        if ($product_attributes = explode( ',', $item->product_attributes ))
	        {
	            sort($product_attributes);
	            $item->product_attributes = implode(',', $product_attributes);
	        }
        }
        $mainframe->setUserState( 'usercart.cart', $cart);
    }
    
    function bind($vals)
    {
        $this->product_id  = (array_key_exists('product_id', $vals))  ? $vals['product_id']  : '';
        $this->product_qty = (array_key_exists('product_qty', $vals)) ? $vals['product_qty'] : '';
        $this->product_attributes = (array_key_exists('product_attributes', $vals)) ? $vals['product_attributes'] : '';
    }
    
    function delete($data)
    {
    	$itemRemoved = false;
    	
        $userid         = $data['user_id'];
        $product_id     = $data['product_id'];
        $product_attributes = $data['product_attributes'];
        // $userid isn't used but is added for allowing abstraction with the carts model
        $mainframe =& JFactory::getApplication();
        $cart = $mainframe->getUserState( 'usercart.cart' );
        foreach ($cart as $key=>$item) 
        {
        	if ($item->product_id == $product_id && $item->product_attributes == $product_attributes)
        	{
        		$itemRemoved = true;
        		unset($cart[$key]);
        	}
        }
        
        $mainframe->setUserState( 'usercart.cart', $cart);
        return $itemRemoved;
        
    }
}
