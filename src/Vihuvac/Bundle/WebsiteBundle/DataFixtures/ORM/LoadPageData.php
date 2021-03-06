<?php

namespace Vihuvac\Bundle\WebsiteBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Model\PageInterface;


class LoadPageData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    function getOrder()
    {
        return 4;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $site = $this->createSite();
        $this->createGlobalPage($site);
        $this->createHomePage($site);
        $this->createBlogIndex($site);
        $this->createBiographyIndex($site);
        $this->createGalleryIndex($site);
        $this->createFaqIndex($site);
        $this->createMediaPage($site);
        $this->createUserPage($site);
    }

    public function createSite()
    {
        $site = $this->getSiteManager()->create();

        $site->setHost('localhost');
        $site->setEnabled(true);
        $site->setName('localhost');
        $site->setEnabledFrom(new \DateTime('now'));
        $site->setEnabledTo(new \DateTime('+10 years'));
        $site->setRelativePath("");
        $site->setIsDefault(true);

        $this->getSiteManager()->save($site);

        return $site;
    }

    /**
     * @param SiteInterface $site
     */
    public function createHomePage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $this->addReference('page-homepage', $homepage = $pageManager->create());
        $homepage->setSlug('/');
        $homepage->setUrl('/');
        $homepage->setName('homepage');
        $homepage->setEnabled(true);
        $homepage->setDecorate(0);
        $homepage->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $homepage->setTemplateCode('default');
        $homepage->setRouteName(PageInterface::PAGE_ROUTE_CMS_NAME);
        $homepage->setSite($site);

        $pageManager->save($homepage);

        // CREATE A HEADER BLOCK
        $homepage->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $homepage,
            'code' => 'content',
        )));

        $content->setName('The container container');

        $blockManager->save($content);

        // add a block text
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', $this->templatingEngine()->render('WebsiteBundle:ORM:main_header.html.twig'));

        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($homepage);

        // add a gallery
        $content->addChildren($gallery = $blockManager->create());
        $gallery->setType('sonata.media.block.gallery');
        $gallery->setSetting('galleryId', $this->getReference('media-gallery')->getId());
        $gallery->setSetting('title', $this->getReference('media-gallery')->getName());
        $gallery->setSetting('context', 'default');
        $gallery->setSetting('format', 'hd');
        $gallery->setPosition(2);
        $gallery->setEnabled(true);
        $gallery->setPage($homepage);

        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');

        $text->setPosition(3);
        $text->setEnabled(true);
        $text->setSetting('content', $this->templatingEngine()->render('WebsiteBundle:ORM:main_footer.html.twig'));

        $pageManager->save($homepage);
    }

    /**
     * @param SiteInterface $site
     */
    public function createBlogIndex(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();

        $blogIndex = $pageManager->create();
        $blogIndex->setSlug('blog');
        $blogIndex->setUrl('/blog');
        $blogIndex->setName('Blog');
        $blogIndex->setEnabled(true);
        $blogIndex->setDecorate(1);
        $blogIndex->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $blogIndex->setTemplateCode('default');
        $blogIndex->setRouteName('sonata_news_home');
        $blogIndex->setParent($this->getReference('page-homepage'));
        $blogIndex->setSite($site);

        $pageManager->save($blogIndex);
    }

    /**
     * @param SiteInterface $site
     */
    public function createBiographyIndex(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $biography = $pageManager->create();
        $biography->setSlug('biography');
        $biography->setUrl('/biography');
        $biography->setName('Biography');
        $biography->setEnabled(true);
        $biography->setDecorate(1);
        $biography->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $biography->setTemplateCode('default');
        $biography->setRouteName('page_slug');
        $biography->setParent($this->getReference('page-homepage'));
        $biography->setSite($site);

        // CREATE A HEADER BLOCK
        $biography->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $biography,
            'code' => 'content_top',
        )));

        $content->setName('The content_top container');

        // add a block text
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT

<h1>Biography</h1>

<p>
    This current text is defined in a <code>text block</code> linked to a custom symfony action <code>GalleryController::indexAction</code>
    the SonataPageBundle can encapsulate an action into a dedicated template. <br /><br />

    If you are connected as an admin you can click on <code>Show Zone</code> to see the different editable areas. Once
    areas are displayed, just double click on one to edit it.
</p>

CONTENT
);
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($biography);

        $pageManager->save($biography);
    }

    /**
     * @param SiteInterface $site
     */
    public function createGalleryIndex(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $galleryIndex = $pageManager->create();
        $galleryIndex->setSlug('gallery');
        $galleryIndex->setUrl('/media/gallery');
        $galleryIndex->setName('Gallery');
        $galleryIndex->setEnabled(true);
        $galleryIndex->setDecorate(1);
        $galleryIndex->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $galleryIndex->setTemplateCode('default');
        $galleryIndex->setRouteName('sonata_media_gallery_index');
        $galleryIndex->setParent($this->getReference('page-homepage'));
        $galleryIndex->setSite($site);

        // CREATE A HEADER BLOCK
        $galleryIndex->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $galleryIndex,
            'code' => 'content_top',
        )));

        $content->setName('The content_top container');

        // add a block text
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT

<p>
    This current text is defined in a <code>text block</code> linked to a custom symfony action <code>GalleryController::indexAction</code>
    the SonataPageBundle can encapsulate an action into a dedicated template. <br /><br />

    If you are connected as an admin you can click on <code>Show Zone</code> to see the different editable areas. Once
    areas are displayed, just double click on one to edit it.
</p>

<h1>Gallery List</h1>

