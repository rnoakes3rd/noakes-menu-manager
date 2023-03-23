<?php
/*!
 * HTML field functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage HTML Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the HTML field object.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Field
 */
final class Noakes_Menu_Manager_Field_HTML extends Noakes_Menu_Manager_Field
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
			 * Content added to the field.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'content':
			
				return '';
		}

		return parent::_default($name);
	}
	
	/**
	 * Generate the output for the HTML field.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the HTML field should be echoed.
	 * @return string        Generated HTML field if $echo is false.
	 */
	public function output($echo = false)
	{
		$output = '<div class="nmm-html' . $this->_field_classes(false) . '">'
			. wpautop(do_shortcode($this->content))
		. '</div>';
		
		return parent::_output($output, 'html', $echo);
	}
}
