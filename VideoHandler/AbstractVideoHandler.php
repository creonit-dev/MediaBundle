<?php


namespace Creonit\MediaBundle\VideoHandler;


use Creonit\MediaBundle\Model\File;
use Creonit\MediaBundle\Model\Video;

abstract class AbstractVideoHandler implements VideoHandlerInterface
{
    /**
     * @param $source1
     * @param $source2
     * @return bool
     */
    public function equals($source1, $source2)
    {
        return $this->normalizeSource($source1) === $this->normalizeSource($source2);
    }

    protected function normalizeSource($source)
    {
        if ($source instanceof Video) {
            return $this->normalizeSource($source->getFile() ?: $source->getSource());
        }

        return $source;
    }

    /**
     * @param $source
     * @return Video
     */
    public function createVideo($source)
    {
        $video = new Video();

        if ($source instanceof File) {
            $video->setFile($source);

        } else if (is_string($source)) {
            $video->setSource($source);
        }

        return $video;
    }
}