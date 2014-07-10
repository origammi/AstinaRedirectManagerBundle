<?php

namespace Astina\Bundle\RedirectManagerBundle\Controller;

use Astina\Bundle\RedirectManagerBundle\Entity\MapRepository;
use Astina\Bundle\RedirectManagerBundle\Entity\Map;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Astina\Bundle\RedirectManagerBundle\Form\Type\MapFormType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MappingController
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Controller
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @author    Dražen Perić <dperic@astina.ch>
 * @copyright 2013 Astina AG (http://astina.ch)
 */
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
    public function newAction(Request $request)
    {
        $form = $this->createForm(new MapFormType());

        if ($request->getMethod() === 'POST') {
            return $this->createAction($request);
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return RedirectResponse
     */
    private function createAction(Request $request)
    {
        $map = new Map();
        $form = $this->createForm(new MapFormType(), $map);

        $form->submit($request);

        if ($form->isValid()) {
            $em  = $this->getEm();
            $em->persist($map);
            $em->flush();

            $this->addFlash('success', 'mapping.flash.map_created.success');

            return $this->redirect($this->generateUrl('armb_homepage'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @param Map $map
     *
     * @Template()
     *
     * @return array
     */
    public function editAction(Map $map)
    {
        $form = $this->createForm(new MapFormType(), $map);

        return array(
            'form' => $form->createView(),
            'map'  => $map
        );
    }

    /**
     * @param Map     $map
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function updateAction(Map $map, Request $request)
    {
        $form = $this->createForm(new MapFormType(), $map);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getEm();
            $em->persist($map);
            $em->flush();

            $this->addFlash('success', 'mapping.flash.map_updated.success');

            return $this->redirect($this->generateUrl('armb_homepage'));
        } else {
            $this->addFlash('error', 'mapping.flash.map_updated.error');

            return $this->redirect($this->generateUrl('astina_edit_map', array('id' => $map->getId())));
        }
    }

    /**
     * @param Map $map
     *
     * @return RedirectResponse
     */
    public function deleteAction(Map $map)
    {
        $em  = $this->getEm();
        $em->remove($map);
        $em->flush();

        $this->addFlash('success', 'mapping.flash.map_deleted.success');

        return $this->redirect($this->generateUrl('armb_homepage'));
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
    private function addFlash($action, $value)
    {
        $value = $this->get('translator')->trans($value, array(), 'AstinaRedirectManagerBundle');
        $this->container->get('session')->getFlashBag()->add($action, $value);
    }
}
