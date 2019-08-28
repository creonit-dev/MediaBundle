<?php

namespace Creonit\MediaBundle\Admin;

use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;
use Creonit\AdminBundle\Component\Scope\Scope;
use Creonit\AdminBundle\Component\TableComponent;
use Creonit\MediaBundle\Model\GalleryItemQuery;
use Creonit\MediaBundle\Model\GalleryQuery;
use Creonit\MediaBundle\Model\GalleryItem;

class GalleryTable extends TableComponent
{


    /**
     * @action uploadImages(query){
     *   var $form = $('<form><input type="file" name="files" multiple></form>');
     *   var $file = $form.find('input');
     *   var $button = this.node.find('button[data-name="uploadImages"]')
     *   var $buttonIcon = $button.find('.icon')
     *
     *   $file.on('change', function(){
     *     $button.prop('disabled', true);
     *     $buttonIcon.removeClass('fa-image').addClass('fa-spin fa-spinner');
     *     this.request('upload_images', query, {files: $file[0].files}, function(){
     *       $buttonIcon.addClass('fa-image').removeClass('fa-spin fa-spinner');
     *       $button.prop('disabled', false);
     *     }.bind(this));
     *     this.loadData();
     *   }.bind(this));
     *
     *   $file.click();
     * }
     *
     * @header
     * {% if _query.image != false %}
     *   {{ button('Добавить изображения', {icon: 'image', size: 'sm', type: 'success'}) | action('uploadImages', _query) }}
     * {% endif %}
     * {% if _query.video != false %}
     *   {{ button('Добавить видео', {icon: 'youtube-play', size: 'sm', type: 'success'}) | open('Media.GalleryVideoEditor', _query) }}
     * {% endif %}
     *
     * @cols Элемент, .
     *
     * \GalleryItem
     * @entity Creonit\MediaBundle\Model\GalleryItem
     * @sortable true
     *
     * @field image_id:image
     * @field video_id:video
     * @field url:image
     *
     * @col
     * {% if image_id %}
     *      {{ image_id.preview | raw | open('Media.GalleryImageEditor', {key: _key}) | controls }}
     * {% else %}
     *      {{ video_id.preview | raw | open('Media.GalleryVideoEditor', {key: _key}) | controls }}
     * {% endif %}
     *
     * @col {{ _delete() }}
     *
     */
    public function schema()
    {
        $this->addHandler('upload_images', [$this, 'uploadImages']);
    }

    /**
     * @param ComponentRequest $request
     * @param ComponentResponse $response
     * @param GalleryItemQuery $query
     * @param Scope $scope
     * @param $relation
     * @param $relationValue
     * @param $level
     */
    protected function filter(ComponentRequest $request, ComponentResponse $response, $query, Scope $scope, $relation, $relationValue, $level)
    {
        $query->filterByGalleryId($request->query->get('gallery_id'));

    }

    public function uploadImages(ComponentRequest $request, ComponentResponse $response)
    {
        if (!GalleryQuery::create()->findPk($request->query->get('gallery_id'))) {
            $response->error('Галерея не найдена');
        }

        if($files = $request->data->get('files')){
            $imageField = $this->createField('image_id', [], 'image');

            foreach ($files as $file){
                $file = ['file' => $file, 'delete' => false];
                $entity = new GalleryItem();
                $entity->setGalleryId($request->query->get('gallery_id'));
                if($imageField->validate($file)->count()){
                    continue;
                }
                $imageField->save($entity, $file);
                $entity->save();
            }
        }
    }


}