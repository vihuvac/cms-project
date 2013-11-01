<?php

namespace Vihuvac\Bundle\WebsiteBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Sonata\MediaBundle\Model\GalleryInterface;
use Sonata\MediaBundle\Model\MediaInterface;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

class LoadMediaData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    function getOrder()
    {
        return 2;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $gallery = $this->getGalleryManager()->create();

        $manager = $this->getMediaManager();

        $files = Finder::create()
            ->name('*.JPG')
            ->in(__DIR__.'/../data/files');

        $i = 0;

        foreach ($files as $pos => $file) {
            $media = $manager->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            
            $descriptions = [
                'IMG_001.JPG' => 'Machu Picchu Peru.',
                'IMG_002.JPG' => 'Lake Titicaca Peru.',
                'IMG_003.JPG' => 'Ruins in Arequipa Peru.',
                'IMG_004.JPG' => 'Summer Holiday.',
                'IMG_005.JPG' => 'Beautiful Dream Beach.',
                'IMG_006.JPG' => 'Heart Island.',
                'IMG_007.JPG' => 'On Boat in a Tropical Beach.',
                'IMG_008.JPG' => 'Summer Holiday in a Tropical Beach.',
                'IMG_009.JPG' => 'Around the Mountain in New Zealand.',
            ];

            if (isset($descriptions[$file->getFileName()])) {
                $media->setDescription($descriptions[$file->getFileName()]);
            } else {
                $media->setDescription('These pictures belongs to the wallpaper collection in HQ');
            }

            $this->addReference('sonata-media-'.($i++), $media);

            $manager->save($media, 'default', 'sonata.media.provider.image');

            $this->addMedia($gallery, $media);
        }

        /**
         * Uncomment the array of videos in case of adding any video to the main gallery,
         * the same will be shown in the home page.
         */
        //$videos = array(
        //    'ocAyDZC2aiU' => 'sonata.media.provider.youtube',
        //    'xdw0tz'      => 'sonata.media.provider.dailymotion',
        //    '9636197'     => 'sonata.media.provider.vimeo'
        //);

        //foreach ($videos as $video => $provider) {
        //    $media = $manager->create();
        //    $media->setBinaryContent($video);
        //    $media->setEnabled(true);

        //    $manager->save($media, 'default', $provider);

        //    $this->addMedia($gallery, $media);
        //}

        $gallery->setEnabled(true);
        $gallery->setName('My Wallpapers Collection in HQ');
        $gallery->setDefaultFormat('small');
        $gallery->setContext('default');

        $this->getGalleryManager()->update($gallery);

        $this->addReference('media-gallery', $gallery);
    }

    /**
     * @param \Sonata\MediaBundle\Model\GalleryInterface $gallery
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     * @return void
     */
    public function addMedia(GalleryInterface $gallery, MediaInterface $media)
    {
        $galleryHasMedia = new \Application\Sonata\MediaBundle\Entity\GalleryHasMedia();
        $galleryHasMedia->setMedia($media);
        $galleryHasMedia->setPosition(count($gallery->getGalleryHasMedias()) + 1);
        $galleryHasMedia->setEnabled(true);

        $gallery->addGalleryHasMedias($galleryHasMedia);
    }

    /**
     * @return \Sonata\MediaBundle\Model\MediaManagerInterface
     */
    public function getMediaManager()
    {
        return $this->container->get('sonata.media.manager.media');
    }

    /**
     * @return \Sonata\MediaBundle\Model\MediaManagerInterface
     */
    public function getGalleryManager()
    {
        return $this->container->get('sonata.media.manager.gallery');
    }
}