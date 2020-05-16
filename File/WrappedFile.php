<?php


namespace Creonit\MediaBundle\File;


class WrappedFile extends \Symfony\Component\HttpFoundation\File\File
{
    /** @var string */
    protected $originalName;

    /**
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * @param string $originalName
     * @return WrappedFile
     */
    public function setOriginalName(string $originalName)
    {
        $this->originalName = $originalName;
        return $this;
    }
}