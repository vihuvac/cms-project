<?php

namespace Vihuvac\Bundle\WebsiteBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Sonata\NewsBundle\Model\CommentInterface;

class LoadNewsData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    function getOrder()
    {
        return 3;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $postManager = $this->getPostManager();

        $faker = $this->getFaker();

        $tags = array(
            'Form'    => null,
            'Gallery' => null,
            'General' => null,
            'Symfony' => null,
            'Video'   => null,
            'Youtuve' => null
        );

        foreach($tags as $tagName => $null) {
            $tag = $this->getTagManager()->create();
            $tag->setEnabled(true);
            $tag->setName($tagName);

            $tags[$tagName] = $tag;
            $this->getTagManager()->save($tag);
        }

        foreach (range(1, 20) as $id) {
            $post = $postManager->create();
            $post->setAuthor($this->getReference('user-admin'));

            $post->setAbstract($faker->sentence(30));
            $post->setEnabled(true);
            $post->setTitle($faker->sentence(6));
            $post->setPublicationDateStart($faker->dateTimeBetween('-30 days', '-1 days'));

            $id = $this->getReference('sonata-media-'.rand(2, 7))->getId();

            $raw = <<<RAW

### Gist Formatter

Now a specific gist from github

<% gist '1552362', 'gistfile1.txt' %>

### Media Formatter

Load a media from a <code>SonataMediaBundle</code> with a specific format

<% media $id, 'hdtv' %>

RAW
;
            $raw .= sprintf("### %s\n\n%s\n\n### %s\n\n%s",
                $faker->sentence(rand(3, 6)),
                $faker->text(1000),
                $faker->sentence(rand(3, 6)),
                $faker->text(1000)
            );

            $post->setRawContent($raw);
            $post->setContentFormatter('markdown');

            $post->setContent($this->getPoolFormatter()->transform($post->getContentFormatter(), $post->getRawContent()));
            $post->setCommentsDefaultStatus(CommentInterface::STATUS_VALID);

            foreach($tags as $tag) {
                $post->addTags($tag);
            }

            foreach(range(1, $faker->randomDigit + 2) as $commentId) {
                $comment = $this->getCommentManager()->create();
                $comment->setEmail($faker->email);
                $comment->setName($faker->name);
                $comment->setStatus(CommentInterface::STATUS_VALID);
                $comment->setMessage($faker->sentence(25));
                $comment->setUrl($faker->url);

                $post->addComments($comment);
            }

            $postManager->save($post);
        }
    }

    public function getPoolFormatter()
    {
        return $this->container->get('sonata.formatter.pool');
    }

    /**
     * @return \Sonata\NewsBundle\Model\TagManagerInterface
     */
    public function getTagManager()
    {
        return $this->container->get('sonata.news.manager.tag');
    }

    /**
     * @return \Sonata\NewsBundle\Model\PostManagerInterface
     */
    public function getPostManager()
    {
        return $this->container->get('sonata.news.manager.post');
    }

    /**
     * @return \Sonata\NewsBundle\Model\CommentManagerInterface
     */
    public function getCommentManager()
    {
        return $this->container->get('sonata.news.manager.comment');
    }

    /**
     * @return \Faker\Generator
     */
    public function getFaker()
    {
        return $this->container->get('faker.generator');
    }
}