<?php
	/*!
	 * Repeatable buttons template.
	 * 
	 * @since 3.0.0
	 * 
	 * @package Nav Menu Manager
	 */

	if (!defined('ABSPATH'))
	{
		exit;
	}
?>

<script id="tmpl-nmm-repeatable-buttons" type="text/html">

	<a class="nmm-repeatable-move" href="javascript:;" tabindex="-1" title="<?php esc_attr_e('Move Item', 'noakes-menu-manager'); ?>">
	
		<span class="nmm-repeatable-count"></span>
		<span class="nmm-repeatable-move-button"><span class="dashicons dashicons-move"></span></span>
		
	</a>
	
	<a class="nmm-repeatable-move-up" href="javascript:;" tabindex="-1" title="<?php esc_attr_e('Move Item Up', 'noakes-menu-manager'); ?>"><span class="dashicons dashicons-arrow-up-alt"></span></a>
	<a class="nmm-repeatable-move-down" href="javascript:;" tabindex="-1" title="<?php esc_attr_e('Move Item Down', 'noakes-menu-manager'); ?>"><span class="dashicons dashicons-arrow-down-alt"></span></a>
	<a class="nmm-repeatable-insert" href="javascript:;" tabindex="-1" title="<?php esc_attr_e('Insert Item Above', 'noakes-menu-manager'); ?>"><span class="dashicons dashicons-plus"></span></a>
	<a class="nmm-repeatable-remove" href="javascript:;" tabindex="-1" title="<?php esc_attr_e('Remove Item', 'noakes-menu-manager'); ?>"><span class="dashicons dashicons-no"></span></a>
	
</script>
