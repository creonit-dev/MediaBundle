<?php


namespace Creonit\MediaBundle\VideoHandler;


class VideoData
{
    /**
     * @var string
     */
    protected $image_url;

    /**
     * @var string
     */
    protected $embed_url;

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * @param mixed $image_url
     * @return VideoData
     */
    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmbedUrl()
    {
        return $this->embed_url;
    }

    /**
     * @param mixed $embed_url
     * @return VideoData
     */
    public function setEmbedUrl($embed_url)
    {
        $this->embed_url = $embed_url;
        return $this;
    }
}