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
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use J4xdemos\Component\Ffmedia\Administrator\Helper\MimetypesHelper;

/**
 * File Model
 *
 * @since  4.0.0
 */
class FileModel extends AdminModel
{
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			return Factory::getUser()->authorise('core.delete', 'com_ffmedia.ffmedia.' . (int) $record->id);
		}

		return false;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = Factory::getUser();

		// Check for existing article.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', 'com_ffmedia.ffmedia.' . (int) $record->id);
		}

		// Default to component settings if neither article nor category known.
		return parent::canEditState($record);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean  A Form object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_ffmedia.file', 'file', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		return parent::getItem($pk);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = Factory::getApplication();
		$data = $app->getUserState('com_ffmedia.edit.ffmedia.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Pre-select some filters (Status, Category, Language, Access) in edit form if those have been selected in Article Manager: Articles
		}

		$this->preprocessData('com_ffmedia.ffmedia', $data);

		return $data;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   4.0.0
	 */
	public function publish(&$pks, $value = 1) {
		/* this is a very simple method to change the state of each item selected */
		$db = $this->getDbo();

		$query = $db->getQuery(true);

		$query->update('`#__ffmedia`');
		$query->set('state = ' . $value);
		$query->where('id IN (' . implode(',', $pks). ')');
		$db->setQuery($query);
		$db->execute();
	}
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		$savedFile = $this->saveFile($data);
		if (empty($savedFile))
		{
			return false;
		}

		return parent::save($data);
	}

	/**
	 * Method to save a file.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   4.0
	 */
	protected function saveFile(&$data)
	{
		$app = Factory::getApplication();
		$file = $app->input->files->get('jform', '', 'array');
		// array (size=1)
		// 'uploadfile' =>
		// array (size=5)
		// 'name' => string 'joomla-topmenu-test.png' (length=23)
		// 'type' => string 'image/png' (length=9)
		// 'tmp_name' => string '/private/var/tmp/phpVr8sUp' (length=26)
		// 'error' => int 0
		// 'size' => int 70637
		if (isset($data['id']) && empty($file['uploadfile']['name']))
		{
			// a file is not required if a record exists
			return true;
		}

		if (!isset($data['id']) && empty($file['uploadfile']['name']))
		{
			// a file is required but a file has not been selected
			$app->enqueueMessage(Text::_('COM_FFMEDIA_ERROR_FILE_NOT_SELECTED'), 'error');
			return false;
		}

		$activePath = $app->getUserState('com_ffmedia.files.activepath');
		$new_path = JPATH_SITE . $activePath . '/' . $data['file_name'];

		// check that we are not overwriting an existing file with a new file
		if (!isset($data['id']) && File::exists($new_path))
		{
			$app->enqueueMessage(Text::_('COM_FFMEDIA_ERROR_FILE_EXISTS'), 'error');
			return false;
		}

		$params = ComponentHelper::getParams('com_ffmedia');

		// check size
		if ($file['uploadfile']['size'] > ($params->get('file_upload_maxsize')*1024*1024))
		{
			$app->enqueueMessage(Text::_('COM_FFMEDIA_ERROR_WARNFILETOOLARGE'), 'error');
			File::delete($file['uploadfile']['tmp_name']);
			return false;
		}

		$mime = $file['uploadfile']['type'];

		// check that mimtype has an extension in the allowed list
		$mimeHelper = new MimetypesHelper;
		$allowed = $mimeHelper->checkInAllowedExtensions($mime, $params, 'file');

		if (empty($allowed))
		{
			$app->enqueueMessage(Text::_('COM_FFMEDIA_ERROR_NOT_AN_ALLOWED_TYPE'), 'error');
			File::delete($file['uploadfile']['tmp_name']);
			return false;
		}

		//ToDo check that the uploaded file has an extension good for the mimetype

		$tmp_name = $file['uploadfile']['tmp_name'];

		// copy because /tmp may be owned by root/wheel
		if (!File::copy($tmp_name, $new_path))
		{
			$app->enqueueMessage(Text::_('COM_FFMEDIA_ERROR_NOT_UPLOADED'), 'error');
			File::delete($tmp_name);
			return false;
		}
		File::delete($tmp_name);

		// add the file information to the data
		$data['folder_path'] = $activePath;

		$size = filesize($new_path);
		$hash = hash('md5', $new_path);
		$data['extension'] = substr($data['file_name'], strrpos($data['file_name'], '.') + 1);
		$data['size'] = $size;
		$data['hash'] = $hash;
		return true;
	}

	/**
	 * Method to get the file information for the given path. Path must be
	 * in the format: adapter:path/to/file.extension
	 *
	 * @param   string  $path  The path to get the information from.
	 *
	 * @return  \stdClass  An object with file information
	 *
	 * @since   4.0.0
	 * @see     ApiModel::getFile()
	 *
	 * Not being used!
	 */
	public function getFileInformation($path)
	{
		list($adapter, $path) = explode(':', $path, 2);

		return $this->bootComponent('com_ffmedia')->getMVCFactory()->createModel('Api', 'Administrator')
			->getFile($adapter, $path, ['url' => true, 'content' => true]);
	}
}
