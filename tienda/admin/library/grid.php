<?php
/**
 * @version	1.5
 * @package	Tienda
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'html'.DS.'grid.php' );

class TiendaGrid extends JHTMLGrid
{
	/**
	 * @param	string	The link title
	 * @param	string	The order field for the column
	 * @param	string	The current direction
	 * @param	string	The selected ordering
	 * @param	string	An optional task override
	 */
	function sort( $title, $order, $direction = 'asc', $selected = 0 )
	{
		JHTML::_('script', 'tienda.js', 'media/com_tienda/js/');
		
		$direction	= strtolower( $direction );
		$images		= array( 'sort_asc.png', 'sort_desc.png' );
		$index		= intval( $direction == 'desc' );
		$direction	= ($direction == 'desc') ? 'asc' : 'desc';

		$html = '<a href="javascript:tiendaGridOrdering(\''.$order.'\',\''.$direction.'\');" title="'.JText::_('COM_TIENDA_CLICK_TO_SORT_THIS_COLUMN').'">';
		$html .= JText::_( $title );
		if ($order == $selected ) {
			$html .= JHTML::_('image.administrator',  $images[$index], '/images/', NULL, NULL);
		}
		$html .= '</a>';
		return $html;
	}
	
	/**
	 * 
	 * @param $id
	 * @return unknown_type
	 */
	function order($id)
	{
		JHTML::_('script', Tienda::getName().'.js', 'media/com_tienda/js/');
		
		$up   = 'uparrow.png'; $up_title = JText::_('COM_TIENDA_MOVE_UP');
		$down = 'downarrow.png'; $down_title = JText::_('COM_TIENDA_MOVE_DOWN');

		$result =
			'<a href="javascript:tiendaGridOrder('.$id.', -1)" >'
			. JHTML::_('image.administrator',  $up, '/images/', NULL, NULL, $up_title, 'border="0" title="'.$up_title.'"')
			.'</a>'
			.'<a href="javascript:tiendaGridOrder('.$id.', 1)" >'
			. JHTML::_('image.administrator',  $down, '/images/', NULL, NULL, $down_title, 'border="0" title="'.$down_title.'"')
			.'</a>';
			
		return $result;
	}
	
	/**
	 * 
	 * @param $id
	 * @param $value
	 * @return unknown_type
	 */
	public static function ordering( $id, $value)
	{
		$result =
			 '
			 <input type="text" 
			 name="ordering['.$id.']" 
			 size="5" 
			 value="'.$value.'" 
			 class="text_area" 
			 style="text-align: center" 
			 />
			 ';
		
		return $result;
	}
	
	/**
	 * Shows a true/false graphics
	 *
	 * @param	bool	Value
	 * @param 	string	Image for true
	 * @param 	string	Image for false
	 * @param 	string 	Text for true
	 * @param 	string	Text for false
	 * @return 	string	Html img
	 */
	public static function boolean( $bool, $true_img = null, $false_img = null, $true_text = null, $false_text = null)
	{
		$true_img 	= $true_img 	? $true_img 	: 'tick.png';
		$false_img 	= $false_img	? $false_img	: 'publish_x.png';
		$true_text 	= $true_text 	? $true_text 	: 'Yes';
		$false_text = $false_text 	? $false_text 	: 'No';
		
		return '<img src="' . Tienda::getUrl("images") . ($bool ? $true_img : $false_img) .'" border="0" alt="'. JText::_($bool ? $true_text : $false_text) .'" />';
	}
	
	function published( $row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		$img 	= $row->published ? $imgY : $imgX;
		$task 	= $row->published ? 'unpublish' : 'publish';
		$alt 	= $row->published ? JText::_('COM_TIENDA_PUBLISHED') : JText::_('COM_TIENDA_UNPUBLISHED');
		$action = $row->published ? H : JText::_('COM_TIENDA_UNPUBLISH_ITEM');

		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
		<img src="' . Tienda::getUrl("images") . $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}
	
	public static function enable( $enable, $i, $prefix = '', $imgY = 'tick.png', $imgX = 'publish_x.png' )
	{
		$img 	= $enable ? $imgY : $imgX;
		$task 	= $enable ? 'disable' : 'enable';
		$alt 	= $enable ? JText::_('COM_TIENDA_ENABLED') : JText::_('COM_TIENDA_DISABLED');
		$action = $enable ? JText::_('COM_TIENDA_DISABLE_ITEM') : JText::_('COM_TIENDA_ENABLE_ITEM');
		
        $href = '
        <a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
        <img src="' . Tienda::getUrl("images") . $img .'" border="0" alt="'. $alt .'" />
        </a>';
        
		return $href;
	}
	
	function checkedout( &$row, $i, $identifier = 'id' )
	{
		$user   = JFactory::getUser();
		$userid = $user->get('id');

		$result = false;
		if (!isset($row->checked_out))
		{
			$result = false;	
		}
			elseif (is_a($row, 'JTable')) 
		{
			$result = $row->isCheckedOut($userid);
		} 
			else 
		{
			$result = JTable::isCheckedOut($userid, $row->checked_out);
		}

		$checked = '';
		if ( $result ) 
		{
			if (isset($row->editor))
			{
				$checked = parent::_checkedOut( $row );	
			}
				else
			{
				$text = JFactory::getUser($row->checked_out)->username;
				$date = JHTML::_('date',  $row->checked_out_time, JText::_('DATE_FORMAT_LC1') );
				$time = JHTML::_('date',  $row->checked_out_time, '%H:%M' );
				$hover = '<span class="editlinktip hasTip" title="'. JText::_('COM_TIENDA_CHECKED_OUT_BY') .' '. $text .' '.JText::_('COM_TIENDA_ON').' '. $date .' '.JText::_('COM_TIENDA_AT').' '. $time .'">';
				$checked = $hover .'<img src="images/checked_out.png"/></span>';
			}
			
		} 
			else 
		{
			$checked = JHTML::_('grid.id', $i, $row->$identifier );
		}

		return $checked;
	}
	
	public static function pagetooltip( $key, $title='Tip', $id='page_tooltip' )
	{
		$href = '';
		
		$constant = "COM_TIENDA_PAGE_TOOLTIP_".$key;		
		$disabled = TiendaConfig::getInstance()->get( $constant."_disabled", '0');
		
		$lang = JFactory::getLanguage();
		if ($lang->hasKey($constant) && !$disabled)
		{
			$view = strtolower( JRequest::getVar('view') );
			$task = "page_tooltip_disable";
			$url = JRoute::_("index.php?option=com_tienda&controller={$view}&view={$view}&task={$task}&key={$key}");
			$link = "<a href='{$url}'>".JText::_('COM_TIENDA_HIDE_THIS')."</a>";
			
			$href = '
				<fieldset class="'.$id.'">
					<legend class="'.$id.'">'.JText::_($title).'</legend>
					'.JText::_($constant).'
					<span class="'.$id.'" style="float: right;">'.$link.'</span>
				</fieldset>
			';			
		}

		return $href;
	}
	
	public static function checkoutnotice( $row, $title='Item', $lock_task='edit' )
	{
		if (!isset($row->checked_out))
		{
			return null;	
		}
		
		if (JFactory::getUser()->id == @$row->checked_out)
		{
			$html = "
			<div class='note'>
				".JText::_('COM_TIENDA_$TITLE_CHECKED_OUT_BY_YOU')."
				<button onclick='document.getElementById(\"task\").value=\"release\"; this.form.submit();'>".JText::_('COM_TIENDA_RELEASE_$TITLE')."</button>
			</div>
			";
		}
			elseif (!empty($row->checked_out))
		{
			$html = "
			<div class='note'>
				".sprintf( JText::_('COM_TIENDA_$TITLE_CHECKED_OUT_BY_ANOTHER'), JFactory::getUser( @$row->checked_out )->username )."
			</div>
			";
		}
			else
		{
			$html = "
			<div class='note'>
				".JText::_('COM_TIENDA_$TITLE_CHECKED_OUT_BY_NOBODY')."
				<button onclick='document.getElementById(\"task\").value=\"$lock_task\"; this.form.submit();'>".JText::_('COM_TIENDA_LOCK_$TITLE')."</button>
			</div>
			";
		}
		
		return $html;
	}
	
	public static function required()
	{
	    $html = '<img src="'.Tienda::getUrl( 'images' ).'required_16.png" alt="'.JText::_('COM_TIENDA_REQUIRED').'">';
        return $html;
	}
}