<?php
/**
 * @package     Ffmedia.Administrator
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E. Ford <https://www.fford.me.uk>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;
use J4xdemos\Component\Ffmedia\Administrator\Helper\FolderHelper;
use J4xdemos\Component\Ffmedia\Administrator\Helper\JsHelper;

$params = ComponentHelper::getParams('com_ffmedia');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_ffmedia.ffmedia')
	->useScript('com_ffmedia.ffmedia');

// Populate the language
//$this->loadTemplate('texts');
JsHelper::getJstext();

$media_type_options = array('image' => Text::_('COM_FFMEDIA_FIELDSET_IMAGES_LABEL') , 'file' => Text::_('COM_FFMEDIA_FIELDSET_FILES_LABEL'));

?>

<div class="row">
	<div class="col-12 col-sm-6">
		<form action="index.php?option=com_ffmedia&view=folders" method="post" name="adminForm" id="adminForm">

		<div class="control-group">
			<div class="control-label">
				<label id="filter_mediatype-lbl" for="filter_mediatype">
					<?php echo Text::_('COM_FFMEDIA_SELECT_MEDIA_TYPE'); ?>
				</label>
			</div>
			<div class="controls media-select">
				<select id="filter_mediatype" name="filter[mediatype]" class="custom-select">
					<?php echo HTMLHelper::_('select.options', $media_type_options, 'value', 'text', $this->mediatype, true); ?>
				</select>
			</div>
		</div>

		<h3><?php echo Text::_('COM_FFMEDIA_FOLDER_TREE'); ?></h3>

		<?php
			$folders = FolderHelper::getTree($this->activepath);
			$layout = new FileLayout('foldertree', JPATH_COMPONENT .'/layouts', array('activepath' => $this->activepath,'folders' => $folders));
			echo $layout->render();
		?>
		<br />

		<input type="hidden" name="task" id="task" value="">
		<input type="hidden" name="boxchecked" value="0">
		<input type="hidden" name="filter[activepath]" id="filter_activepath" value="<?php echo $this->activepath; ?>">
		<input type="hidden" name="jform[newfoldername]" id="jform_newfoldername" value="">
		<?php echo HTMLHelper::_('form.token'); ?>

		</form>
	</div>

	<div class="col-12 col-sm-6 results-box">
		<h3><?php echo Text::_('COM_FFMEDIA_ACTION_RESULTS'); ?></h3>
		<p>Folders may be processed in random order! Please wait for the job to complete!</p>

		<div id="results">

		</div>
	</div>
</div>
