<?php
/**
 * @package     Ffmedia.Administrator
 * @subpackage  com_ffmedia
 *
 * @copyright   (C) 2021 Clifford E. Ford <https://www.fford.me.uk>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$activepath = $this->options->get('activepath');
$folders = $this->options->get('folders');

foreach ($folders as $folder)
{
	// make an array
	$members = explode('/', substr($folder, 1));
	$space = count($members) -1;
	$active = ($folder == $activepath) ? ' active' : '';
	echo '<div class="cat-folder indent-' . $space . $active . '" data-link="'. $folder .'">' . "\n";
	if (!$active)
	{
		echo '<span class="icon-folder"></span> ' . "\n";
		echo '<a href="#">' . "\n";
	}
	else
	{
		echo '<span class="icon-folder-open"></span> ' . "\n";
	}
	echo array_pop($members);
	if (!$active)
	{
		echo '</a>' . "\n";
	}
	echo '</div>' . "\n";
}
