<?php
/**
 * @package     Ffmedia.Administrator
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E. Ford <https://www.fford.me.uk>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

Factory::getDocument()->getWebAssetManager()
	->useScript('webcomponent.toolbar-button');

$nRecords = $this->options->get('nrecords');

$title = $nRecords . ' ' . Text::_('COM_FFMEDIA_TOOLBAR_BUTTON_RECORDS')

?>
<joomla-toolbar-button id="toolbar-info" task="">
	<button class="btn btn-info" type="button">
		<span class="icon-info" aria-hidden="true"></span>
		<?php echo $title; ?>
	</button>
</joomla-toolbar-button>
