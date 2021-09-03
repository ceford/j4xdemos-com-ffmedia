<?php
/**
 * @package     Ffmedia.Administrator
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E. Ford <https://www.fford.me.uk>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Ffmedia\Administrator\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Dispatcher\ComponentDispatcher;

/**
 * ComponentDispatcher class for com_ffmedia
 *
 * @since  4.0.0
 */
class Dispatcher extends ComponentDispatcher
{
	/**
	 * Method to check component access permission
	 *
	 * @since   4.0.0
	 *
	 * @return  void
	 */
	protected function checkAccess()
	{
		$user   = $this->app->getIdentity();
		$asset  = $this->input->get('asset');
		$author = $this->input->get('author');

		// Access check
		if (!$user->authorise('core.manage', 'com_ffmedia')
			&& (!$asset || (!$user->authorise('core.edit', $asset)
			&& !$user->authorise('core.create', $asset)
			&& count($user->getAuthorisedCategories($asset, 'core.create')) == 0)
			&& !($user->id == $author && $user->authorise('core.edit.own', $asset))))
		{
			throw new NotAllowed($this->app->getLanguage()->_('JERROR_ALERTNOAUTHOR'), 403);
		}
	}
}
