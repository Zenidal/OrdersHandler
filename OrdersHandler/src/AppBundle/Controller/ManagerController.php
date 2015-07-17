<?php

namespace AppBundle\Controller;

/**
 * Note: I removed unused namespace imports - they make code a bit messy
 * Note: Try to split this controller into several smaller controllers, each for its own entity (User, Place, Company)
 */

use AppBundle\Entity\Company;
use AppBundle\Entity\Place;
use AppBundle\Form\Type\PlaceType;
use AppBundle\Form\Type\CompanyType;
use AppBundle\Form\Type\RoleType;
use AppBundle\Form\Type\UserAlterationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ManagerController extends Controller
{
    /**
     * Note: Don't hesitate to use additional methods for common logic.
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function redirectToDefault($message)
    {
        $this->addFlash('notice', $message);

        return $this->redirectToRoute('default'); // Note: usually I add one line before "return" statement to make it more visible
    }

    public function indexAction()
    {
        /**
         * Note: First check all permissions logic, show errors if needed. Then do normal processing.
         */
        if (!$this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->redirectToDefault('Access denied');
        }

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        return $this->render('AppBundle:Manager:index.html.twig', [
            'users' => $users
        ]);
    }

    public function usersManagerAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->redirectToDefault('Access denied');
        }

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        return $this->render('AppBundle:Manager:index.html.twig', [
            'users' => $users
        ]);
    }

    public function companiesManagerAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->redirectToDefault('Access denied');
        }

        $companies = $this->getDoctrine()->getRepository('AppBundle:Company')->findAll();

        return $this->render('AppBundle:Manager:index.html.twig', [
            'companies' => $companies
        ]);
    }

    public function placesManagerAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->redirectToDefault('Access denied');
        }

        $places = $this->getDoctrine()->getRepository('AppBundle:Place')->findAll();

        return $this->render('AppBundle:Manager:index.html.twig', [
            'places' => $places
        ]);
    }

    public function usersShowManagerAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var \AppBundle\Entity\User|null $entity
         */
        $entity = $em->getRepository('AppBundle:User')->find($id);

        /**
         * Note: We don't need to throw an exception here - we can just do the redirect.
         */
        if (!$entity) {
            return $this->redirectToDefault('Unable to find User entity.');
        }

        return $this->render('AppBundle:User:show.html.twig', [
            'user' => $entity
        ]);
    }

    public function usersDeleteManagerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var \AppBundle\Entity\User|null $entity
         */
        $entity = $em->getRepository('AppBundle:User')->find($id);

        /**
         * Note: We don't need to throw an exception here - we can just do the redirect.
         */
        if (!$entity) {
            return $this->redirectToDefault('Unable to find User entity.');
        }

        if ($entity->getRole()->getName() === RoleType::ROLE_MANAGER || $entity->getId() === $this->getUser()->getId()) {
            return $this->redirectToDefault('Access denied to delete this user.');
        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('manager_users_delete', array(
                'id' => $id
            )))
            ->setMethod('POST')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();

        if ($request->isMethod('POST')) {
            $deleteForm->handleRequest($request);

            if ($deleteForm->isValid()) {
                $em->remove($entity);
                $em->flush();

                return $this->redirectToDefault('User successfully deleted.');
            }

            // TODO: remove it, I leaved it just to show what I mean in my next comment
/*            return $this->render('AppBundle:User:delete.html.twig', [
                    'deleteForm' => $deleteForm->createView(),
                    'user' => $entity
                ]
            );*/
        }

        /**
         * Note: this default logic for this method - so we don't need to repeat it inside previous "if"
         */
        return $this->render('AppBundle:User:delete.html.twig', [
            'delete_form' => $deleteForm->createView(),
            'user' => $entity
        ]);
    }

    public function usersEditManagerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var \AppBundle\Entity\User|null $entity
         */
        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            return $this->redirectToDefault('Unable to find User entity.');
        }

        if ($entity->getRole()->getName() === RoleType::ROLE_MANAGER || $entity->getId() === $this->getUser()->getId()) {
            return $this->redirectToDefault('Access denied to edit this user.');
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

                return $this->redirectToDefault('User successfully edited.');
            }
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

                return $this->redirectToDefault('Place successfully created.');

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

        /**
         * Note: Use phpdoc's to describe type of dynamically created objects. And try to choose more appropriate names for vars
         *
         * @var Place|null
         */
        $place = $em->getRepository('AppBundle:Place')->find($id);

        if (!$place) {
            return $this->redirectToDefault('Unable to find Place entity.');
        }

        return $this->render('AppBundle:Place:show.html.twig', array(
                'place' => $place
            )
        );
    }

    public function placesDeleteManagerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var Place|null $place
         */
        $place = $em->getRepository('AppBundle:Place')->find($id);

        if (!$place) {
            return $this->redirectToDefault('Unable to find Place entity.');
        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('manager_places_delete', array(
                'id' => $id
            )))
            ->setMethod('POST')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();

        if ($request->isMethod('POST')) {
            // Todo: We already checked this scenario - few lines before. Here we sure that $place exists.
/*            if (!$place) {
                try {
                    throw $this->createNotFoundException('Unable to find Place entity.');
                } catch (NotFoundHttpException $ex) {
                    $this->addFlash(
                        'notice',
                        $ex->getMessage()
                    );
                    return $this->redirectToRoute('default');
                }
            }*/

            $deleteForm->handleRequest($request);

            if ($deleteForm->isValid()) {
                $em->remove($place);
                $em->flush();

                return $this->redirectToDefault('Place successfully deleted.');
            }

            return $this->render('AppBundle:Place:delete.html.twig', [
                    'deleteForm' => $deleteForm->createView(),
                    'place' => $place,
                    'errorMessages' => $this->get('validator')->validate($deleteForm)
                ]
            );
        }

        return $this->render('AppBundle:Place:delete.html.twig', [
                'delete_form' => $deleteForm->createView(),
                'place' => $place
            ]
        );
    }

    public function placesEditManagerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var Place|null $place
         */
        $place = $em->getRepository('AppBundle:Place')->find($id);

        if (!$place) {
            return $this->redirectToDefault('Unable to find Place entity.');
        }

        $editForm = $this->createForm(new PlaceType(), $place, array(
            'action' => $this->generateUrl('manager_places_edit', array('id' => $place->getId())),
            'method' => 'PUT',
        ));

        $editForm->add('submit', 'submit', array('label' => 'Update'));

        if ($request->isMethod('PUT')) {
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                $em->flush();

                return $this->redirectToDefault('Place successfully edited.');
            }

            return $this->render('AppBundle:Place:edit.html.twig', array(
                    'place' => $place,
                    'edit_form' => $editForm->createView(),
                    'errorMessages' => $this->get('validator')->validate($editForm)
                )
            );
        }

        return $this->render('AppBundle:Place:edit.html.twig', array(
                'place' => $place,
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

                return $this->redirectToDefault('Company successfully created.');

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

        /**
         * @var Company $company
         */
        $company = $em->getRepository('AppBundle:Company')->find($id);

        if (!$company) {
            return $this->redirectToDefault('Unable to find Company entity.');
        }

        return $this->render('AppBundle:Company:show.html.twig', array(
                'company' => $company
            )
        );
    }

    public function companiesDeleteManagerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var Company $company
         */
        $company = $em->getRepository('AppBundle:Company')->find($id);

        if (!$company) {
            return $this->redirectToDefault('Unable to find Company entity.');
        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('manager_companies_delete', array(
                'id' => $id
            )))
            ->setMethod('POST')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();

        if ($request->isMethod('POST')) {
            $deleteForm->handleRequest($request);

            if ($deleteForm->isValid()) {
                /**
                 * @var \AppBundle\Entity\User[] $users
                 */
                $users = $company->getUsers();

                foreach ($users as $user) {
                    // Note: enjoy the auto complete from IDE when we used phpdoc to describe type of $users. Do ctrl+click on removeCompany().
                    $user->removeCompany($company);
                }

                $em->remove($company);
                $em->flush();

                return $this->redirectToDefault('Company successfully deleted.');
            }

            return $this->render('AppBundle:Company:delete.html.twig', [
                    'deleteForm' => $deleteForm->createView(),
                    'company' => $company,
                    'errorMessages' => $this->get('validator')->validate($deleteForm)
                ]
            );
        }

        return $this->render('AppBundle:Company:delete.html.twig', [
                'delete_form' => $deleteForm->createView(),
                'company' => $company
            ]
        );
    }

    public function companiesEditManagerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var Company|null $company
         */
        $company = $em->getRepository('AppBundle:Company')->find($id);

        if (!$company) {
            return $this->redirectToDefault('Unable to find Company entity.');
        }

        $editForm = $this->createForm(new CompanyType(), $company, array(
            'action' => $this->generateUrl('manager_companies_edit', array('id' => $company->getId())),
            'method' => 'PUT',
        ));

        $editForm->add('submit', 'submit', array('label' => 'Update'));

        if ($request->isMethod('PUT')) {
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                $em->flush();

                return $this->redirectToDefault('Company successfully edited.');
            }

            return $this->render('AppBundle:Company:edit.html.twig', array(
                    'company' => $company,
                    'edit_form' => $editForm->createView(),
                    'errorMessages' => $this->get('validator')->validate($editForm)
                )
            );
        }

        return $this->render('AppBundle:Company:edit.html.twig', array(
                'company' => $company,
                'edit_form' => $editForm->createView()
            )
        );
    }
}