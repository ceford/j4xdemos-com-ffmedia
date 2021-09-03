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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;
use J4xdemos\Component\Ffmedia\Administrator\Helper\FolderHelper;
use J4xdemos\Component\Ffmedia\Administrator\Helper\JsHelper;

$params = ComponentHelper::getParams('com_ffmedia');
$fileBaseUrl = Uri::root(true);

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_ffmedia.ffmedia')
	->registerAndUseStyle('com_ffmedia.file-icon-vectors', 'media/com_ffmedia/css/file-icon-vectors.min.css')
	->useScript('com_ffmedia.ffmedia');

// Populate the language
JsHelper::getJstext();

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));

?>

<form action="<?php echo Route::_('index.php?option=com_ffmedia&view=files'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php
				// Search tools bar
				echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
				?>
				<div class="row">

					<div class="col-12 col-md-3">

					<h3><?php echo Text::_('COM_FFMEDIA_FOLDER_TREE'); ?></h3>

					<?php
						$folders = FolderHelper::getTree($this->state->get('filter.activepath'));
						$layout = new FileLayout('foldertree', JPATH_COMPONENT .'/layouts', array('activepath' => $this->state->get('filter.activepath'),'folders' => $folders));
						echo $layout->render();
					?>
					</div>

					<div class="col-12 col-md-9">
					<?php if (empty($this->items)) : ?>
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
					<?php else : ?>
					<table class="table" id="articleList">
						<caption id="captionTable" class="sr-only">
							<?php echo Text::_('COM_FFMEDIA_FILES_TABLE_CAPTION'); ?>,
							<span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
							<span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
						</caption>
						<thead>
							<tr>
								<td class="preview"><?php echo Text::_('COM_FFMEDIA_ICON'); ?></td>
								<td>
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_FFMEDIA_MEDIA_NAME', 'a.file_name', $listDirn, $listOrder); ?>
								</td>
								<td>
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_FFMEDIA_MEDIA_EXTENSION', 'a.extension', $listDirn, $listOrder); ?>
								</td>
								<td>
									<?php
										if ($this->state->get('filter.state') == -2)
										{
											echo HTMLHelper::_('searchtools.sort', 'COM_FFMEDIA_MEDIA_DATE_TRASHED', 'a.date_trashed', $listDirn, $listOrder);
										}
										else if ($this->state->get('filter.state') == -3)
										{
											echo HTMLHelper::_('searchtools.sort', 'COM_FFMEDIA_MEDIA_DATE_DELETED', 'a.date_deleted', $listDirn, $listOrder);
										}
										else
										{
											echo HTMLHelper::_('searchtools.sort', 'COM_FFMEDIA_MEDIA_DATE_CREATED', 'a.date_created', $listDirn, $listOrder);
										}
									?>
								</td>
								<td>
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_FFMEDIA_MEDIA_SIZE', 'a.size', $listDirn, $listOrder); ?>
								</td>
								<th scope="col" class="w-5 d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->items as $i => $item) :
								$imageurl = substr($item->folder_path, 1) . '/' . $item->file_name;
							?>
								<tr>
									<td rowspan="2" class="preview">
										<?php if ($this->state->get('filter.state') == -3) : ?>
											<span class="fiv-cla fiv-icon-<?php echo $item->extension; ?>"></span>
										<?php elseif ($this->state->get('filter.state') == -2) : ?>
										<a href="<?php echo $fileBaseUrl . '/' . $params->get('trash_path') . $item->folder_path . '/' . $item->id . '-' . $item->file_name; ?>">
											<span class="fiv-cla fiv-icon-<?php echo $item->extension; ?>"></span>
										</a>
										<?php else : ?>
										<a href="<?php echo $fileBaseUrl . $item->folder_path . '/' . $item->file_name; ?>">
											<span class="fiv-cla fiv-icon-<?php echo $item->extension; ?>"></span>
										</a>
										<?php endif;?>
									</td>

									<td class="break-word">
										<?php if ($this->state->get('filter.state') < 0) : ?>
											<?php echo $item->file_name; ?>
										<?php else : ?>
										<a href="index.php?option=com_ffmedia&view=file&layout=edit&id=<?php echo $item->id; ?>">
											<?php echo $item->file_name; ?>
										</a>
										<?php endif;?>
									</td>

									<td>
										<?php echo $item->extension; ?>
									</td>

									<td>
									<?php
										if ($this->state->get('filter.state') == -2)
										{
											echo $item->date_trashed;
										}
										else if ($this->state->get('filter.state') == -3)
										{
											echo $item->date_deleted;
										}
										else
										{
											echo $item->date_created;
										}
									?>
									</td>

									<td>
										<?php echo $item->size; ?>
									</td>

									<td class="d-none d-md-table-cell">
										<?php echo $item->id; ?>
									</td>
								</tr>

								<tr>
									<td>
										<?php if ($this->state->get('filter.state') >= 0) : ?>
										<select id="actionlist_<?php echo $item->id; ?>" class="actionselect custom-select"
											data-url="<?php echo $imageurl; ?>">
											<option value=""><?php echo Text::_('COM_FFMEDIA_ACTIONS'); ?></option>
											<option value="share"><?php echo Text::_('COM_FFMEDIA_ACTIONS_SHARE_URL'); ?></option>
											<option value="link"><?php echo Text::_('COM_FFMEDIA_ACTIONS_LINK'); ?></option>
											<option value="trashfile"><?php echo Text::_('JTRASH'); ?></option>
										</select>
										<?php else : ?>
										<select id="actionlist_<?php echo $item->id; ?>" class="actionselect custom-select"
											data-url="">
											<option value=""><?php echo Text::_('COM_FFMEDIA_ACTIONS'); ?></option>
											<option value="restorefile"><?php echo Text::_('COM_FFMEDIA_ACTIONS_RESTORE'); ?></option>
											<option value="deletefile"><?php echo Text::_('COM_FFMEDIA_ACTIONS_DELETE'); ?></option>
										</select>
										<?php endif; ?>
									</td>
									<td id="alt-<?php echo $item->id; ?>" colspan="4">
										<?php echo Text::_('COM_FFMEDIA_FILE_UPLOAD_SHORT_DESCRIPTION_LABEL'); ?> = <span id="alt-<?php echo $item->id; ?>"><?php echo $item->alt; ?></span><br>
										<?php echo Text::_('COM_FFMEDIA_FILE_UPLOAD_LONG_DESCRIPTION_LABEL'); ?> = <span id="caption-<?php echo $item->id; ?>"><?php echo $item->caption; ?></span>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

					<?php // Load the pagination. ?>
					<?php echo $this->pagination->getListFooter(); ?>
					<?php endif; ?>
					</div>
				</div>


				<input type="hidden" name="jform[action_id]" id="jform_action_id" value="">
				<input type="hidden" name="jform[media_type]" id="jform_media_type" value="file">
				<input type="hidden" name="task" id="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<input type="hidden" name="filter[activepath]" id="filter_activepath" value="<?php echo $this->state->get('filter.activepath'); ?>">
				<input type="hidden" name="jform[newfoldername]" id="jform_newfoldername" value="">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>

<?php
$footer = '
	<button type="button" class="btn btn-secondary" bs-dismiss="modal">Close</button>
';
	echo HTMLHelper::_(
	'bootstrap.renderModal',
	'collapseModal',
	[
		'title' => Text::_('COM_FFMEDIA_IMAGE_ZOOM'),
		'footer' => $footer
	],
	'<div id="results"></div>'
	//$this->loadTemplate('batch_body')
); ?>
