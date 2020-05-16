<?php


namespace Creonit\MediaBundle\VideoHandler;


use Creonit\MediaBundle\Model\Video;

interface VideoHandlerInterface
{
    /**
     * @param $source1
     * @param $source2
     * @return bool
     */
    public function equals($source1, $source2);

    /**
     * @param $source
     * @return Video
     */
    public function createVideo($source);

    /**
     * @param $source
     * @return VideoData
     */
    public function getVideoData($source);

    /**
     * @param $source
     * @return bool
     */
    public function supports($source);
}