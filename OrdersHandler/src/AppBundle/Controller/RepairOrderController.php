<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrderHistory;
use AppBundle\Form\Type\RoleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\RepairOrder;
use AppBundle\Form\Type\RepairOrderType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\DateTime;

class RepairOrderController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:RepairOrder')->findAll();
        foreach ($entities as $entity) {
            try {
                $this->denyAccessUnlessGranted('view', $entity);
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
        $history = new OrderHistory();
        $history->setDate(new \DateTime("now"));
        $history->setEstablishedStatus(RepairOrderType::STATUS_OPEN);
        $history->setRepairOrder($entity);

        $entity->setStatus(RepairOrderType::STATUS_OPEN);
        $entity->setEngineer(null);
        $entity->setStartDate(null);
        $entity->setEndDate(null);
        $entity->setUser($user);
        $entity->addOrderHistory($history);

        $form = $this->createForm($this->get('form.order.type'), $entity, array(
            'action' => $this->generateUrl('repairorder_new'),
        ));
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($history);
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
                'entity' => $entity,
                'isReopened' => $entity->getStatus() === RepairOrderType::STATUS_REOPENED,
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

    public function assignAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $repairOrder = $em->getRepository('AppBundle:RepairOrder')->find($id);
        $users = $em->getRepository('AppBundle:User')->findAll();

        if (!$repairOrder) {
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
            $this->denyAccessUnlessGranted('assign', $repairOrder, 'Access denied to assign order.');
        } catch (AccessDeniedException $ex) {
            return $this->render('default/index.html.twig', array(
                    'errorMessages' => [
                        $ex->getMessage()
                    ]
                )
            );
        }
        $engineers = [];
        foreach ($users as $user) {
            foreach ($user->getCompanies() as $company) {
                $companiesNames[] = $company->getName();
            }
            if ($user->getRole()->getName() === RoleType::ROLE_ENGINEER) {
                if (in_array($repairOrder->getCompany()->getName(), $companiesNames)) {
                    $engineers[] = $user;
                }
            }
        }

        return $this->render('AppBundle:RepairOrder:assign.html.twig', [
            'engineers' => $engineers,
            'repairOrder' => $repairOrder
        ]);
    }

    public function assignAcceptAction($orderId, $engineerId)
    {
        $em = $this->getDoctrine()->getManager();

        $repairOrder = $em->getRepository('AppBundle:RepairOrder')->find($orderId);
        $engineer = $em->getRepository('AppBundle:User')->find($engineerId);

        if (!$repairOrder) {
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
            $this->denyAccessUnlessGranted('assign', $repairOrder, 'Access denied to assign order.');
        } catch (AccessDeniedException $ex) {
            return $this->render('default/index.html.twig', array(
                    'errorMessages' => [
                        $ex->getMessage()
                    ]
                )
            );
        }
        $history = new OrderHistory();
        $history->setDate(new \DateTime("now"));
        $history->setEstablishedStatus(RepairOrderType::STATUS_ASSIGNED);
        $history->setRepairOrder($repairOrder);
        $em->persist($history);

        $repairOrder->addOrderHistory($history);
        $repairOrder->setEngineer($engineer);
        $repairOrder->setStatus(RepairOrderType::STATUS_ASSIGNED);
        $em->flush();
        return $this->redirectToRoute('repair_orders');
    }

    public function startAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repairOrder = $em->getRepository('AppBundle:RepairOrder')->find($id);

        if (!$repairOrder) {
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
            $this->denyAccessUnlessGranted('start', $repairOrder, 'Access denied to start order.');
        } catch (AccessDeniedException $ex) {
            return $this->render('default/index.html.twig', array(
                    'errorMessages' => [
                        $ex->getMessage()
                    ]
                )
            );
        }
        $history = new OrderHistory();
        $history->setDate(new \DateTime("now"));
        $history->setEstablishedStatus(RepairOrderType::STATUS_IN_PROCESS);
        $history->setRepairOrder($repairOrder);
        $em->persist($history);

        $repairOrder->addOrderHistory($history);
        $repairOrder->setStatus(RepairOrderType::STATUS_IN_PROCESS);
        $repairOrder->setStartDate(new \DateTime("now"));
        $em->flush();

        return $this->redirectToRoute('repair_orders');
    }

    public function finishAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repairOrder = $em->getRepository('AppBundle:RepairOrder')->find($id);

        if (!$repairOrder) {
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
            $this->denyAccessUnlessGranted('finish', $repairOrder, 'Access denied to finish order.');
        } catch (AccessDeniedException $ex) {
            return $this->render('default/index.html.twig', array(
                    'errorMessages' => [
                        $ex->getMessage()
                    ]
                )
            );
        }
        $history = new OrderHistory();
        $history->setDate(new \DateTime("now"));
        $history->setEstablishedStatus(RepairOrderType::STATUS_RESOLVED);
        $history->setRepairOrder($repairOrder);
        $em->persist($history);

        $repairOrder->addOrderHistory($history);
        $repairOrder->setStatus(RepairOrderType::STATUS_RESOLVED);
        $repairOrder->setEndDate(new \DateTime("now"));
        $em->flush();

        return $this->redirectToRoute('repair_orders');
    }

    public function closeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repairOrder = $em->getRepository('AppBundle:RepairOrder')->find($id);

        if (!$repairOrder) {
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
            $this->denyAccessUnlessGranted('close', $repairOrder, 'Access denied to close order.');
        } catch (AccessDeniedException $ex) {
            return $this->render('default/index.html.twig', array(
                    'errorMessages' => [
                        $ex->getMessage()
                    ]
                )
            );
        }

        $history = new OrderHistory();
        $history->setDate(new \DateTime("now"));
        $history->setEstablishedStatus(RepairOrderType::STATUS_CLOSED);
        $history->setRepairOrder($repairOrder);
        $em->persist($history);

        $repairOrder->addOrderHistory($history);
        $repairOrder->setStatus(RepairOrderType::STATUS_CLOSED);
        $repairOrder->setComment(null);
        $em->flush();

        return $this->redirectToRoute('repair_orders');
    }

    public function reopenAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $repairOrder = $em->getRepository('AppBundle:RepairOrder')->find($id);

        if (!$repairOrder) {
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
            $this->denyAccessUnlessGranted('reopen', $repairOrder, 'Access denied to reopen order.');
        } catch (AccessDeniedException $ex) {
            return $this->render('default/index.html.twig', array(
                    'errorMessages' => [
                        $ex->getMessage()
                    ]
                )
            );
        }

        $reopenForm = $this->createFormBuilder($repairOrder)
            ->setAction($this->generateUrl('repairorder_reopen', array(
                'id' => $id
            )))
            ->setMethod('PUT')
            ->add('comment', 'textarea')
            ->add('reopen', 'submit', array('label' => 'Reopen'))
            ->add('cancel', 'submit', array('label' => 'Cancel'))
            ->getForm();

        if ($request->isMethod('PUT')) {
            $reopenForm->handleRequest($request);

            if($reopenForm->get("cancel")->isClicked()){
                return $this->redirect('/repair_orders/'.$id);
            }

            if ($reopenForm->isValid()) {
                $history = new OrderHistory();
                $history->setDate(new \DateTime("now"));
                $history->setEstablishedStatus(RepairOrderType::STATUS_REOPENED);
                $history->setRepairOrder($repairOrder);
                $em->persist($history);

                $history->setRepairOrder($repairOrder);
                $repairOrder->addOrderHistory($history);
                $repairOrder->setStatus(RepairOrderType::STATUS_REOPENED);
                $repairOrder->setComment($reopenForm->get('comment')->getData());
                $em->flush();

                return $this->redirect($this->generateUrl('repairorder_show', array('id' => $id)));
            }

            return $this->render('AppBundle:RepairOrder:reopen.html.twig', array(
                    'entity' => $repairOrder,
                    'reopenForm' => $reopenForm->createView()
                )
            );
        }

        return $this->render('AppBundle:RepairOrder:reopen.html.twig', array(
                'entity' => $repairOrder,
                'reopenForm' => $reopenForm->createView()
            )
        );
    }

    public function historyAction($id)
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

        $history = $entity->getOrderHistory();

        return $this->render('AppBundle:RepairOrder:history.html.twig', array(
                'entity' => $entity,
                'history' => $history,
                'isReopened' => $entity->getStatus() === RepairOrderType::STATUS_REOPENED,
            )
        );
    }
}
