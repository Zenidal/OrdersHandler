<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\RepairOrder;
use AppBundle\Form\Type\RepairOrderType;

class RepairOrderController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:RepairOrder')->findAll();

        return $this->render('AppBundle:RepairOrder:index.html.twig', array(
                'entities' => $entities
            )
        );
    }

    public function createAction(Request $request)
    {
        $user = $this->getUser();

        $entity = new RepairOrder();
        $entity->setStatus(RepairOrderType::STATUS_OPEN);
        $entity->setUser($user);

        $form = $this->createForm($this->get('form.order.type'), $entity, array(
            'action' => $this->generateUrl('repairorder_create'),
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('repairorder_show', array('id' => $entity->getId())));
        }

        return $this->render('AppBundle:RepairOrder:new.html.twig', array(
                'entity' => $entity,
                'form' => $form->createView()
            )
        );
    }

    public function newAction()
    {
        $repairOrder = new RepairOrder();
        $form = $this->createForm($this->get('form.order.type'), $repairOrder, array(
            'action' => $this->generateUrl('repairorder_create'),
        ));

        return $this->render(
            'AppBundle:RepairOrder:new.html.twig',
            array(
                'entity' => $repairOrder,
                'form' => $form->createView()
            )
        );
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:RepairOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RepairOrder entity.');
        }

        return $this->render('AppBundle:RepairOrder:show.html.twig', array(
                'entity' => $entity
            )
        );
    }

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:RepairOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RepairOrder entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('AppBundle:RepairOrder:edit.html.twig', array(
                'entity' => $entity,
                'edit_form' => $editForm->createView()
            )
        );
    }

    private function createEditForm(RepairOrder $entity)
    {
        $form = $this->createForm($this->get('form.order.type'), $entity, array(
            'action' => $this->generateUrl('repairorder_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:RepairOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RepairOrder entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('repairorder_show', array('id' => $id)));
        }

        return $this->render('AppBundle:RepairOrder:edit.html.twig', array(
                'entity' => $entity,
                'edit_form' => $editForm->createView()
            )
        );
    }

    public function deleteAction($id)
    {
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:RepairOrder:delete.html.twig', array(
                'delete_form' => $deleteForm->createView()
            )
        );
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('repairorder_remove', array(
                'id' => $id
            )))
            ->setMethod('POST')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function removeAction(Request $request, $id)
    {
        $deleteForm = $this->createDeleteForm($id);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:RepairOrder')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RepairOrder entity.');
        }

        $deleteForm->handleRequest($request);
        if ($deleteForm->isValid()) {
            $em->remove($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('repair_orders'));
        }
        return $this->render('AppBundle:RepairOrder:delete.html.twig', array(
                'deleteForm' => $deleteForm->createView()
            )
        );
    }
}
