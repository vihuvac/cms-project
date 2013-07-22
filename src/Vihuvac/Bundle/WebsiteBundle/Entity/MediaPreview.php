<?php

namespace Vihuvac\Bundle\WebsiteBundle\Entity;

use Sonata\MediaBundle\Model\MediaInterface;

class MediaPreview
{
    protected $media;

    /**
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     * @return void
     */
    public function setMedia(MediaInterface $media)
    {
        $this->media = $media;
    }

    /**
     * @return \Sonata\MediaBundle\Model\MediaInterface
     */
    public function getMedia()
    {
        return $this->media;
    }
}