<?php

namespace Astina\RedirectManagerBundle\Tests\Controller;

use Astina\RedirectManagerBundle\Tests\RedirectManagerTestHelper;

class MappingControllerTest extends RedirectManagerTestHelper
{
    public function testIndex()
    {
        $this->assertTrue(true);
    }

    public function testAddingNewRedirect()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->generateUrl('armb_new_map'));

        $form = $crawler->selectButton('Create')->form(array(
            'urlFrom' => '/test',
            'urlTo'   => 'http://www.google.com'
        ));

        $this->assertTrue(
            $client->submit($form, array(
                'urlFrom' => '/test',
                'urlTo'   => 'http://www.google.com'
            ))
            ->getResponse()
            ->isRedirect($this->generateUrl('armb_homepage'))
        );
    }
}
