<?php

namespace Creonit\MediaBundle\Admin;

use Creonit\AdminBundle\Plugin as BasePlugin;
use Creonit\MediaBundle\Admin\Field\FileField;
use Creonit\MediaBundle\Admin\Field\GalleryField;
use Creonit\MediaBundle\Admin\Field\ImageField;
use Creonit\MediaBundle\Admin\Field\VideoField;

class AdminPlugin extends BasePlugin
{
    public function configure()
    {
        $this->addFieldType(FileField::class);
        $this->addFieldType(ImageField::class);
        $this->addFieldType(VideoField::class);
        $this->addFieldType(GalleryField::class);

        $this->addStylesheet('/bundles/creonitmedia/css/admin.css');
        $this->addJavascript('/bundles/creonitmedia/js/admin.js');
    }
}