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
	<button id="ffmediaDeleteIfEmpty" class="button-ffmedia dropdown-item"
		data-view="<?php echo $view; ?>">
		<span class="fa-folder-minus icon-fw" aria-hidden="true"></span>
		<?php echo Text::_('COM_FFMEDIA_TOOLBAR_BUTTON_DELETE_IF_EMPTY'); ?>
	</button>
</joomla-toolbar-button>
