<?php

namespace Creonit\MediaBundle\Admin\Field;

use Creonit\AdminBundle\Component\Field\Field;
use Creonit\AdminBundle\Component\Field\NoData;
use Creonit\MediaBundle\Exception\VideoSourceIsNotSupportedException;
use Creonit\MediaBundle\Model\Video;
use Creonit\MediaBundle\Model\VideoQuery;
use Symfony\Component\Validator\ConstraintViolation;

class VideoField extends Field
{
    const TYPE = 'video';

    public function validate($data)
    {
        $violations = parent::validate($data);
        if ($violations && count($violations)) {
            return $violations;
        }

        if ($data and !$data instanceof NoData) {
            try {
                $this->container->get('creonit_media')->getVideoHandler($data);

            } catch (VideoSourceIsNotSupportedException $exception) {
                return [new ConstraintViolation('Источник видео не поддерживается', null, [], '', '', '')];
            }
        }

        return [];
    }

    public function save($entity, $data, $processed = false)
    {
        if (!$data instanceof NoData) {
            $videoId = $this->loadValue($entity);
            $video = $videoId ? VideoQuery::create()->findPk($videoId) : null;

            if ($data) {
                $videoHandler = $this->container->get('creonit_media')->getVideoHandler($data);
                if (!$video or !$videoHandler->equals($video, $data)) {
                    if ($video) {
                        $video->delete();
                    }

                    $video = $videoHandler->createVideo($data);
                    $video->save();
                    $videoId = $video->getId();
                }

            } else {
                if ($video) {
                    $video->delete();
                }

                $videoId = null;
            }

            $this->saveValue($entity, $videoId);
        }
    }

    public function decorate($data)
    {
        if ($data instanceof Video) {
            try {
                $videoHandler = $this->container->get('creonit_media')->getVideoHandler($data);
                $videoData = $videoHandler->getVideoData($data);

                return [
                    'source' => $data->getSource(),
                    'image_url' => $videoData->getImageUrl(),
                    'embed_url' => $videoData->getEmbedUrl()
                ];

            } catch (VideoSourceIsNotSupportedException $exception) {
                return [];
            }
        }

        return $data;
    }

    public function load($entity)
    {
        if ($value = $this->loadValue($entity)) {
            if ($video = VideoQuery::create()->findPk($value)) {
                return $this->decorate($video);
            }
        }

        return $value;
    }
}