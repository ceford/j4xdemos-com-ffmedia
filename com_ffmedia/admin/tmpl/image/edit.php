<?php
/**
 * @package     Ffmedia.Administrator
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E. Ford <https://www.fford.me.uk>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use J4xdemos\Component\Ffmedia\Administrator\Helper\JsHelper;
use J4xdemos\Component\Ffmedia\Administrator\Helper\MimetypesHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useStyle('com_ffmedia.ffmedia')
	->useScript('com_ffmedia.ffmedia');

$params = ComponentHelper::getParams('com_ffmedia');

$mthelper = new MimetypesHelper;
$mimeTypes = $mthelper->getMimetypes($params->get('image_upload_extensions'));

// Populate the language
JsHelper::getJstext();

?>

<form action="" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col">
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'photo')); ?>

			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'photo', Text::_('COM_FFMEDIA_IMAGE_EDIT_TAB_IMAGE')); ?>

			<div class="row">
				<div class="col-12 col-lg-6">

					<p><?php echo Text::_('COM_FFMEDIA_FIELD_IMAGE_EXTENSIONS_LABEL'); ?>
					<?php echo str_replace(',', ', ', $params->get('image_upload_extensions')); ?>.
					<br />
					<?php echo Text::_('COM_FFMEDIA_FIELD_IMAGE_MAXIMUM_SIZE_LABEL'); ?>
					<?php echo $params->get('image_upload_maxsize'); ?></p>

					<?php if (empty($this->item->id)) : ?>
					<div class="control-group">
						<div class="control-label">
							<label id="jform_uploadfile-lbl" for="jform_uploadfile">
								<?php echo Text::_('COM_FFMEDIA_IMAGE_UPLOAD_LABEL'); ?>
							</label>
						</div>
						<div class="controls">
							<input type="file" required="required" name="jform[uploadfile]" id="jform_uploadfile" class="form-control"
							accept="<?php echo $mimeTypes; ?>">
							<div id="jform[uploadfile]-desc">
								<small class="form-text text-muted">
								<?php echo Text::_('COM_FFMEDIA_IMAGE_UPLOAD_DESC'); ?>
								</small>
							</div>
						</div>
					</div>
					<?php endif; ?>

					<div class="control-group">
						<div class="control-label">
							<label id="jform_alt-lbl" for="jform_alt">
							<?php echo Text::_('COM_FFMEDIA_IMAGE_ALT_LABEL'); ?>
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
									<?php echo Text::_('COM_FFMEDIA_IMAGE_ALT_DESC'); ?>
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
								<?php echo Text::_('COM_FFMEDIA_IMAGE_CAPTION_LABEL'); ?>
							</label>
						</div>
						<div class="controls">
							<textarea required="required" cols="60" rows="3" name="jform[caption]" id="jform_caption"
							class="form-control" ><?php echo (isset($this->item->caption) ? $this->item->caption : ''); ?></textarea>
							<div id="jform[caption]-desc">
								<small class="form-text text-muted">
									<?php echo Text::_('COM_FFMEDIA_IMAGE_CAPTION_DESC'); ?>
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
							<label id="jform_tn_width-lbl" for="jform_tn_width">
								<?php echo Text::_('COM_FFMEDIA_IMAGE_THUMB_LABEL'); ?>
							</label>
						</div>
						<div class="controls">
							<select name="jform[tn_width]" id="jform_tn_width" class="form-select">
							<option value="100">100</option>
							<option value="150">150</option>
							<option value="0">0</option>
							</select>
						</div>
					</div>

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

				<div class="col-12 col-md-6 col-lg-3">

					<?php if (isset($this->item->id)) : ?>
					<img src="<?php echo JURI::root(true) . $this->item->folder_path . '/' . $this->item->file_name; ?>"
						class="cover"
					/>
					<?php else : ?>
					<div class="image-holder">
						<p><?php echo Text::_('COM_FFMEDIA_IMAGE_UPLOAD_NOT_YET'); ?></p>
					</div>
					<?php endif; ?>
				</div>
			</div>

			<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
		</div>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="jform[media_type]" id="jform_media_type"value="image" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>