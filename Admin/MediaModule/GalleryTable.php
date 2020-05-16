<?php

namespace Creonit\MediaBundle\Admin\MediaModule;

use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;
use Creonit\AdminBundle\Component\Scope\Scope;
use Creonit\AdminBundle\Component\TableComponent;
use Creonit\MediaBundle\Admin\Field\ImageField;
use Creonit\MediaBundle\Model\GalleryItem;
use Creonit\MediaBundle\Model\GalleryItemQuery;
use Creonit\MediaBundle\Model\GalleryQuery;

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
     * @action cover(key, rowId){
     *     var $row = this.findRowById(rowId),
     *         $button = $row.find('.fa-picture-o').closest('button'),
     *         cover = !$button.hasClass('btn-success');
     *
     *     $button.toggleClass('btn-success', cover);
     *     $button.toggleClass('btn-default', !cover);
     *     this.request('cover', $.extend(this.getQuery(), {key: key}), {cover: cover}, (response) => {
     *         this.checkResponse(response);
     *     });
     * }
     *
     * @header
     * {% if _query.image != false %}
     *   {{ button('Добавить изображение', {icon: 'image', size: 'sm', type: 'success'}) | action('uploadImages', _query) }}
     * {% endif %}
     * {% if _query.video != false %}
     *   {{ button('Добавить видео', {icon: 'youtube-play', size: 'sm', type: 'success'}) | open('Media.GalleryVideoEditor', _query) }}
     * {% endif %}
     *
     * @cols ., Название, .
     *
     * \GalleryItem
     * @entity Creonit\MediaBundle\Model\GalleryItem
     * @sortable true
     *
     * @field image_id:image
     * @field video_id:video
     * @field cover
     *
     * @col
     * {% if image_id %}
     *     {{ image_id | image_preview | open('Media.GalleryImageEditor', {key: _key}) | controls }}
     * {% else %}
     *     {{ video_id | video_preview | open('Media.GalleryVideoEditor', {key: _key}) | controls }}
     * {% endif %}
     *
     * @col {{ title }}
     * @col {{ buttons(
     *   (button('', {icon: 'picture-o', size: 'xs', type: cover ? 'success' : 'default'}) | tooltip('Сделать обложкой') | action('cover', _key, _row_id))
     *   ~ _visible()
     *   ~ _delete()
     * ) }}
     */
    public function schema()
    {
        $this->addHandler('upload_images', [$this, 'uploadImages']);
        $this->addHandler('cover', [$this, 'cover']);
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

        if ($files = $request->data->get('files')) {
            /** @var ImageField $field */
            $field = $this->createField('image_id', [], 'image');

            foreach ($files as $file) {
                $file = $field->build($file);
                if (count($field->validate($file))) {
                    continue;
                }

                $entity = new GalleryItem();
                $entity->setGalleryId($request->query->get('gallery_id'));

                $field->save($entity, $file);
                $entity->save();
            }
        }
    }

    public function cover(ComponentRequest $request, ComponentResponse $response)
    {
        $query = $request->query;

        if (
            $request->data->has('cover')
            and $query->get('key')
        ) {
            $scope = $this->getScope('GalleryItem');

            if ($entity = $scope->createQuery()->findPk($query->get('key'))) {
                $cover = $request->data->getBoolean('cover');
                $coverField = $this->createField('cover');
                $coverField->save($entity, $cover);
                $entity->save();

                $response->data->set('success', true);
                $response->data->set('cover', $coverField->load($entity));

            } else {
                $response->flushError('Элемент не найден');
            }

        } else {
            $response->flushError('Ошибка при выполнения запроса');
        }
    }
}