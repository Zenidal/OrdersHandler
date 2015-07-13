<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Company;
use AppBundle\Entity\Place;
use AppBundle\Form\Type\PlaceType;
use AppBundle\Form\Type\CompanyType;
use AppBundle\Form\Type\RoleType;
use AppBundle\Form\Type\UserAlterationType;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\RegistrationType;
use AppBundle\Form\Model\Registration;
use AppBundle\Entity\User;
use AppBundle\Entity\Role;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class ManagerController extends Controller
{
    public function indexAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_MANAGER')) {
            $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
            return $this->render('AppBundle:Manager:index.html.twig', [
                'users' => $users
            ]);
        }
        $this->addFlash(
            'notice',
            'Access denied'
        );
        return $this->redirectToRoute('default');
    }

    public function usersManagerAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_MANAGER')) {
            $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
            return $this->render('AppBundle:Manager:index.html.twig', [
                'users' => $users
            ]);
        }
        $this->addFlash(
            'notice',
            'Access denied'
        );
        return $this->redirectToRoute('default');
    }

    public function companiesManagerAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_MANAGER')) {
            $companies = $this->getDoctrine()->getRepository('AppBundle:Company')->findAll();
            return $this->render('AppBundle:Manager:index.html.twig', [
                'companies' => $companies
            ]);
        }
        $this->addFlash(
            'notice',
            'Access denied'
        );
        return $this->redirectToRoute('default');
    }

    public function placesManagerAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_MANAGER')) {
            $places = $this->getDoctrine()->getRepository('AppBundle:Place')->findAll();
            return $this->render('AppBundle:Manager:index.html.twig', [
                'places' => $places
            ]);
        }
        $this->addFlash(
            'notice',
            'Access denied'
        );
        return $this->redirectToRoute('default');
    }

    public function usersShowManagerAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            try {
                throw $this->createNotFoundException('Unable to find User entity.');
            } catch (NotFoundHttpException $ex) {
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }

        }
        return $this->render('AppBundle:User:show.html.twig', array(
                'user' => $entity
            )
        );
    }

    public function usersDeleteManagerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            try {
                throw $this->createNotFoundException('Unable to find User entity.');
            } catch (NotFoundHttpException $ex) {
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }

        }

        if ($entity->getRole()->getName() === RoleType::ROLE_MANAGER || $entity->getId() === $this->getUser()->getId()) {
            $this->addFlash('notice', 'Access denied to delete this user.');
            return $this->redirectToRoute('default');
        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('manager_users_delete', array(
                'id' => $id
            )))
            ->setMethod('POST')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();

        if ($request->isMethod('POST')) {
            if (!$entity) {
                try {
                    throw $this->createNotFoundException('Unable to find User entity.');
                } catch (NotFoundHttpException $ex) {
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
                    'User successfully deleted.'
                );
                return $this->redirectToRoute('default');
            }
            return $this->render('AppBundle:User:delete.html.twig', [
                    'deleteForm' => $deleteForm->createView(),
                    'user' => $entity
                ]
            );
        }

        return $this->render('AppBundle:User:delete.html.twig', [
                'delete_form' => $deleteForm->createView(),
                'user' => $entity
            ]
        );
    }

    public function usersEditManagerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            try {
                throw $this->createNotFoundException('Unable to find User entity.');
            } catch (NotFoundHttpException $ex) {
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }

        }

        if ($entity->getRole()->getName() === RoleType::ROLE_MANAGER || $entity->getId() === $this->getUser()->getId()) {
            $this->addFlash(
                'notice',
                'Access denied to edit this user.'
            );
            return $this->redirectToRoute('default');
        }

        $editForm = $this->createForm(new UserAlterationType(), $entity, array(
            'action' => $this->generateUrl('manager_users_edit', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $editForm->add('submit', 'submit', array('label' => 'Update'));
        if ($request->isMethod('PUT')) {
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                $em->flush();
                $this->addFlash(
                    'notice',
                    'User successfully edited.'
                );
                return $this->redirectToRoute('default');
            }

            return $this->render('AppBundle:User:edit.html.twig', array(
                    'user' => $entity,
                    'edit_form' => $editForm->createView(),
                    'errorMessages' => $this->get('validator')->validate($editForm)
                )
            );
        }

        return $this->render('AppBundle:User:edit.html.twig', array(
                'user' => $entity,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    public function placesCreateManagerAction(Request $request)
    {
        $form = $this->createForm(new PlaceType(), new Place(), array(
            'action' => $this->generateUrl('manager_places_create'),
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $place = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($place);
                $em->flush();
                $this->addFlash(
                    'notice',
                    'Place successfully created.'
                );
                return $this->redirectToRoute('default');
            } else {
                return $this->render(
                    'AppBundle:Place:new.html.twig', array(
                        'form' => $form->createView(),
                        'errorMessages' => $this->get('validator')->validate($form)
                    )
                );
            }
        }

        return $this->render(
            'AppBundle:Place:new.html.twig', array(
                'form' => $form->createView()
            )
        );
    }

    public function placesShowManagerAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Place')->find($id);

        if (!$entity) {
            try {
                throw $this->createNotFoundException('Unable to find Place entity.');
            } catch (NotFoundHttpException $ex) {
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }

        }
        return $this->render('AppBundle:Place:show.html.twig', array(
                'place' => $entity
            )
        );
    }

    public function placesDeleteManagerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Place')->find($id);

        if (!$entity) {
            try {
                throw $this->createNotFoundException('Unable to find Place entity.');
            } catch (NotFoundHttpException $ex) {
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }

        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('manager_places_delete', array(
                'id' => $id
            )))
            ->setMethod('POST')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();

        if ($request->isMethod('POST')) {
            if (!$entity) {
                try {
                    throw $this->createNotFoundException('Unable to find Place entity.');
                } catch (NotFoundHttpException $ex) {
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
                    'Place successfully deleted.'
                );
                return $this->redirectToRoute('default');
            }
            return $this->render('AppBundle:Place:delete.html.twig', [
                    'deleteForm' => $deleteForm->createView(),
                    'place' => $entity,
                    'errorMessages' => $this->get('validator')->validate($deleteForm)
                ]
            );
        }

        return $this->render('AppBundle:Place:delete.html.twig', [
                'delete_form' => $deleteForm->createView(),
                'place' => $entity
            ]
        );
    }

    public function placesEditManagerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Place')->find($id);

        if (!$entity) {
            try {
                throw $this->createNotFoundException('Unable to find Place entity.');
            } catch (NotFoundHttpException $ex) {
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }

        }

        $editForm = $this->createForm(new PlaceType(), $entity, array(
            'action' => $this->generateUrl('manager_places_edit', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $editForm->add('submit', 'submit', array('label' => 'Update'));
        if ($request->isMethod('PUT')) {
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                $em->flush();
                $this->addFlash(
                    'notice',
                    'Place successfully edited.'
                );
                return $this->redirectToRoute('default');
            }

            return $this->render('AppBundle:Place:edit.html.twig', array(
                    'place' => $entity,
                    'edit_form' => $editForm->createView(),
                    'errorMessages' => $this->get('validator')->validate($editForm)
                )
            );
        }

        return $this->render('AppBundle:Place:edit.html.twig', array(
                'place' => $entity,
                'edit_form' => $editForm->createView()
            )
        );
    }

    public function companiesCreateManagerAction(Request $request)
    {
        $form = $this->createForm(new CompanyType(), new Company(), array(
            'action' => $this->generateUrl('manager_companies_create'),
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $company = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();
                $this->addFlash(
                    'notice',
                    'Company successfully created.'
                );
                return $this->redirectToRoute('default');
            } else {
                return $this->render(
                    'AppBundle:Company:new.html.twig', array(
                        'form' => $form->createView(),
                        'errorMessages' => $this->get('validator')->validate($form)
                    )
                );
            }
        }

        return $this->render(
            'AppBundle:Company:new.html.twig', array(
                'form' => $form->createView(),
                'errors' => null
            )
        );
    }

    public function companiesShowManagerAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Company')->find($id);

        if (!$entity) {
            try {
                throw $this->createNotFoundException('Unable to find Company entity.');
            } catch (NotFoundHttpException $ex) {
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }

        }
        return $this->render('AppBundle:Company:show.html.twig', array(
                'company' => $entity
            )
        );
    }

    public function companiesDeleteManagerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Company')->find($id);

        if (!$entity) {
            try {
                throw $this->createNotFoundException('Unable to find Company entity.');
            } catch (NotFoundHttpException $ex) {
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }

        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('manager_companies_delete', array(
                'id' => $id
            )))
            ->setMethod('POST')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();

        if ($request->isMethod('POST')) {
            if (!$entity) {
                try {
                    throw $this->createNotFoundException('Unable to find Company entity.');
                } catch (NotFoundHttpException $ex) {
                    $this->addFlash(
                        'notice',
                        $ex->getMessage()
                    );
                    return $this->redirectToRoute('default');
                }
            }

            $deleteForm->handleRequest($request);
            if ($deleteForm->isValid()) {
                $users = $entity->getUsers();
                foreach ($users as $user) {
                    $user->removeCompany($entity);
                }
                $em->remove($entity);
                $em->flush();
                $this->addFlash(
                    'notice',
                    'Company successfully deleted.'
                );
                return $this->redirectToRoute('default');
            }
            return $this->render('AppBundle:Company:delete.html.twig', [
                    'deleteForm' => $deleteForm->createView(),
                    'company' => $entity,
                    'errorMessages' => $this->get('validator')->validate($deleteForm)
                ]
            );
        }

        return $this->render('AppBundle:Company:delete.html.twig', [
                'delete_form' => $deleteForm->createView(),
                'company' => $entity
            ]
        );
    }

    public function companiesEditManagerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Company')->find($id);

        if (!$entity) {
            try {
                throw $this->createNotFoundException('Unable to find Company entity.');
            } catch (NotFoundHttpException $ex) {
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }

        }

        $editForm = $this->createForm(new CompanyType(), $entity, array(
            'action' => $this->generateUrl('manager_companies_edit', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $editForm->add('submit', 'submit', array('label' => 'Update'));
        if ($request->isMethod('PUT')) {
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                $em->flush();
                $this->addFlash(
                    'notice',
                    'Company successfully edited.'
                );
                return $this->redirectToRoute('default');
            }

            return $this->render('AppBundle:Company:edit.html.twig', array(
                    'company' => $entity,
                    'edit_form' => $editForm->createView(),
                    'errorMessages' => $this->get('validator')->validate($editForm)
                )
            );
        }

        return $this->render('AppBundle:Company:edit.html.twig', array(
                'company' => $entity,
                'edit_form' => $editForm->createView()
            )
        );
    }
}