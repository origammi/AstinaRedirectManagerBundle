<?php

namespace Astina\Bundle\RedirectManagerBundle\Controller;

use Astina\Bundle\RedirectManagerBundle\Entity\MapRepository;
use Astina\Bundle\RedirectManagerBundle\Entity\Map;
use FOS\UserBundle\Entity\Group;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Astina\Bundle\RedirectManagerBundle\Form\Type\MapFormType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;

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
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        if ($request->get('action') == 'remove') {
            return $this->redirect($this->generateUrl('armb_homepage'));
        }

        $search = $request->get('search');
        $page = (int)$request->get('page', 1);
        $pageSize = $this->container->getParameter('armb.pagesize');
        $maps = $this->getMapRepository()->search($search, $page, $pageSize);

        $groupedMaps = $this->groupMaps($maps);

        return array(
            'grouped_maps' => $groupedMaps,
            'paginator' => $maps,
            'page' => $page,
            'pageSize' => $pageSize,
            'layout' => $this->container->getParameter('armb.base_layout'),
            'search' => $search,
        );
    }

    /**
     * @param Request $request
     *
     *  @Template()
     *
     * @return array|RedirectResponse
     */
    public function newAction(Request $request)
    {
        $map = new Map();
        $http_code = $this->getParameter('armb.default_http_code');
        $map->setRedirectHttpCode($http_code);
        $form = $this->createForm(MapFormType::class, $map);

        if ($form->handleRequest($request)->isValid()) {
            if (!$this->get('armb.map_validator')->validate($map)) {
                $this->addFlash('error', 'mapping.flash.map_circular_redirect.error');
                return $this->createView($form, $map);
            }
            $em  = $this->getEm();
            $em->persist($map);
            try {
                $em->flush();
                $this->addFlash('success', 'mapping.flash.map_created.success');
            }
            catch (\Exception $e) {
                $this->addFlash('error', 'mapping.flash.map_created.error');
                return $this->redirect($this->generateUrl('armb_new_map'));
            }

            return $this->redirect($this->generateUrl('armb_homepage'));
        }

        return $this->createView($form, $map);
    }

    /**
     * @param Map $map
     *
     * @Template()
     *
     * @return array
     */
    public function editAction(Map $map, Request $request)
    {
        $form = $this->createForm(MapFormType::class, $map);

        if ($form->handleRequest($request)->isValid()) {
            if (!$this->get('armb.map_validator')->validate($map)) {
                $this->addFlash('error', 'mapping.flash.map_circular_redirect.error');
                return $this->createView($form, $map);
            }
            try {
                $em = $this->getEm();
                $em->flush();
                $this->addFlash('success', 'mapping.flash.map_updated.success');
            }
            catch (\Exception $e) {
                $this->addFlash('error', 'mapping.flash.map_updated.error');
                return $this->redirect($this->generateUrl('armb_edit_map', array('id' => $map->getId())));
            }

            return $this->redirect($this->generateUrl('armb_homepage'));
        }

        return $this->createView($form, $map);
    }

    /**
     * @param Form $form
     * @param Map $map
     * @return array
     */
    private function createView(Form $form, Map $map) {
        return array(
            'form' => $form->createView(),
            'layout' => $this->container->getParameter('armb.base_layout'),
            'map'  => $map,
        );
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
        return $this->getEm()->getRepository('AstinaRedirectManagerBundle:Map');
    }

    /**
     * Returns Doctrine's entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEm()
    {
        return $this->get('armb.em');
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function addFlash($action, $value)
    {
        $value = $this->get('translator')->trans($value, array(), 'AstinaRedirectManagerBundle');
        $this->container->get('session')->getFlashBag()->add($action, $value);
    }

    /**
     * @param Map[] $maps
     * @return array
     */
    private function groupMaps($maps)
    {
        /** @var Group[] $groups */
        $groups = $this->getEm()->getRepository('AstinaRedirectManagerBundle:Group')->findBy(array(), array('priority' => 'asc'));

        $groupedMaps = array();

        foreach ($maps as $map) {
            if (null === $map->getGroup()) {
                $groupedMaps['__none__']['maps'][] = $map;
            }
        }

        foreach ($groups as $group) {
            $groupedMaps[$group->getId()] = array('group' => $group, 'maps' => array());

            foreach ($maps as $map) {
                if ($map->getGroup() && $map->getGroup()->getId() === $group->getId()) {
                    $groupedMaps[$group->getId()]['maps'][] = $map;
                }
            }
        }

        return $groupedMaps;
    }
}
