<?php
/**
 * @package     Ffmedia.Administrator
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E. Ford <https://www.fford.me.uk>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Ffmedia\Administrator\View\Folders;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Folders List View
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Holds a list of providers
	 *
	 * @var array|string
	 *
	 * @since   4.0.0
	 */
	protected $providers = null;

	/**
	 * The current path of the media manager
	 *
	 * @var string
	 *
	 * @since 4.0.0
	 */
	protected $currentPath;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse;
	 *                        automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   4.0.0
	 */
	public function display($tpl = null)
	{
		$model         = $this->getModel();

		$app = Factory::getApplication();
		$params = ComponentHelper::getParams('com_ffmedia');

		$filter = $app->input->get('filter', '', 'array');
		$old_mediatype = $app->getUserState('com_ffmedia.folders.filter.mediatype');
		$old_activepath = $app->getUserState('com_ffmedia.folders.filter.activepath');

		if (empty($filter))
		{
			if (empty($old_mediatype))
			{
				$this->activepath = '/' . $params->get('image_path');
				$this->mediatype = 'image';
			}
			else
			{
				$this->activepath = $app->getUserState('com_ffmedia.folders.filter.activepath');
				$this->mediatype = $app->getUserState('com_ffmedia.folders.filter.mediatype');
			}
		}
		else
		{
			// has the media type changed
			if ($filter['mediatype'] != $old_mediatype)
			{
				if ($filter['mediatype'] == 'image')
				{
					$this->activepath = '/' . $params->get('image_path');
				}
				else
				{
					$this->activepath = '/' . $params->get('file_path');
				}
				$this->mediatype = $filter['mediatype'];
			}
			else
			{
				$this->activepath = $filter['activepath'];
				$this->mediatype = $filter['mediatype'];
			}
		}
		$app->setUserState('com_ffmedia.folders.filter.activepath', $this->activepath);
		$app->setUserState('com_ffmedia.folders.filter.mediatype', $this->mediatype);

		$this->folders = $model->getFolders($this->activepath);
		$this->prepareToolbar();
		//$this->currentPath = Factory::getApplication()->input->getString('path');

		parent::display($tpl);
	}

	/**
	 * Prepare the toolbar.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function prepareToolbar()
	{
		$tmpl = Factory::getApplication()->input->getCmd('tmpl');

		// Get the toolbar object instance
		$toolbar  = Toolbar::getInstance('toolbar');
		$user = Factory::getUser();

		// Set the title
		ToolbarHelper::title(Text::_('COM_FFMEDIA_TITLE_BAR_FOLDERS'), 'folder ffmedia');


		$dropdown = $toolbar->dropdownButton('status-group')
		->text('JTOOLBAR_CHANGE_STATUS')
		->toggleSplit(false)
		->icon('icon-ellipsis-h')
		->buttonClass('btn btn-action');

		$childBar = $dropdown->getChildToolbar();

		$layout = new FileLayout('toolbar.index-all', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');
		$childBar->appendButton('Custom', $layout->render([]), 'archive');

		$layout = new FileLayout('toolbar.index-one', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');
		$childBar->appendButton('Custom', $layout->render([]), 'archive');

		$layout = new FileLayout('toolbar.hash-all', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');
		$childBar->appendButton('Custom', $layout->render([]), 'hashtag');

		$layout = new FileLayout('toolbar.hash-one', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');
		$childBar->appendButton('Custom', $layout->render([]), 'hashtag');

		if ($user->authorise('core.create', 'com_ffmedia'))
		{
			$dropdown = $toolbar->dropdownButton('folders')
			->text('COM_FFMEDIA_FOLDERS')
			->toggleSplit(false)
			->icon('icon-folder')
			->buttonClass('btn btn-action');

			$childBar = $dropdown->getChildToolbar();

			// Add the create folder button
			$layout = new FileLayout('toolbar.create-folder', JPATH_COMPONENT_ADMINISTRATOR . '/layouts', array('view' => 'folders'));
			$childBar->appendButton('Custom', $layout->render([]), 'icon-folder-plus');

			$layout = new FileLayout('toolbar.delete-if-empty', JPATH_COMPONENT_ADMINISTRATOR . '/layouts', array('view' => 'folders'));
			$childBar->appendButton('Custom', $layout->render([]), 'icon-folder-minus');
		}

		// Add the preferences button
		if (($user->authorise('core.admin', 'com_ffmedia') || $user->authorise('core.options', 'com_ffmedia')) && $tmpl !== 'component')
		{
			ToolbarHelper::preferences('com_ffmedia');
			ToolbarHelper::divider();
		}

		if ($tmpl !== 'component')
		{
			ToolbarHelper::help('folders', true);
		}
	}
}
