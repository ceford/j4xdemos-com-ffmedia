<?php
/**
 * @package     Ffmedia.Administrator
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E. Ford <https://www.fford.me.uk>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Ffmedia\Administrator\Helper;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Language\Text;

/**
 * Ffmedia component helper.
 *
 * @since  4.0
 */

Class JsHelper
{	/*
	 * Set the strings used in Javascript
	 *
	 * @return void
	 *
	 * @since   4.0.0
	*/
	public static function getJstext()
	{
		Text::script('COM_FFMEDIA_JS_CLICK_TO_COPY', true);
		Text::script('COM_FFMEDIA_JS_DELETE_IF_EMPTY', true);
		Text::script('COM_FFMEDIA_JS_DELETE_ITEM', true);
		Text::script('COM_FFMEDIA_JS_FIGURE_TAG', true);
		Text::script('COM_FFMEDIA_JS_ERROR_STATUS', true);
		Text::script('COM_FFMEDIA_JS_FOLDER_NAME_EMPTY', true);
		Text::script('COM_FFMEDIA_JS_FOLDER_NAME_IS_OK', true);
		Text::script('COM_FFMEDIA_JS_FOLDER_NAME_NO_STOP', true);
		Text::script('COM_FFMEDIA_JS_FOLDER_TRASH_ITEMS', true);
		Text::script('COM_FFMEDIA_JS_HASH_ALL', true);
		Text::script('COM_FFMEDIA_JS_HASH_FOLDER', true);
		Text::script('COM_FFMEDIA_JS_IMAGE_TAG', true);
		Text::script('COM_FFMEDIA_JS_IMAGE_ZOOM', true);
		Text::script('COM_FFMEDIA_JS_INDEX_ALL', true);
		Text::script('COM_FFMEDIA_JS_INDEX_FOLDER', true);
		Text::script('COM_FFMEDIA_JS_NFOLDERS_TO_PROCESS', true);
		Text::script('COM_FFMEDIA_JS_PICTURE_TAG', true);
		Text::script('COM_FFMEDIA_JS_PLEASE_SELECT_FILE', true);
		Text::script('COM_FFMEDIA_JS_RESTORE_ITEM', true);
		Text::script('COM_FFMEDIA_JS_SHARE_LINK', true);
		Text::script('COM_FFMEDIA_JS_TRASH_ITEM', true);

		Text::script('ERROR', true);
		Text::script('JACTION_CREATE', true);
		Text::script('JAPPLY', true);
		Text::script('JCANCEL', true);
		Text::script('JGLOBAL_CONFIRM_DELETE', true);
		Text::script('JLIB_FORM_FIELD_REQUIRED_VALUE', true);
		Text::script('MESSAGE', true);
	}
}