<?php
/**
 * @package     Ffmedia.Site
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E. Ford <https://www.fford.me.uk>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

Text::script('COM_FFMEDIA_JS_IMAGE_ZOOM', true);

$wa = $this->document->getWebAssetManager();
$wa->useScript('com_ffmedia.ffmedia-site');

?>

<h1><?php echo $this->title; ?></h1>
<div class="com-ffmedia-category">
	<?php
		echo $this->loadTemplate('items');
	?>
</div>
<?php
$footer = '
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
';
	echo HTMLHelper::_(
	'bootstrap.renderModal',
	'collapseModal',
	[
		'title' => Text::_('COM_FFMEDIA_JS_IMAGE_ZOOM'),
		'footer' => $footer
	],
	'<h3>Transient Modal Stuff</h3><div id="modal-content">Transient Modal Content</div>'
); ?>
