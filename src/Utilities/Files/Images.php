<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Utilities\Files;

use Cake\Core\Configure;

/**
 * Images class to be injected into the XML export
 *
 */
class Images
{

    /**
     * Encode image
     *
     * Used in JobExport to export images.
     *
     * @param $filename
     * @param $size
     *
     * @return string
     * @throws \GmagickException
     */
    static public function base64Image($filename = '', $size = 300)
    {
        $folder = Configure::read('Data.currentdatabase');
        if (empty($folder)) {
            $folder = Configure::read('Data.databases');
        }

        $filename = $folder . DS . $filename;
        $thumbname = Files::getThumb($filename, $size, true);

        $data = file_get_contents($thumbname);
        $data = base64_encode($data);

        return $data;
    }

    /**
     * Encode URI
     *
     * Used in JobExport to export images.
     *
     * @param $filename
     * @param $size
     *
     * @return string
     * @throws \GmagickException
     */
    static public function base64URI($filename = '', $size = 300)
    {
        $folder = Configure::read('Data.currentdatabase');
        if (empty($folder)) {
            $folder = Configure::read('Data.databases');
        }
        $folder = rtrim($folder, DS);

        $filename = $folder . DS . $filename;
        $thumbname = Files::getThumb($filename, $size, true);

        $data = file_get_contents($thumbname);
        $type = pathinfo($thumbname, PATHINFO_EXTENSION);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return $base64;
    }

    /**
     * Test whether a file exists
     *
     * Used in JobExport.
     *
     * @param $filename
     * @return bool
     */
    static public function fileExists($filename = '')
    {
        $folder = Configure::read('Data.currentdatabase');
        if (empty($folder)) {
            $folder = Configure::read('Data.databases');
        }
        $filename = $folder . DS . $filename;

        return is_file($filename);
    }

    /**
     * Output the full path of a file in the current database folder
     *
     * Used in JobExport.
     *
     * @param $filename
     * @return string
     */
    static public function filePath($filename = '')
    {
        $folder = Configure::read('Data.currentdatabase');
        if (empty($folder)) {
            $folder = Configure::read('Data.databases');
        }
        return $folder . DS . $filename;
    }


}

