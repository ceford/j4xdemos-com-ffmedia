<?php
/**
 * @package     Ffmedia.Administrator
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E. Ford <https://www.fford.me.uk>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Ffmedia\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use J4xdemos\Component\Ffmedia\Administrator\Helper\MimetypesHelper;
use J4xdemos\Component\Ffmedia\Administrator\Sanitizer\Sanitizer;

/**
 * Item Model for a single image.
 *
 * @since  1.6
 */

class ImageModel extends AdminModel
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_FFMEDIA';

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
		$form = $this->loadForm('com_ffmedia.image', 'image', array('control' => 'jform', 'load_data' => $loadData));

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
	public function publish(&$pks, $value = 1)
	{
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
		$savedFile = $this->saveImage($data);
		if (empty($savedFile))
		{
			return false;
		}

		return parent::save($data);
	}

	/**
	 * Method to save the image data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   4.0
	 */
	protected function saveImage(&$data)
	{
		$app = Factory::getApplication();
		// need to use raw because anything else causes an error with an empty file name
		$file = $app->input->files->get('jform', array(), 'array');
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

		$activePath = $app->getUserState('com_ffmedia.images.activepath');
		$new_path = JPATH_SITE . $activePath . '/' . $data['file_name'];

		// check that we are not overwriting an existing file with a new file
		if (!isset($data['id']) && File::exists($new_path))
		{
			$app->enqueueMessage(Text::_('COM_FFMEDIA_ERROR_FILE_EXISTS'), 'error');
			return false;
		}

		$params = ComponentHelper::getParams('com_ffmedia');

		// check size
		if ($file['uploadfile']['size'] > ($params->get('image_upload_maxsize')*1024*1024))
		{
			$app->enqueueMessage(Text::_('COM_FFMEDIA_ERROR_WARNFILETOOLARGE'), 'error');
			File::delete($file['uploadfile']['tmp_name']);
			return false;
		}

		$mime = $file['uploadfile']['type'];

		// check that mimtype has an extension in the allowed list
		$mimeHelper = new MimetypesHelper;
		$allowed = $mimeHelper->checkInAllowedExtensions($mime, $params, 'image');

		if (empty($allowed))
		{
			$app->enqueueMessage(Text::_('COM_FFMEDIA_ERROR_NOT_AN_ALLOWED_TYPE'), 'error');
			File::delete($file['uploadfile']['tmp_name']);
			return false;
		}

		//ToDo check that the uploaded file has an extension good for the mimetype

		$tmp_name = $file['uploadfile']['tmp_name'];

		// if this is an svg - sanitize it
		if ($mime == 'image/svg+xml') {
			// Create a new sanitizer instance
			$sanitizer = new Sanitizer();

			// Load the dirty svg
			$dirtySVG = file_get_contents($tmp_name);

			// Pass it to the sanitizer and get it back clean
			file_put_contents($tmp_name, $sanitizer->sanitize($dirtySVG));
		}

		// copy because /tmp may be owned by root/wheel
		if (!File::copy($tmp_name, $new_path))
		{
			$app->enqueueMessage(Text::_('COM_FFMEDIA_ERROR_NOT_UPLOADED'), 'error');
			File::delete($tmp_name);
			return false;
		}

		if ($mime != 'image/svg+xml')
		{
			list ($width, $height, $type, $wandhstring) = getimagesize($new_path);
			$data['width'] = $width;
			$data['height'] = $height;
		}
		else{
			$data['width'] = 100;
			$data['height'] = 100;
		}

		if (!empty($data['tn_width']) && ($width > $data['tn_width']))
		{
			$prefix = $params->get('thumbnail_prefix');
			$thumb_path = JPATH_SITE . $activePath . '/' . $prefix . $data['tn_width'];
			// make the path if it does not exist
			if (!file_exists($thumb_path))
			{
				mkdir($thumb_path, 0777, true);
			}
			// make the thumbnail

			switch ($mime) {
				case 'image/jpeg':
					$thumb = imagecreatefromjpeg($tmp_name);
					$thumb = imagescale($thumb, $data['tn_width']);
					imagejpeg($thumb, $thumb_path . '/' . $data['file_name']);
					break;
				case 'image/png':
					$img = imagecreatefrompng($tmp_name);

					$newWidth = $data['tn_width'];
					$newHeight = intval($height / $width * $newWidth);

					$thumb = imagecreatetruecolor($newWidth, $newHeight);
					imagealphablending($thumb, false);

					$transparency = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
					imagefill($thumb, 0, 0, $transparency);

					imagecolortransparent($thumb, $transparency);

					imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
					imagesavealpha($thumb,true);
					imagepng($thumb, $thumb_path . '/' . $data['file_name']);
					break;
				case 'image/gif':
					$img = imagecreatefromgif($tmp_name);

					$newWidth = $data['tn_width'];
					$newHeight = intval($height / $width * $newWidth);

					$thumb = imagecreatetruecolor($newWidth, $newHeight);
					imagealphablending($thumb, false);

					# get and reallocate transparency-color
					$transindex = imagecolortransparent($img);

					if ($transindex >= 0) {
						$transcol = imagecolorsforindex($img, $transindex);
						$transindex = imagecolorallocatealpha(
								$thumb,
								$transcol['red'],
								$transcol['green'],
								$transcol['blue'],
								127
								);
						imagefill($thumb, 0, 0, $transindex);
					}
					# resample
					imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

					# restore transparency
					if ($transindex >= 0) {
						imagecolortransparent($thumb, $transindex);
						for ($y = 0; $y < $newHeight; ++$y) {
							for ($x = 0; $x < $newWidth; ++$x) {
								if (((imagecolorat($thumb, $x, $y) >> 24) & 0x7F) >= 100) {
									imagesetpixel(
											$thumb,
											$x,
											$y,
											$transindex
											);
								}
							}
						}

					}
					# save GIF
					imagetruecolortopalette($thumb, true, 255);
					imagesavealpha($thumb, false);
					imagegif($thumb, $thumb_path . '/' . $data['file_name']);
					break;
				case 'image/webp':
					$thumb = imagecreatefromwebp($tmp_name);
					$thumb = imagescale($thumb, $data['tn_width']);
					imagewebp($thumb, $thumb_path . '/' . $data['file_name']);
					break;
				default:
					$thumb = '';
			}
			if (!empty($thumb))
			{
				$app->enqueueMessage('Thumbnail image created: ' . $data['tn_width'], 'info');
			}
			else
			{
				$app->enqueueMessage('Thumbnail image NOT created', 'info');
			}
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
}