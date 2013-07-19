<?php

namespace Sonata\Bundle\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;

use Sonata\Bundle\DemoBundle\Entity\Enquiry;
use Sonata\Bundle\DemoBundle\Form\Type\EnquiryType;

class ContactController extends Controller
{
	/**
     * @Route("/contact")
     */
    public function contactAction(Request $request)
    {
        $request = $this->getRequest();

        $enquiry = new Enquiry();
        $form = $this->createForm(new EnquiryType(), $enquiry);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $message = \Swift_Message::newInstance()
                ->setSubject('Contact from the website')
                ->setFrom($this->container->getParameter('vihuvac_website.app.email_address'))
                ->setTo($this->container->getParameter('vihuvac_website.contact.email_address'))
                ->setBody(
                    $this->renderView('SonataDemoBundle:Contact:comment.txt.twig',
                        array(
                            'enquiry' => $enquiry
                        )
                    )
                );

                $this->get('mailer')->send($message);

                $this->get('session')->getFlashBag()->set(
                    'success',
                    $this->get('translator')->trans('Your contact enquiry was successfully sent. Thanks for writing!')
                );

                return $this->redirect($this->generateUrl('sonata_demo_contact'));
            }
        }

        return $this->render('SonataDemoBundle:Contact:contact.html.twig', array(
            'form' => $form->createView()
        ));
    }
}