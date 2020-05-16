<?php

namespace Creonit\MediaBundle\Admin\MediaModule;

use Creonit\AdminBundle\Module;

class MediaModule extends Module
{
    protected function configure()
    {
        $this->setVisible(false);
    }

    public function initialize()
    {
        $this->addComponent(new GalleryTable);
        $this->addComponent(new GalleryImageEditor());
        $this->addComponent(new GalleryVideoEditor());
    }
}