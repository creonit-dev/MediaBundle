<?php


namespace Creonit\MediaBundle\Admin\Event;


use Creonit\MediaBundle\Admin\Field\FileField;
use Creonit\MediaBundle\Model\File;
use Symfony\Contracts\EventDispatcher\Event;

class AfterSaveFileEvent extends Event
{
    /**
     * @var File
     */
    protected $file;

    /**
     * @var FileField
     */
    private $fileField;

    public function __construct(File $file, FileField $fileField)
    {
        $this->file = $file;
        $this->fileField = $fileField;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     * @return AfterSaveFileEvent
     */
    public function setFile(File $file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return FileField
     */
    public function getFileField()
    {
        return $this->fileField;
    }

    /**
     * @param FileField $fileField
     * @return AfterSaveFileEvent
     */
    public function setFileField(FileField $fileField)
    {
        $this->fileField = $fileField;
        return $this;
    }
}