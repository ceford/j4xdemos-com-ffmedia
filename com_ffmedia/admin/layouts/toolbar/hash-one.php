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

?>

<joomla-toolbar-button>
	<button id="ffmediaHashOne" class="button-ffmedia dropdown-item">
		<span class="fa-hashtag icon-fw" aria-hidden="true"></span>
		<?php echo Text::_('COM_FFMEDIA_TOOLBAR_BUTTON_HASH_ONE'); ?>
	</button>
</joomla-toolbar-button>
