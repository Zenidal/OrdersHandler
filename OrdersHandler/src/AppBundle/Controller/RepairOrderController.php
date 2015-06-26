<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\RepairOrder;
use AppBundle\Form\Type\RepairOrderType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RepairOrderController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:RepairOrder')->findAll();
        foreach ($entities as $entity) {
            try {
                $this->denyAccessUnlessGranted('view', $entity, 'Access denied!');
            } catch (AccessDeniedException $ex) {
                $key = array_search($entity, $entities);
                unset($entities[$key]);
                $entities = array_values($entities);
            }
        }

        return $this->render('AppBundle:RepairOrder:index.html.twig', array(
                'entities' => $entities
            )
        );
    }

    public function newAction(Request $request)
    {
        $user = $this->getUser();

        $entity = new RepairOrder();
        try {
            $this->denyAccessUnlessGranted('create', $entity, 'Access denied!');
        } catch (AccessDeniedException $ex) {
            return $this->render('default/index.html.twig', array(
                    'errorMessages' => [
                        $ex->getMessage()
                    ]
                )
            );
        }
        $entity->setStatus(RepairOrderType::STATUS_OPEN);
        $entity->setUser($user);

        $form = $this->createForm($this->get('form.order.type'), $entity, array(
            'action' => $this->generateUrl('repairorder_new'),
        ));
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('repairorder_show', array('id' => $entity->getId())));
            }
        }


        return $this->render('AppBundle:RepairOrder:new.html.twig', array(
                'entity' => $entity,
                'form' => $form->createView()
            )
        );
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:RepairOrder')->find($id);

        if (!$entity) {
            try {
                throw $this->createNotFoundException('Unable to find RepairOrder entity.');
            } catch (NotFoundHttpException $ex) {
                return $this->render('default/index.html.twig', array(
                        'errorMessages' => [
                            $ex->getMessage()
                        ]
                    )
                );
            }

        }

        try {
            $this->denyAccessUnlessGranted('view', $entity, 'Access denied!');
        } catch (AccessDeniedException $ex) {
            return $this->render('default/index.html.twig', array(
                    'errorMessages' => [
                        $ex->getMessage()
                    ]
                )
            );
        }
        return $this->render('AppBundle:RepairOrder:show.html.twig', array(
                'entity' => $entity
            )
        );
    }

    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:RepairOrder')->find($id);

        try {
            $this->denyAccessUnlessGranted('edit', $entity, 'Access denied to edit orders!');
        } catch (AccessDeniedException $ex) {
            return $this->render('default/index.html.twig', array(
                    'errorMessages' => [
                        $ex->getMessage()
                    ]
                )
            );
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RepairOrder entity.');
        }

        $editForm = $this->createForm($this->get('form.order.type'), $entity, array(
            'action' => $this->generateUrl('repairorder_edit', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $editForm->add('submit', 'submit', array('label' => 'Update'));
        if ($request->isMethod('PUT')) {
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

        return $this->render('AppBundle:RepairOrder:edit.html.twig', array(
                'entity' => $entity,
                'edit_form' => $editForm->createView()
            )
        );
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:RepairOrder')->find($id);
        try {
            $this->denyAccessUnlessGranted('delete', $entity, 'Access denied to delete.');
        } catch (AccessDeniedException $ex) {
            return $this->render('default/index.html.twig', array(
                    'errorMessages' => [
                        $ex->getMessage()
                    ]
                )
            );
        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('repairorder_delete', array(
                'id' => $id
            )))
            ->setMethod('POST')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
        if ($request->isMethod('POST')) {
            try {
                $this->denyAccessUnlessGranted('edit', $entity, 'Access denied to edit orders!');
            } catch (AccessDeniedException $ex) {
                return $this->render('default/index.html.twig', array(
                        'errorMessages' => [
                            $ex->getMessage()
                        ]
                    )
                );
            }
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

        return $this->render('AppBundle:RepairOrder:delete.html.twig', array(
                'delete_form' => $deleteForm->createView()
            )
        );
    }
}
