<?php

namespace Creonit\MediaBundle\Model;

use Creonit\MediaBundle\Model\Base\File as BaseFile;

/**
 * Skeleton subclass for representing a row from the 'file' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class File extends BaseFile
{
    public function getUrl()
    {
        return '/' . ltrim($this->path, '/') . '/' . $this->name;
    }
}
