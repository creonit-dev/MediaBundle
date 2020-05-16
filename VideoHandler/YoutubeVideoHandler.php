<?php


namespace Creonit\MediaBundle\VideoHandler;


class YoutubeVideoHandler extends AbstractVideoHandler
{

    /**
     * @param $source
     * @return VideoData
     */
    public function getVideoData($source)
    {
        $videoData = new YoutubeVideoData();

        if (!preg_match('/(?:\?v=([\w\d_-]+)|.be\/([\w\d_-]+))/i', $this->normalizeSource($source), $match)) {
            return $videoData;
        }

        $code = $match[1] ?: $match[2];

        $videoData
            ->setCode($code)
            ->setEmbedUrl('//www.youtube.com/embed/' . $code)
            ->setImageUrl(sprintf('//img.youtube.com/vi/%s/0.jpg', $code));

        return $videoData;
    }

    /**
     * @param $source
     * @return bool
     */
    public function supports($source)
    {
        return (bool)preg_match('#^(https?://)?(www\.)?(youtu\.be|youtube\.com)/(watch\?v=[\w\d_-]+|[\w\d_-]+)$#i', $this->normalizeSource($source));
    }
}