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
        $faker = $this->getFaker();

        $files = Finder::create()
            ->name('*.JPG')
            ->in(__DIR__.'/../data/files');

        $i = 0;

        foreach ($files as $pos => $file) {
            $media = $manager->create();
            $media->setBinaryContent($file);
            $media->setEnabled(true);
            /**
             * Uncomment the line below to allow the faker generates a description automatically for each picture.
             */
            //$media->setDescription($faker->sentence(10));

            /**
             * If the line above was uncommented, all these lines of 'if evaluations' should be commented.
             */
            if ($i == 0) {
                $media->setDescription('Machu Picchu Peru.');
            } elseif ($i == 1) {
                $media->setDescription('Lake Titicaca Peru.');
            } elseif ($i == 2) {
                $media->setDescription('Ruins in Arequipa Peru.');
            } elseif ($i == 3) {
                $media->setDescription('Summer Holiday.');
            } elseif ($i == 4) {
                $media->setDescription('Beautiful Dream Beach.');
            } elseif ($i == 5) {
                $media->setDescription('Heart Island.');
            } elseif ($i == 6) {
                $media->setDescription('On Boat in a Tropical Beach.');
            } elseif ($i == 7) {
                $media->setDescription('Summer Holiday in a Tropical Beach.');
            } elseif ($i == 8) {
                $media->setDescription('Around the Mountain in New Zealand.');
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
        /*
        $videos = array(
            'ocAyDZC2aiU' => 'sonata.media.provider.youtube',
            'xdw0tz'      => 'sonata.media.provider.dailymotion',
            '9636197'     => 'sonata.media.provider.vimeo'
        );

        foreach ($videos as $video => $provider) {
            $media = $manager->create();
            $media->setBinaryContent($video);
            $media->setEnabled(true);

            $manager->save($media, 'default', $provider);

            $this->addMedia($gallery, $media);
        }
        */

        $gallery->setEnabled(true);
        $gallery->setName('Wallpaper Collection in High Quality');
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

    /**
     * @return \Faker\Generator
     */
    public function getFaker()
    {
        return $this->container->get('faker.generator');
    }
}