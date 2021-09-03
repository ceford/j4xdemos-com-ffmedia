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

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use J4xdemos\Component\Ffmedia\Administrator\Helper\MimetypesHelper;

use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;

/**
 * Ffmedia View Model
 *
 * @since  4.0.0
 */
class FoldersModel extends ListModel
{
	/*
	 * Create hashes for all files in one folder using the catalogue
	 *
	 * $folder string relative to site root
	 * $media_type string either 'images' or 'files'
	 *
	 * return a list of catalogued files
	 */
	public function getHashes($media_type, $folder)
	{
		if (empty($folder))
		{
			return array ('That', 'went', 'wrong');
		}

		$table = '#__ffmedia';

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// does the record exist
		$query->select('*');
		$query->from($table);

		$query->where('folder_path = ' . $db->quote($folder));
		$db->setQuery($query);
		$items = $db->loadObjectList();

		$new = 0;
		$old = 0;
		$dud = 0;

		foreach ($items as $item)
		{
			$file = JPATH_SITE . $item->folder_path . '/' . $item->file_name;

			if (file_exists($file))
			{
				$hash = hash('md5', $file);
			}
			else
			{
				$hash = '';
			}
			if (!empty($hash))
			{
				// has the hash changed
				if ($hash == $item->hash)
				{
					$old++;
				}
				else
				{
					$query = $db->getQuery(true);
					$query->update($table);
					$query->set('hash = ' . $db->quote($hash));
					$query->where('id = ' . $item->id);
					$db->setquery($query);
					$result = $db->execute();
					if (!empty($result))
					{
						$new++;
					}
				}
			}
			else
			{
				$dud++;
			}
		}
		return array('Folder = ' . $folder, ' New = ' . $new, ' Old = ' . $old . ', Missing = ' . $dud);
	}


	/*
	 * Get a list of all files in one folder and catalogue them
	 *
	 * $folder string relative to site root
	 * $media_type string either 'images' or 'files'
	 *
	 * return a list of catalogued files
	 */
	public function getFiles($media_type, $folder)
	{
		if (empty($folder))
		{
			return array ('That', 'went', 'wrong');
		}
		$path = JPATH_SITE . $folder;

		$folders[] = '/' . $folder;
		$params = ComponentHelper::getParams('com_ffmedia');
		$mh = new MimetypesHelper;

		$items = scandir($path);

		$new = 0;
		$old = 0;
		$dud = 0;

		foreach ($items as $item)
		{
			// skip directories
			if (is_dir($item))
			{
				continue;
			}
			// skip hidden files
			if (strpos($item, '.') == 0)
			{
				continue;
			}
			// get mime type
			$mime = $mh->getFileMimeType($path . '/' . $item);
			// is it an allowed type
			if (empty($mh->checkInAllowedExtensions($mime, $params, $media_type)))
			{
				continue;
			}

			// save data as image or file
			$result = $this->saveData($media_type, $folder, $item);

			if ($result == 'new')
			{
				$new++;
			}
			else if ($result == 'old')
			{
				$old++;
			}
			else
			{
				$dud++;
			}
		}

		return array('Folder = ' . $folder, ' New = ' . $new, ' Old = ' . $old, ' Dud = ' . $dud);
	}

	/*
	 *
	 */
	protected function saveData($media_type, $folder, $filename)
	{
		$root = JPATH_SITE;

		if ($media_type == 'image')
		{
			$mh = new MimetypesHelper;
			$mime = $mh->getFileMimeType($root . $folder . '/' . $filename);
			// is it an svg
			if ($mime != 'image/svg+xml')
			{
				list($width, $height, $type, $wandhstring) = getimagesize($root. $folder. '/' . $filename);
			}
			else
			{
				$width = 100;
				$height = 100;
			}
		}
		else
		{
			$width = 0;
			$height = 0;
		}

		// the size of the file
		$size = filesize($root . $folder . '/' . $filename);

		// get the file name and extension
		$extension = substr($filename, strrpos($filename, '.') + 1);

		// takes 5x longer to hash - needs time saving ploy if file has not changed
		//$hash = hash('md5', $file);

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// does the record exist
		$query->select('id');
		$query->from('#__ffmedia');
		$query->where('media_type = '. $db->quote($media_type));
		$query->where('folder_path = ' . $db->quote($folder));
		$query->where('file_name = ' . $db->quote($filename));
		$db->setQuery($query);
		$id = $db->loadResult();
		$query = $db->getQuery(true);
		if ($id)
		{
			$query->update('#__ffmedia');
			$query->where('id = ' . $id);
			$result = 'old';
		}
		else
		{
			$query->insert('#__ffmedia');
			$query->set("media_type = 'image'");
			$result = 'new';
		}
		$query->set('folder_path = ' . $db->quote($folder));
		$query->set('file_name = ' . $db->quote($filename));
		$query->set('extension = ' . $db->quote($extension));
		$query->set('width = ' . $width);
		$query->set('height = ' . $height);
		$query->set('size = ' . $size);
		//$query->set('hash = ' . $db->quote($hash));

		$db->setQuery($query);
		try
		{
			$db->execute();
		}
		catch (\RuntimeException $e)
		{
			// skip this one
			return 'dud';
		}
		return $result;
	}

	/*
	 * Get a list all folders from the images or files root
	 *
	 * @param string $folder Start folder
	 *
	 * @return A sorted array of folders
	 *
	 * @since   4.0
	 */
	public function getFolders($folder)
	{
		// if folder is empty - abort
		if (empty($folder))
		{
			return array ('That', 'went', 'wrong');
		}
		$path = JPATH_SITE . $folder;
		$root = JPATH_SITE;
		$rootlen = strlen($root);
		$folders[] = $folder;
		$params = ComponentHelper::getParams('com_ffmedia');
		$prefix = $params->get('thumbnail_prefix');

		try
		{
			$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path),
				RecursiveIteratorIterator::SELF_FIRST);
			foreach($objects as $name => $object)
			{
				if (!is_dir($name))
				{
					continue;
				}
				// skip if any path element begins with .
				if (strpos($name, '/.') > 0)
				{
					continue;
				}
				// skip if a thumbnail folder
				if (strpos($name, $prefix) === 0) {
					continue;
				}
				$folders[] = substr($name, $rootlen);
			}
			sort($folders);
		}
		catch (\Exception $e)
		{
			Factory::getApplication()->enqueueMessage('Bad path = ' . $path, 'warning');
		}
		return $folders;
	}
}
