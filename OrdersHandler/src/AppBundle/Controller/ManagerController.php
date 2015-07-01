<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Company;
use AppBundle\Entity\Place;
use AppBundle\Form\Type\PlaceType;
use AppBundle\Form\Type\CompanyType;
use AppBundle\Form\Type\RoleType;
use AppBundle\Form\Type\UserAlterationType;
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
        return $this->render('default/index.html.twig', [
                'errorMessages' => ['Access denied']
            ]
        );
    }

    public function usersManagerAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_MANAGER')) {
            $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
            return $this->render('AppBundle:Manager:index.html.twig', [
                'users' => $users
            ]);
        }
        return $this->render('default/index.html.twig', [
                'errorMessages' => ['Access denied']
            ]
        );
    }

    public function companiesManagerAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_MANAGER')) {
            $companies = $this->getDoctrine()->getRepository('AppBundle:Company')->findAll();
            return $this->render('AppBundle:Manager:index.html.twig', [
                'companies' => $companies
            ]);
        }
        return $this->render('default/index.html.twig', [
                'errorMessages' => ['Access denied']
            ]
        );
    }

    public function placesManagerAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_MANAGER')) {
            $places = $this->getDoctrine()->getRepository('AppBundle:Place')->findAll();
            return $this->render('AppBundle:Manager:index.html.twig', [
                'places' => $places
            ]);
        }
        return $this->render('default/index.html.twig', [
                'errorMessages' => ['Access denied']
            ]
        );
    }

    public function usersCreateManagerAction(Request $request)
    {
        $registration = new Registration();
        $form = $this->createForm(new RegistrationType(), $registration, array(
            'action' => $this->generateUrl('manager_users_create'),
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $registration = $form->getData();

                /** @var User $user */
                $user = $registration->getUser();

                /** @var EncoderFactory $factory */
                $factory = $this->get('security.encoder_factory');
                /** @var PasswordEncoderInterface $encoder */
                $encoder = $factory->getEncoder($user);

                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);

                $user->setRole($this->getDoctrine()->getEntityManager()->getRepository('AppBundle:Role')->findRoleByName(RoleType::ROLE_CUSTOMER));

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('manager');
            } else {
                return $this->render(
                    'AppBundle:User:new.html.twig', array(
                        'form' => $form->createView(),
                        'errors' => $errors = $this->get('validator')->validate($form)
                    )
                );
            }
        }

        return $this->render(
            'AppBundle:User:new.html.twig', array(
                'form' => $form->createView(),
                'errors' => null
            )
        );
    }

    public function usersShowManagerAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            try {
                throw $this->createNotFoundException('Unable to find User entity.');
            } catch (NotFoundHttpException $ex) {
                return $this->render('default/index.html.twig', array(
                        'errorMessages' => [
                            $ex->getMessage()
                        ]
                    )
                );
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
                return $this->render('default/index.html.twig', array(
                        'errorMessages' => [
                            $ex->getMessage()
                        ]
                    )
                );
            }

        }

        if ($entity->getRole()->getName() === RoleType::ROLE_MANAGER || $entity->getId() === $this->getUser()->getId()) {
            return $this->render('default/index.html.twig', array(
                    'errorMessages' => [
                        'Access denied to delete this user.'
                    ]
                )
            );
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
                    return $this->render('default/index.html.twig', array(
                            'errorMessages' => [
                                $ex->getMessage()
                            ]
                        )
                    );
                }
            }

            $deleteForm->handleRequest($request);
            if ($deleteForm->isValid()) {
                $em->remove($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('manager'));
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
                return $this->render('default/index.html.twig', array(
                        'errorMessages' => [
                            $ex->getMessage()
                        ]
                    )
                );
            }

        }

        if ($entity->getRole()->getName() === RoleType::ROLE_MANAGER || $entity->getId() === $this->getUser()->getId()) {
            return $this->render('default/index.html.twig', array(
                    'errorMessages' => [
                        'Access denied to edit this user.'
                    ]
                )
            );
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

                return $this->redirect($this->generateUrl('manager_users_show', array('id' => $id)));
            }

            return $this->render('AppBundle:User:edit.html.twig', array(
                    'user' => $entity,
                    'edit_form' => $editForm->createView()
                )
            );
        }

        return $this->render('AppBundle:User:edit.html.twig', array(
                'user' => $entity,
                'edit_form' => $editForm->createView()
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

                return $this->redirectToRoute('manager_places');
            } else {
                return $this->render(
                    'AppBundle:Place:new.html.twig', array(
                        'form' => $form->createView(),
                        'errors' => $errors = $this->get('validator')->validate($form)
                    )
                );
            }
        }

        return $this->render(
            'AppBundle:Place:new.html.twig', array(
                'form' => $form->createView(),
                'errors' => null
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
                return $this->render('default/index.html.twig', array(
                        'errorMessages' => [
                            $ex->getMessage()
                        ]
                    )
                );
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
                return $this->render('default/index.html.twig', array(
                        'errorMessages' => [
                            $ex->getMessage()
                        ]
                    )
                );
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
                    return $this->render('default/index.html.twig', array(
                            'errorMessages' => [
                                $ex->getMessage()
                            ]
                        )
                    );
                }
            }

            $deleteForm->handleRequest($request);
            if ($deleteForm->isValid()) {
                $em->remove($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('manager'));
            }
            return $this->render('AppBundle:Place:delete.html.twig', [
                    'deleteForm' => $deleteForm->createView(),
                    'place' => $entity
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
                return $this->render('default/index.html.twig', array(
                        'errorMessages' => [
                            $ex->getMessage()
                        ]
                    )
                );
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

                return $this->redirect($this->generateUrl('manager_places_show', array('id' => $id)));
            }

            return $this->render('AppBundle:Place:edit.html.twig', array(
                    'place' => $entity,
                    'edit_form' => $editForm->createView()
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

                return $this->redirectToRoute('manager_companies');
            } else {
                return $this->render(
                    'AppBundle:Company:new.html.twig', array(
                        'form' => $form->createView(),
                        'errors' => $errors = $this->get('validator')->validate($form)
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
                return $this->render('default/index.html.twig', array(
                        'errorMessages' => [
                            $ex->getMessage()
                        ]
                    )
                );
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
                return $this->render('default/index.html.twig', array(
                        'errorMessages' => [
                            $ex->getMessage()
                        ]
                    )
                );
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
                    return $this->render('default/index.html.twig', array(
                            'errorMessages' => [
                                $ex->getMessage()
                            ]
                        )
                    );
                }
            }

            $deleteForm->handleRequest($request);
            if ($deleteForm->isValid()) {
                $users = $entity->getUsers();
                foreach($users as $user){
                    $user->removeCompany($entity);
                }
                $em->remove($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('manager_companies'));
            }
            return $this->render('AppBundle:Company:delete.html.twig', [
                    'deleteForm' => $deleteForm->createView(),
                    'company' => $entity
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
                return $this->render('default/index.html.twig', array(
                        'errorMessages' => [
                            $ex->getMessage()
                        ]
                    )
                );
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

                return $this->redirect($this->generateUrl('manager_companies_show', array('id' => $id)));
            }

            return $this->render('AppBundle:Company:edit.html.twig', array(
                    'company' => $entity,
                    'edit_form' => $editForm->createView()
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