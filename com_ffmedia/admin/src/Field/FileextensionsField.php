<?php
/**
 * @package     Ffmedia.Administrator
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E. Ford <https://www.fford.me.uk>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Ffmedia\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

/**
 * Fileextenions field.
 *
 * @since  1.6
 */
class FileextensionsField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $type = 'Fileextensions';

	/**
	 * Method to get the field input markup for file extensions.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getInput()
	{
		// get the list of allowed file extensions
		$params = ComponentHelper::getParams('com_ffmedia');
		$extensions = $params->get('file_upload_extensions');
		$html = '
			<select id="filter_extension" name="filter[extension]" class="custom-select" onchange="this.form.submit();">
			<option value="" selected="selected">' . Text::_('COM_FFMEDIA_SELECT_EXTENSION') . '</option>';
		$items = explode(',', $extensions);
		asort($items);
		foreach ($items as $item)
		{
			if ($item == $this->value)
			{
				$selected = '" selected="selected"';
			}
			else
			{
				$selected = '"';
			}
			$html .= '<option value="' . $item . $selected. '>' . $item  . '</option>' . "\n";
		}
		$html .= "</select>\n";
		return $html;
	}
}
