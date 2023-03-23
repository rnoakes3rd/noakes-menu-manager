<?php
/*!
 * Hidden field functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Hidden Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the hidden field object.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Field
 */
final class Noakes_Menu_Manager_Field_Hidden extends Noakes_Menu_Manager_Field_Text
{
	/**
	 * Get a default value based on the provided name.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 * @param  string $name Name of the value to return.
	 * @return mixed        Default value if it exists, otherwise an empty string.
	 */
	protected function _default($name)
	{
		switch ($name)
		{
			/**
			 * Input type attribute.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'input_type':
			
				return 'hidden';
		}

		return parent::_default($name);
	}
	
	/**
	 * Generate the output for the hidden field.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the hidden field should be echoed.
	 * @return string        Generated hidden field if $echo is false.
	 */
	public function output($echo = false)
	{
		$this->push('wrapper_classes', 'nmm-hidden');
		
		return parent::output($echo);
	}
}
