<?php
/**
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Ffmedia\Administrator\Sanitizer\data;

\defined('JPATH_PLATFORM') or die;

/**
 * Interface TagInterface
 *
 * @package J4xdemos\Component\Ffmedia\Administrator\Sanitizer\tags
 */
interface TagInterface
{

    /**
     * Returns an array of tags
     *
     * @return array
     */
    public static function getTags();

}