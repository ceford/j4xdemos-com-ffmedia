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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;
use J4xdemos\Component\Ffmedia\Administrator\Helper\JsHelper;
use J4xdemos\Component\Ffmedia\Administrator\Helper\MimetypesHelper;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useStyle('com_ffmedia.ffmedia')
	->useScript('com_ffmedia.ffmedia');

$params = ComponentHelper::getParams('com_ffmedia');

$mthelper = new MimetypesHelper;
$mimeTypes = $mthelper->getMimetypes($params->get('file_upload_extensions'));

// Populate the language
JsHelper::getJstext();

?>
<form action="" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col">

		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'file')); ?>

			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'file', Text::_('COM_FFMEDIA_FILE')); ?>

			<div class="row">
				<div class="col-12 col-lg-6">
					<p><?php echo Text::_('COM_FFMEDIA_FIELD_FILE_EXTENSIONS_LABEL'); ?>
					<?php echo str_replace(',', ', ', $params->get('file_upload_extensions')); ?>.
					<br />
					<?php echo Text::_('COM_FFMEDIA_FIELD_FILE_MAXIMUM_SIZE_LABEL'); ?>
					<?php echo $params->get('file_upload_maxsize'); ?></p>

					<?php if (empty($this->item->id)) : ?>
					<div class="control-group">
						<div class="control-label">
							<label id="jform_uploadfile-lbl" for="jform_uploadfile">
								<?php echo Text::_('COM_FFMEDIA_FILE_UPLOAD_LABEL'); ?>
							</label>
						</div>
						<div class="controls">
							<input type="file" required="required" name="jform[uploadfile]" id="jform_uploadfile" class="form-control"
							accept="<?php echo $mimeTypes; ?>">
							<div id="jform[uploadfile]-desc">
								<small class="form-text text-muted">
									<?php echo Text::_('COM_FFMEDIA_FILE_UPLOAD_DESC'); ?>
								</small>
							</div>
						</div>
					</div>
					<?php endif; ?>

					<div class="control-group">
						<div class="control-label">
							<label id="jform_alt-lbl" for="jform_alt">
							<?php echo Text::_('COM_FFMEDIA_FILE_UPLOAD_SHORT_DESCRIPTION_LABEL'); ?>
							</label>
						</div>
						<div class="controls">
							<?php
							if (empty($this->item->alt)) {
								$alt = substr($this->item->file_name, 0, strrpos($this->item->file_name, '.'));
								$alt = str_replace('-', ' ', $alt);
							} else {
								$alt = $this->item->alt;
							}
							?>
							<input type="text" required="required" name="jform[alt]" id="jform_alt" class="form-control"
							value="<?php echo $alt; ?>" />
							<div id="jform[alt]-desc">
								<small class="form-text text-muted">
									<?php echo Text::_('COM_FFMEDIA_FILE_UPLOAD_SHORT_DESCRIPTION_DESC'); ?>
								</small>
							</div>
						</div>
					</div>

					<div class="control-group">
						<div class="control-label">
							<label id="jform_file_name-lbl" for="jform_file_name">
							<?php echo Text::_('COM_FFMEDIA_FILE_UPLOAD_FILE_NAME_LABEL'); ?>
							</label>
						</div>
						<div class="controls">
							<input type="text" name="jform[file_name]" id="jform_file_name" class="form-control" readonly
							value="<?php echo isset($this->item->file_name) ? $this->item->file_name : ''; ?>"/>
							<div id="jform[file_name]-desc">
								<small class="form-text text-muted">
									<?php echo Text::_('COM_FFMEDIA_FILE_UPLOAD_FILE_NAME_DESC'); ?>
								</small>
							</div>
						</div>
					</div>

					<div class="control-group">
						<div class="control-label">
							<label id="jform_photo_caption-lbl" for="jform_photo_caption" class="hasPopover" title="Photo caption" data-content="What, Where, When in up to 256 characters.">
							<?php echo Text::_('COM_FFMEDIA_FILE_UPLOAD_LONG_DESCRIPTION_LABEL'); ?>
							</label>
						</div>
						<div class="controls">
							<textarea required="required" cols="60" rows="3" name="jform[caption]" id="jform_caption"
							class="form-control" ><?php echo (isset($this->item->caption) ? $this->item->caption : ''); ?></textarea>
							<div id="jform[caption]-desc">
								<small class="form-text text-muted">
							<?php echo Text::_('COM_FFMEDIA_FILE_UPLOAD_LONG_DESCRIPTION_DESC'); ?>
								</small>
							</div>
						</div>
					</div>

					<?php if (!empty($this->item->id)) : ?>
					<div class="control-group">
						<div class="control-label">
							<label id="jform_uploadfile-lbl" for="jform_uploadfile">
								<?php echo Text::_('COM_FFMEDIA_FILE_REPLACEMENT_LABEL'); ?>
							</label>
						</div>
						<div class="controls">
							<input type="file" name="jform[uploadfile]" id="jform_uploadfile" class="form-control"
							accept="<?php echo $mimeTypes; ?>">
							<div id="jform[uploadfile]-desc">
								<small class="form-text text-muted">
									<?php echo Text::_('COM_FFMEDIA_FILE_UPLOAD_DESC'); ?>
								</small>
							</div>
						</div>
					</div>
					<?php endif; ?>

					<div class="control-group">
						<div class="control-label">
							<label id="jform_id-lbl" for="jform_id">
								<?php echo Text::_('JGLOBAL_FIELD_ID_LABEL'); ?>
							</label>
						</div>
						<div class="controls">
							<input type="text" name="jform[id]" id="jform_id" class="form-control"
							value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" readonly />
						</div>
					</div>
				</div>
			</div>

			<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
		</div>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="jform[media_type]" id="jform_media_type" value="file" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

