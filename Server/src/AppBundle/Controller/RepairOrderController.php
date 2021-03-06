<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrderHistory;
use AppBundle\Form\Type\RoleType;
use Doctrine\ORM\EntityNotFoundException;
use Swift_Mailer;
use Swift_Message;
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
            $this->addFlash(
                'notice',
                $ex->getMessage()
            );
            return $this->redirectToRoute('default');
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
                $this->addFlash(
                    'notice',
                    'Order was successfully created.'
                );

                return $this->redirectToRoute('repair_orders');
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
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }
        }

        try {
            $this->denyAccessUnlessGranted('view', $entity, 'Access denied!');
        } catch (AccessDeniedException $ex) {
            $this->addFlash(
                'notice',
                $ex->getMessage()
            );
            return $this->redirectToRoute('default');
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
            $this->addFlash(
                'notice',
                $ex->getMessage()
            );
            return $this->redirectToRoute('default');
        }

        if (!$entity) {
            try {
                throw $this->createNotFoundException('Unable to find RepairOrder entity.');
            } catch (NotFoundHttpException $ex) {
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
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
                $this->addFlash(
                    'notice',
                    'Order was successfully edited.'
                );

                return $this->redirectToRoute('repair_orders');
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
            $this->addFlash(
                'notice',
                $ex->getMessage()
            );
            return $this->redirectToRoute('default');
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
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }
            if (!$entity) {
                try {
                    throw $this->createNotFoundException('Unable to find RepairOrder entity.');
                } catch (EntityNotFoundException $ex) {
                    $this->addFlash(
                        'notice',
                        $ex->getMessage()
                    );
                    return $this->redirectToRoute('default');
                }
            }

            $deleteForm->handleRequest($request);
            if ($deleteForm->isValid()) {
                $em->remove($entity);
                $em->flush();
                $this->addFlash(
                    'notice',
                    'Order was successfully deleted.'
                );

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
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }

        }

        try {
            $this->denyAccessUnlessGranted('assign', $repairOrder, 'Access denied to assign order.');
        } catch (AccessDeniedException $ex) {
            $this->addFlash(
                'notice',
                $ex->getMessage()
            );
            return $this->redirectToRoute('default');
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
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }

        }

        try {
            $this->denyAccessUnlessGranted('assign', $repairOrder, 'Access denied to assign order.');
        } catch (AccessDeniedException $ex) {
            $this->addFlash(
                'notice',
                $ex->getMessage()
            );
            return $this->redirectToRoute('default');
        }

        $repairOrder->setEngineer($engineer);
        $this->changeStatus($repairOrder, RepairOrderType::STATUS_ASSIGNED);
        $em->flush();
        $this->addFlash(
            'notice',
            'The status of the order has changed to ' . RepairOrderType::getStatusByValue(RepairOrderType::STATUS_ASSIGNED) . '.'
        );
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
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }

        }

        try {
            $this->denyAccessUnlessGranted('start', $repairOrder, 'Access denied to start order.');
        } catch (AccessDeniedException $ex) {
            $this->addFlash(
                'notice',
                $ex->getMessage()
            );
            return $this->redirectToRoute('default');
        }
        $this->changeStatus($repairOrder, RepairOrderType::STATUS_IN_PROCESS);

        $repairOrder->setStartDate(new \DateTime("now"));
        $em->flush();
        $this->addFlash(
            'notice',
            'The status of the order has changed to ' . RepairOrderType::getStatusByValue(RepairOrderType::STATUS_IN_PROCESS) . '.'
        );

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
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }
        }

        try {
            $this->denyAccessUnlessGranted('finish', $repairOrder, 'Access denied to finish order.');
        } catch (AccessDeniedException $ex) {
            $this->addFlash(
                'notice',
                $ex->getMessage()
            );
            return $this->redirectToRoute('default');
        }
        $this->changeStatus($repairOrder, RepairOrderType::STATUS_RESOLVED);
        $repairOrder->setEndDate(new \DateTime("now"));
        $em->flush();
        $this->addFlash(
            'notice',
            'The status of the order has changed to ' . RepairOrderType::getStatusByValue(RepairOrderType::STATUS_RESOLVED) . '.'
        );

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
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }
        }

        try {
            $this->denyAccessUnlessGranted('close', $repairOrder, 'Access denied to close order.');
        } catch (AccessDeniedException $ex) {
            $this->addFlash(
                'notice',
                $ex->getMessage()
            );
            return $this->redirectToRoute('default');
        }
        $this->changeStatus($repairOrder, RepairOrderType::STATUS_CLOSED);
        $repairOrder->setComment(null);
        $em->flush();
        $this->addFlash(
            'notice',
            'The status of the order has changed to ' . RepairOrderType::getStatusByValue(RepairOrderType::STATUS_CLOSED) . '.'
        );

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
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }
        }

        try {
            $this->denyAccessUnlessGranted('reopen', $repairOrder, 'Access denied to reopen order.');
        } catch (AccessDeniedException $ex) {
            $this->addFlash(
                'notice',
                $ex->getMessage()
            );
            return $this->redirectToRoute('default');
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

            if ($reopenForm->get("cancel")->isClicked()) {
                return $this->redirect('/repair_orders/' . $id);
            }

            if ($reopenForm->isValid()) {
                $this->changeStatus($repairOrder, RepairOrderType::STATUS_REOPENED);
                $repairOrder->setComment($reopenForm->get('comment')->getData());
                $em->flush();
                $this->addFlash(
                    'notice',
                    'The status of the order has changed to ' . RepairOrderType::getStatusByValue(RepairOrderType::STATUS_REOPENED) . '.'
                );

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
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }
        }

        try {
            $this->denyAccessUnlessGranted('view', $entity, 'Access denied!');
        } catch (AccessDeniedException $ex) {
            $this->addFlash(
                'notice',
                $ex->getMessage()
            );
            return $this->redirectToRoute('default');
        }

        $history = $entity->getOrderHistory();

        return $this->render('AppBundle:RepairOrder:history.html.twig', array(
                'entity' => $entity,
                'history' => $history,
                'isReopened' => $entity->getStatus() === RepairOrderType::STATUS_REOPENED,
            )
        );
    }

    private function changeStatus(RepairOrder $repairOrder, $status)
    {
        $history = new OrderHistory();
        $history->setDate(new \DateTime("now"));
        $history->setEstablishedStatus($status);
        $history->setRepairOrder($repairOrder);
        $em = $this->getDoctrine()->getManager();
        $em->persist($history);

        $history->setRepairOrder($repairOrder);
        $repairOrder->addOrderHistory($history);
        $repairOrder->setStatus($status);

        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls');
        $transport->setUsername('1ochka1994@gmail.com');
        $transport->setPassword('sa375292884545');
        $mailer = Swift_Mailer::newInstance($transport);
        $message = Swift_Message::newInstance();
        $message->setSubject('Order status changed');
        $message->setFrom(['1ochka1994@gmail.com' => 'Alexandr']);
        $message->setBody(
            $this->renderView(
                'Emails/notifications.html.twig',
                [
                    'order' => $repairOrder
                ]
            ),
            'text/html'
        );

        $mails = [];
        switch ($status) {
            case 1:
                $repository = $this->getDoctrine()->getRepository('AppBundle:User');
                $managerRole = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneBy(
                    [
                        'name' => RoleType::ROLE_MANAGER
                    ]
                );
                $managers = $repository->findBy(
                    array('role' => $managerRole->getId())
                );
                foreach ($managers as $manager) {
                    $mails[] = $manager->getEmail();
                }
                break;
            case 2:
                if (is_object($repairOrder->getEngineer())) {
                    $mails[] = $repairOrder->getEngineer()->getEmail();
                }
                break;
            case 3:
                $mails[] = $repairOrder->getUser()->getEmail();
                break;
            case 4:
                $repository = $this->getDoctrine()->getRepository('AppBundle:User');
                $managerRole = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneBy(
                    [
                        'name' => RoleType::ROLE_MANAGER
                    ]
                );
                $managers = $repository->findBy(
                    array('role' => $managerRole->getId())
                );
                foreach ($managers as $manager) {
                    $mails[] = $manager->getEmail();
                }
                $mails[] = $repairOrder->getUser()->getEmail();
                break;
            case 5:
                $repository = $this->getDoctrine()->getRepository('AppBundle:User');
                $managerRole = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneBy(
                    [
                        'name' => RoleType::ROLE_MANAGER
                    ]
                );
                $managers = $repository->findBy(
                    array('role' => $managerRole->getId())
                );
                $mails = [];
                foreach ($managers as $manager) {
                    $mails[] = $manager->getEmail();
                }
                $mails[] = $repairOrder->getUser()->getEmail();
                break;
            case 6:
                $repository = $this->getDoctrine()->getRepository('AppBundle:User');
                $managerRole = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneBy(
                    [
                        'name' => RoleType::ROLE_MANAGER
                    ]
                );
                $managers = $repository->findBy(
                    array('role' => $managerRole->getId())
                );
                $mails = [];
                foreach ($managers as $manager) {
                    $mails[] = $manager->getEmail();
                }
                $mails[] = $repairOrder->getUser()->getEmail();
                if (is_object($repairOrder->getEngineer())) {
                    $mails[] = $repairOrder->getEngineer()->getEmail();
                }
                break;
        }
        $message->setTo(
            $mails
        );
        $mailer->send($message);
    }
}
