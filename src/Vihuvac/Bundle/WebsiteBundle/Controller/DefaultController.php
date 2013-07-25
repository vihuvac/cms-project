<?php

namespace Vihuvac\Bundle\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Vihuvac\Bundle\WebsiteBundle\Entity\MediaPreview;

class DefaultController extends Controller
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/media")
     * @Template()
     */
    public function mediaAction(Request $request)
    {
        // preset a default value
        $media = $this->get('sonata.media.manager.media')->create();
        $media->setBinaryContent('http://www.youtube.com/watch?v=dU1xS07N-FA');

        // create the target object
        $mediaPreview = new MediaPreview();
        $mediaPreview->setMedia($media);

        // create the form
        $builder = $this->createFormBuilder($mediaPreview);
        $builder->add('media', 'sonata_media_type', array(
             'provider' => 'sonata.media.provider.youtube',
             'context'  => 'default'
        ));

        $form = $builder->getForm();

        // bind and transform the media's binary content into real content
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            $this->getSeoPage()
                ->setTitle($media->getName())
                ->addMeta('property', 'og:description', $media->getDescription())
                ->addMeta('property', 'og:type', 'video')
            ;
        }

        return array(
            'form' => $form->createView(),
            'media' => $mediaPreview->getMedia()
        );
    }

    /**
     * @return \Sonata\SeoBundle\Seo\SeoPageInterface
     */
    public function getSeoPage()
    {
        return $this->get('sonata.seo.page');
    }
}