<?php

namespace Astina\Bundle\RedirectManagerBundle\Controller;

use Astina\Bundle\RedirectManagerBundle\Entity\Group;
use Astina\Bundle\RedirectManagerBundle\Form\Type\GroupFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class GroupController extends Controller
{
    public function formAction(Request $request, Group $group = null)
    {
        if (null === $group) {
            $group = new Group();
        }

        $form = $this->createForm(new GroupFormType(), $group);

        if ($form->handleRequest($request)->isValid()) {

            $isNew = null === $group->getId();

            /** @var Session $session */
            $session = $this->get('session');
            /** @var TranslatorInterface $translator */
            $translator = $this->get('translator');

            try {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($group);
                $manager->flush();

                $session->getFlashBag()->add('success', $translator->trans($isNew ? 'group.flash.group_created.success' : 'group.flash.group_updated.success', array(), 'AstinaRedirectManagerBundle'));

            } catch (\Exception $e) {
                $session->getFlashBag()->add('error', $e->getMessage());
            }

            return $this->redirect($this->generateUrl('armb_homepage'));
        }

        return $this->render('AstinaRedirectManagerBundle:Group:form.html.twig', array(
            'group' => $group,
            'form' => $form->createView(),
        ));
    }

    public function deleteAction(Group $group)
    {
        /** @var Session $session */
        $session = $this->get('session');
        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');

        try {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($group);
            $manager->flush();
            $session->getFlashBag()->add('success', $translator->trans('group.flash.group_deleted.success', array(), 'AstinaRedirectManagerBundle'));

        } catch (\Exception $e) {
            $session->getFlashBag()->add('error', $e->getMessage());
        }

        return $this->redirect($this->generateUrl('armb_homepage'));
    }
} 