CONTENT
);
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($galleryIndex);

        $pageManager->save($galleryIndex);
    }

    /**
     * @param SiteInterface $site
     */
    public function createFaqIndex(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $faqIndex = $pageManager->create();
        $faqIndex->setSlug('frequently-asked-questions');
        $faqIndex->setUrl('/frequently-asked-questions');
        $faqIndex->setName('Faq');
        $faqIndex->setEnabled(true);
        $faqIndex->setDecorate(1);
        $faqIndex->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $faqIndex->setTemplateCode('default');
        $faqIndex->setRouteName('page_slug');
        $faqIndex->setParent($this->getReference('page-homepage'));
        $faqIndex->setSite($site);

        // CREATE A HEADER BLOCK
        $faqIndex->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $faqIndex,
            'code' => 'content_top',
        )));

        $content->setName('The content_top container');

        // add a block text
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT

<h1>Frequently Asked Questions</h1>

<p>
    This current text is defined in a <code>text block</code> linked to a custom symfony action <code>GalleryController::indexAction</code>
    the SonataPageBundle can encapsulate an action into a dedicated template. <br /><br />

    If you are connected as an admin you can click on <code>Show Zone</code> to see the different editable areas. Once
    areas are displayed, just double click on one to edit it.
</p>

CONTENT
);
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($faqIndex);

        $pageManager->save($faqIndex);
    }

    /**
     * @param SiteInterface $site
     */
    public function createMediaPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();

        $this->addReference('page-media', $media = $pageManager->create());
        $media->setSlug('media');
        $media->setUrl('/media');
        $media->setName('Media & Seo');
        $media->setEnabled(true);
        $media->setDecorate(1);
        $media->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $media->setTemplateCode('default');
        $media->setRouteName('vihuvac_website_default_media');
        $media->setSite($site);
        $media->setParent($this->getReference('page-homepage'));

        $pageManager->save($media);
    }

    /**
     * @param SiteInterface $site
     */
    public function createUserPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $this->addReference('page-user', $userPage = $pageManager->create());
        $userPage->setSlug('user-credentials');
        $userPage->setUrl('/user-credentials');
        $userPage->setName('Admin');
        $userPage->setEnabled(true);
        $userPage->setDecorate(1);
        $userPage->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $userPage->setTemplateCode('default');
        $userPage->setRouteName('page_slug');
        $userPage->setSite($site);
        $userPage->setParent($this->getReference('page-homepage'));

        $userPage->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $userPage,
            'code' => 'content_top',
        )));

        $content->setName('The content_top container');

        // add a block text
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT

<h2>Website Admin Dashboard</h2>

<div>
    You can connect to the <a href="/admin/dashboard">admin section</a> by using two different accounts: <br>

    <ul>
        <li>Login: admin - Password: admin</li>
        <li>Login: secure - Password: secure - Key: 4YU4QGYPB63HDN2C</li>
    </ul>

    <h3>Two Step Verification</h3>
    The <b>secure</b> account is a demo of the Two Step Verification provided by
    the <a href="http://sonata-project.org/bundles/user/2-0/doc/reference/two_step_validation.html">Sonata User Bundle</a>

    <br />
    <br />
    <center>
        <img src="/bundles/website/images/useful/secure_qr_code.png" class="img-polaroid" />
        <br />
        <em>Take a shot of this QR Code with <a href="https://support.google.com/accounts/bin/answer.py?hl=en&answer=1066447">Google Authenticator</a></em>
    </center>

</div>

CONTENT
);
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($userPage);

        $pageManager->save($userPage);
    }

    public function createGlobalPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $global = $pageManager->create();
        $global->setName('global');
        $global->setRouteName('_page_internal_global');
        $global->setSite($site);

        $pageManager->save($global);

        // CREATE A HEADER BLOCK
        $global->addBlocks($title = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $global,
            'code' => 'title',
        )));

        $title->setName('The title container');
        $title->addChildren($text = $blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', '<h2><a href="/">Victor Hugo</a></h2>');
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        $global->addBlocks($header = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $global,
            'code' => 'header',
        )));

        $header->setName('The header container');
        $header->addChildren($menu = $blockManager->create());

        $menu->setType('sonata.page.block.children_pages');
        $menu->setSetting('current', false);
        $menu->setPosition(1);
        $menu->setEnabled(true);
        $menu->setPage($global);

        // CREATE A FOOTER BLOCK
        $global->addBlocks($footer = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $global,
            'code' => 'footer',
        )));

        $footer->setName('The footer container');
        $footer->addChildren($text = $blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', $this->templatingEngine()->render('WebsiteBundle:ORM:global_footer.html.twig'));
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        $pageManager->save($global);
    }

    /**
     * @return \Sonata\PageBundle\Model\SiteManagerInterface
     */
    public function getSiteManager()
    {
        return $this->container->get('sonata.page.manager.site');
    }

    /**
     * @return \Sonata\PageBundle\Model\PageManagerInterface
     */
    public function getPageManager()
    {
        return $this->container->get('sonata.page.manager.page');
    }

    /**
     * @return \Sonata\BlockBundle\Model\BlockManagerInterface
     */
    public function getBlockManager()
    {
        return $this->container->get('sonata.page.manager.block');
    }

    /**
     * @return \Sonata\PageBundle\Entity\BlockInteractor
     */
    public function getBlockInteractor()
    {
        return $this->container->get('sonata.page.block_interactor');
    }

    /**
     * Templating Engine Function
     */
    public function templatingEngine()
    {
        return $this->container->get('templating');
    }
}