<?php
/**
 * @package     Ffmedia.Site
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E Ford
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Ffmedia\Administrator\Helper\FfmediaHelper;
use Joomla\Component\Ffmedia\Site\Helper\RouteHelper;

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$prefix = $this->params->get('thumbnail_prefix');
$fileBaseUrl = Uri::root(true);

?>

<div class="com-ffmedia-category__items">
	<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">

		<?php if ($this->params->get('filter_field')) : ?>
			<div class="com-ffmedia-category__filter btn-group">
				<label class="filter-search-lbl visually-hidden" for="filter-search">
					<?php echo Text::_('COM_FFMEDIA_FILTER_SEARCH_DESC'); ?>
				</label>
				<input
					type="text"
					name="filter-search"
					id="filter-search"
					value="<?php echo $this->escape($this->state->get('list.filter')); ?>"
					class="inputbox" onchange="document.adminForm.submit();"
					placeholder="<?php echo Text::_('COM_FFMEDIA_FILTER_SEARCH_DESC'); ?>"
				>
				<button type="submit" name="filter_submit" class="btn btn-primary"><?php echo Text::_('JGLOBAL_FILTER_BUTTON'); ?></button>
				<button type="reset" name="filter-clear-button" class="btn btn-secondary"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
		<?php endif; ?>

		<?php if ($this->params->get('show_pagination_limit')) : ?>
			<div class="com-ffmedia-category__pagination btn-group float-end">
				<label for="limit" class="visually-hidden">
					<?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?>
				</label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		<?php endif; ?>

		<?php if (empty($this->items)) : ?>
			<?php if ($this->params->get('show_no_ffmedias', 1)) : ?>
				<div class="alert alert-info">
					<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
					<?php echo Text::_('COM_FFMEDIA_NO_FFMEDIAS'); ?>
				</div>
			<?php endif; ?>

		<?php else : ?>
			<div class="row">
			<?php foreach ($this->items as $i => $item) :
				$zoomurl = $item->folder_path . '/' . $item->file_name;
				$tn = $item->folder_path . '/' . $prefix . $item->tn_width . '/' . $item->file_name;
				if (file_exists(JPATH_SITE . $tn))
				{
					$imageurl = $fileBaseUrl . $tn;
				}
				else
				{
					$imageurl = $fileBaseUrl . $zoomurl;
				}
			?>
			<div class="col-12 col-sm-6 col-md-4 mb-3">
				<div class="d-flex align-items-center">
					<div class="flex-shrink-0 preview img-preview cursor-zoom"
						style="max-width: 100px; cursor: zoom-in;"
						data-url="<?php echo $zoomurl;?>" data-alt="<?php echo $item->alt; ?>">
						<img src="<?php echo $imageurl; ?>" alt="<?php echo $item->alt; ?>" />
					</div>
					<div class="flex-grow-1 ms-3">
						<?php echo $item->caption; ?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ($this->params->get('show_pagination', 2)) : ?>
			<div class="com-ffmedia-category__counter w-100">
				<?php if ($this->params->def('show_pagination_results', 1)) : ?>
					<p class="com-ffmedia-category__counter counter float-end pt-3 pe-2">
						<?php echo $this->pagination->getPagesCounter(); ?>
					</p>
				<?php endif; ?>

				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif; ?>
		<div>
			<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->state->get('list.ordering')); ?>">
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->state->get('list.direction')); ?>">
		</div>
	</form>
</div>
