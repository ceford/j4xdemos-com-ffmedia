<?php
/**
 * @package     Ffmedia.Administrator
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E. Ford <https://www.fford.me.uk>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Ffmedia\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;
use Joomla\Database\ParameterType;

/**
 * Methods supporting a list of image records.
 *
 * @since  4.0
 */
class ImagesModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'state', 'a.state',
				'file_name', 'a.file_name',
				'extension', 'a.extension',
				'depth',
				'date_created', 'a.date_created',
				'size', 'a.size',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__ffmedia AS a');

		$state = $this->getState('filter.state');
		if (!empty($state))
		{
			$query->where('a.state = ' . $state);
		}
		else
		{
			$query->where('a.state = 1');
		}

		$current = $this->getState('filter.activepath');
		$depth = $this->getState('filter.depth');
		if (empty($depth) || $depth == 'tree')
		{
			$query->where('folder_path LIKE ' . $db->quote($current . '%'));
		}
		else
		{
			$query->where('folder_path = ' . $db->quote($current));
		}

		$extension = $this->getState('filter.extension');
		if (!empty($extension))
		{
			$query->where('extension = ' . $db->quote($extension));
		}

		// Filter by search in title
		if ($search = $this->getState('filter.search'))
		{
			if (stripos($search, 'id:') === 0)
			{
				$search = (int) substr($search, 3);
				$query->where($db->quoteName('a.id') . ' = :search')
				->bind(':search', $search, ParameterType::INTEGER);
			}
			else
			{
				$search = '%' . str_replace(' ', '%', trim($search)) . '%';
				$query->where('(' . $db->quoteName('a.file_name') . ' LIKE :search1 OR ' .
					$db->quoteName('a.alt') . ' LIKE :search2 OR ' .
					$db->quoteName('a.caption') . ' LIKE :search3)')
					->bind([':search1', ':search2', ':search3'], $search);
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'DESC');
		$ordering = $db->escape($orderCol) . ' ' . $db->escape($orderDirn);
		$query->order($ordering);

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.depth');
		$id .= ':' . $this->getState('filter.extension');

		return parent::getStoreId($id);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Ffmedia', $prefix = 'Administrator', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = 'a.id', $direction = 'desc')
	{
		// Load the parameters.
		//$this->setState('params', ComponentHelper::getParams('com_ffmedia'));

		//$state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '1');
		//$this->setState('filter.state', $state);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$activepath = $this->getUserStateFromRequest($this->context . '.filter.activepath', 'filter_activepath', '/images');
		$this->setState('filter.activepath', $activepath);
		Factory::getApplication()->setUserState('com_ffmedia.images.activepath', $activepath);

		$depth = $this->getUserStateFromRequest($this->context . '.filter.depth', 'filter_depth', '');
		$this->setState('filter.depth', $depth);

		$extension = $this->getUserStateFromRequest($this->context . '.filter.extension', 'filter_extension', '');
		$this->setState('filter.extension', $extension);

		// List state information.
		parent::populateState($ordering, $direction);
	}
}
