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

            return $this->redirect($this->generateUrl("astina_new_map"));
        }
    }

    /**
     * @Template()
     *
     * @param integer $id
     *
     * @return array
     */
    public function editAction($id)
    {
        $form = $this->createForm(new MapFormType());
        $map = $this->getMapRepository()->findOneById($id);

        return array(
            'form' => $form->setData($map)->createView(),
            'map'  => $map
        );
    }

    /**
     * @param integer $id
     *
     * @return RedirectResponse
     */
    public function updateAction($id)
    {
        $map = $this->getMapRepository()->findOneById($id);

        $form = $this->createForm(new MapFormType(), $map);
        $form->bind($this->getRequest());
        if ($form->isValid()) {
            $em = $this->getEm();
            $em->persist($map);
            $em->flush();

            $this->setFlash('success', 'mapping.flash.map_updated.success');

            return $this->redirect($this->generateUrl('armb_homepage'));
        } else {
            $this->setFlash('error', 'mapping.flash.map_updated.error');

            return $this->redirect($this->generateUrl("astina_edit_map", array('id' => $id)));
        }
    }

    /**
     * @param integer $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $map = $this->getMapRepository()->findOneById($id);

        if ($map) {
            $em  = $this->getEm();
            $em->remove($map);
            $em->flush();

            $this->setFlash('success', 'mapping.flash.map_deleted.success');

            return $this->redirect($this->generateUrl('armb_homepage'));
        } else {
            $this->setFlash('error', 'mapping.flash.map_deleted.error');

            return $this->redirect($this->generateUrl("armb_homepage"));
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
