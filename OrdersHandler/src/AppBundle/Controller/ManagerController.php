<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\RoleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\RegistrationType;
use AppBundle\Form\Model\Registration;
use AppBundle\Entity\User;
use AppBundle\Entity\Role;
use Symfony\Component\HttpFoundation\Request;
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

    public function usersEditManagerAction(Request $request)
    {

    }
}