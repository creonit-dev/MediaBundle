<?php

namespace Creonit\MediaBundle\Admin\Field;

use Creonit\AdminBundle\Component\Field\Field;
use Creonit\AdminBundle\Component\Field\NoData;
use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\MediaBundle\Admin\Event\AfterSaveFileEvent;
use Creonit\MediaBundle\Admin\Event\BeforeSaveFileEvent;
use Creonit\MediaBundle\Model\FileQuery;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileField extends Field
{
    const TYPE = 'file';

    public function extract(ComponentRequest $request)
    {
        return [
            'file' => parent::extract($request),
            'delete' => $request->data->has($this->name . '__delete')
        ];
    }

    public function build(UploadedFile $uploadedFile)
    {
        return [
            'file' => $uploadedFile,
            'delete' => false
        ];
    }

    public function process($data)
    {
        /** @var UploadedFile $file */
        if ($file = $data['file'] and !$file instanceof NoData) {
            $data['file'] = $this->container->get('creonit_media')->uploadFile($file, $this->parameters->get('prefix', 'file'));
        }

        return $data;
    }

    public function saveFile(File $file)
    {
        $file = $this->container->get('creonit_media')->createFile($file);

        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch($event = new BeforeSaveFileEvent($file, $this));

        $file = $event->getFile();
        $file->save();

        $eventDispatcher->dispatch(new AfterSaveFileEvent($file, $this));

        return $file;
    }

    public function deleteFile(\Creonit\MediaBundle\Model\File $file)
    {
        try {
            $fileInfo = $this->container->get('creonit_media')->openFile($file);
            @unlink($fileInfo->getPathname());

        } catch (FileNotFoundException $exception) {
        }

        $file->delete();
    }

    /**
     * @todo Сделать настраиваемое удаление файла
     */
    public function save($entity, $data, $processed = false)
    {
        if ($data['delete']) {
//            $fileId = $this->loadValue($entity);
            $this->saveValue($entity, null);

//            if ($fileId and $file = FileQuery::create()->findPk($fileId)) {
//                $this->deleteFile($file);
//            }

        } else if ($data['file'] instanceof File) {
            if ($processed === false) {
                $data = $this->process($data);
            }

            $file = $this->saveFile($data['file']);
            $this->saveValue($entity, $file->getId());
        }
    }

    public function decorate($data)
    {
        if ($data instanceof \Creonit\MediaBundle\Model\File) {
            $mediaService = $this->container->get('creonit_media');

            $file = $data;
            $data = [
                'id' => $file->getId(),
                'mime' => $file->getMime(),
                'size' => $mediaService->formatFileSize($file->getSize()),
                'extension' => $file->getExtension(),
                'path' => $file->getPath(),
                'name' => $file->getName(),
                'original_name' => $file->getOriginalName(),
                'url' => $file->getUrl(),
            ];

            if (preg_match('/^(png|jpe?g)$/i', $file->getExtension())) {
                $imageHandling = $this->container->get('image.handling');

                try {
                    $data['image_url'] = $imageHandling
                        ->open($mediaService->openFile($file)->getPathname())
                        ->cropResize(200, 200)
                        ->png();

                } catch (FileNotFoundException $exception) {
                }

            } else if (preg_match('/^(svg)$/i', $file->getExtension())) {
                $data['image_url'] = $file->getUrl();
            }

            return $data;
        }

        return $data;
    }

    public function load($entity)
    {
        if ($value = $this->loadValue($entity)) {
            return $this->decorate(
                FileQuery::create()->findPk($value)
            );
        }

        return $value;
    }
}
