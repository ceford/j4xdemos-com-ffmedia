<?php
/**
 * @package     Ffmedia.Administrator
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E. Ford <https://www.fford.me.uk>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Ffmedia\Administrator\View\Files;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;

/**
 * View class for a list of ffmedia files.
 *
 * @since  4.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * The search tools form
	 *
	 * @var    Form
	 * @since  1.6
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var    array
	 * @since  1.6
	 */
	public $activeFilters = [];

	/**
	 * Category data
	 *
	 * @var    array
	 * @since  1.6
	 */
	protected $categories = [];

	/**
	 * An array of items
	 *
	 * @var    array
	 * @since  1.6
	 */
	protected $items = [];

	/**
	 * The pagination object
	 *
	 * @var    Pagination
	 * @since  1.6
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var    Registry
	 * @since  1.6
	 */
	protected $state;

	/**
	 * The media tree
	 *
	 * @var    Array
	 * @since  4.0
	 */
	protected $tree;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  void
	 *
	 * @since   1.6
	 * @throws  Exception
	 */
	public function display($tpl = null): void
	{
		/** @var FfmediaModel $model */
		$model               = $this->getModel();
		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->state         = $model->getState();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar(): void
	{
		$tmpl = Factory::getApplication()->input->getCmd('tmpl');

		//$canDo = ContentHelper::getActions('com_ffmedia');
		$user  = Factory::getUser();

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		ToolbarHelper::title(Text::_('COM_FFMEDIA_FILES'), 'file ffmedia');

		$toolbar->addNew('file.add');

		if ($user->authorise('core.create', 'com_ffmedia'))
		{
			$dropdown = $toolbar->dropdownButton('folders')
			->text('COM_FFMEDIA_FOLDERS')
			->toggleSplit(false)
			->icon('icon-folder')
			->buttonClass('btn btn-action');

			$childBar = $dropdown->getChildToolbar();

			// Add the create folder button
			$layout = new FileLayout('toolbar.create-folder', JPATH_COMPONENT_ADMINISTRATOR . '/layouts', array('view' => 'files'));
			$childBar->appendButton('Custom', $layout->render([]), 'icon-folder-plus');

			$layout = new FileLayout('toolbar.delete-if-empty', JPATH_COMPONENT_ADMINISTRATOR . '/layouts', array('view' => 'files'));
			$childBar->appendButton('Custom', $layout->render([]), 'icon-folder-minus');
		}

		$nRecords = $this->pagination->total;
		$layout = new FileLayout('toolbar.nrecords', JPATH_COMPONENT_ADMINISTRATOR . '/layouts', array('nrecords' => $nRecords));
		$toolbar->appendButton('Custom', $layout->render([]), 'icon-info');

		if ($user->authorise('core.admin', 'com_ffmedia') || $user->authorise('core.options', 'com_ffmedia'))
		{
			$toolbar->preferences('com_ffmedia');
		}

		if ($tmpl !== 'component')
		{
			ToolbarHelper::help('files', true);
		}
	}
}
