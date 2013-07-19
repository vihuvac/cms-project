<?php

namespace Sonata\Bundle\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Mapping\ClassMetadata;

class Enquiry
{
    /**
     * @Assert\NotBlank
     */ 
    protected $name;

    /**
     * @Assert\Email
     */
    protected $email;

    /**
     * @Assert\NotBlank
     */
    protected $subject;

    /**
     * @Assert\NotBlank
     */
    protected $comment;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }
}