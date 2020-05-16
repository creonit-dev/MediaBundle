<?php


namespace Creonit\MediaBundle\Exception;


class VideoSourceIsNotSupportedException extends \Exception
{
    protected $source;

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     * @return VideoSourceIsNotSupportedException
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }
}