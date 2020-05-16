<?php

namespace Creonit\MediaBundle\Admin\MediaModule;

use Creonit\MediaBundle\Model\GalleryItem;
use Creonit\MediaBundle\Model\GalleryQuery;
use Creonit\AdminBundle\Component\EditorComponent;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;

class GalleryVideoEditor extends EditorComponent
{
    /**
     *
     * @title Видео
     * @entity Creonit\MediaBundle\Model\GalleryItem
     * @field video_id:video {constraints: [NotBlank()]}
     *
     * @template
     * {{ video_id | video | group('Видео') }}
     * {{ title | text | group('Название') }}
     * {{ description | textedit | group('Описание') }}
     */
    public function schema()
    {
    }

    /**
     * @param ComponentRequest $request
     * @param ComponentResponse $response
     * @param GalleryItem $entity
     */
    public function validate(ComponentRequest $request, ComponentResponse $response, $entity)
    {
        if ($entity->isNew() and !GalleryQuery::create()->findPk($request->query->get('gallery_id'))) {
            $response->error('Галерея не найдена');
        }
    }

    /**
     * @param ComponentRequest $request
     * @param ComponentResponse $response
     * @param GalleryItem $entity
     */
    public function preSave(ComponentRequest $request, ComponentResponse $response, $entity)
    {
        if ($entity->isNew()) {
            $entity->setGalleryId($request->query->get('gallery_id'));
        }
    }
}
