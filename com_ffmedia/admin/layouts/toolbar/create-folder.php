<?php
/**
 * @package     Ffmedia.Administrator
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E. Ford <https://www.fford.me.uk>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$view = $this->options->get('view');

?>
<joomla-toolbar-button>
	<button id="ffmediaCreateFolder" class="button-ffmedia dropdown-item"
		data-view="<?php echo $view; ?>">
		<span class="fa-folder-plus icon-fw" aria-hidden="true"></span>
		<?php echo Text::_('COM_FFMEDIA_CREATE_NEW_FOLDER'); ?>
	</button>
</joomla-toolbar-button>
