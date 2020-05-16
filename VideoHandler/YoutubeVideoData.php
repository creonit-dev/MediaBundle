<?php


namespace Creonit\MediaBundle\VideoHandler;


class YoutubeVideoData extends VideoData
{
    protected $code;

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     * @return YoutubeVideoData
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
}