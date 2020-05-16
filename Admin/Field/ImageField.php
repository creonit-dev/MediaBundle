<?php

namespace Creonit\MediaBundle\Admin\Field;

use Creonit\AdminBundle\Component\Field\NoData;
use Creonit\MediaBundle\Model\Image;
use Creonit\MediaBundle\Model\ImageQuery;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImageField extends FileField
{
    const TYPE = 'image';
    const MAX_WIDTH = 3000;
    const MAX_HEIGHT = 3000;
    const MAX_SIZE = '5M';

    public function save($entity, $data, $processed = false)
    {
        if ($data['delete']) {
            $imageId = $this->loadValue($entity);
            $this->saveValue($entity, null);

            if ($imageId and $image = ImageQuery::create()->findPk($imageId)) {
                $image->delete();
            }

        } else if ($data['file'] instanceof File) {
            if ($processed === false) {
                $data = $this->process($data);
            }

            $file = $this->saveFile($data['file']);

            $image = new Image();
            $image->setFile($file);
            $image->save();

            $this->saveValue($entity, $image->getId());
        }
    }

    public function load($entity)
    {
        if ($value = $this->loadValue($entity)) {
            if ($image = ImageQuery::create()->findPk($value)) {
                return $this->decorate($image->getFile());
            }
        }

        return $value;
    }

    private function findImageConstraint($constraints)
    {
        foreach ($constraints as $constraint) {
            if ($constraint instanceof ImageConstraint) {
                return $constraint;
            }
        }
        return null;
    }

    public function validate($data)
    {
        if ($data['file'] instanceof NoData) {
            return [];
        }

        $constraints = $this->parameters->get('constraints', []);
        $required = $this->parameters->get('required');

        if ($required) {
            $constraints[] = new NotBlank(true === $required ? [] : ['message' => $required]);
        }

        if (!$imageConstraint = $this->findImageConstraint($constraints)) {
            $constraints[] = $imageConstraint = new ImageConstraint;
        }

        if (!$imageConstraint->maxWidth) {
            $imageConstraint->maxWidth = static::MAX_WIDTH;
        }

        if (!$imageConstraint->maxHeight) {
            $imageConstraint->maxHeight = static::MAX_HEIGHT;
        }

        if (!$imageConstraint->maxSize) {
            $imageConstraint->maxSize = static::MAX_SIZE;
        }

        $imageConstraint->detectCorrupted = true;

        return $constraints ? $this->container->get('validator')->validate($data['file'], $constraints) : [];
    }
}
