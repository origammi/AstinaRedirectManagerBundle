<?php

namespace Astina\RedirectManagerBundle\Controller;

use Astina\RedirectManagerBundle\Entity\MapRepository;
use Astina\RedirectManagerBundle\Entity\Map;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Astina\RedirectManagerBundle\Form\Type\MapFormType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MappingController extends Controller
{
    /**
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        $maps = $this->getMapRepository()->findAll();

        return array(
            'maps' => $maps
        );
    }

    /**
     * @Template()
     *
     * @return array
     */
    public function newAction()
    {
        $form = $this->createForm(new MapFormType());

        return array(
            'form' => $form->createView()
        );
    }

    /**
     *
     * @return RedirectResponse
     */
    public function createAction()
    {
        $form = $this->createForm(new MapFormType());

        $form->bind($this->getRequest());

        if ($form->isValid()) {
            /** @var $map Map */
            $map = $form->getData();
            $em  = $this->getEm();
            $em->persist($map);
            $em->flush();

            $this->setFlash('success', 'mapping.flash.map_created.success');

            return $this->redirect($this->generateUrl('armb_homepage'));
        } else {
            $this->setFlash('error', 'mapping.flash.map_created.error');

            return $this->redirect($this->generateUrl("astina_rm_new_map"));
        }
    }


    /**
     * Get repository for Map entity.
     *
     * @return MapRepository
     */
    private function getMapRepository()
    {
        return $this->getDoctrine()->getRepository('AstinaRedirectManagerBundle:Map');
    }

    /**
     * Returns Doctrine's entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }
}
