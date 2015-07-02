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

class AccountController extends Controller
{
    public function registerAction(Request $request)
    {
        $registration = new Registration();
        $form = $this->createForm(new RegistrationType(), $registration, array(
            'action' => $this->generateUrl('account_register'),
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

                return $this->redirectToRoute('profile');
            } else {
                $errors = $this->get('validator')->validate($form);
                $messages = [];
                foreach($errors as $error){
                    $messages[] = $error->getMessage();
                }
                return $this->render(
                    'AppBundle:account:register.html.twig', array(
                        'form' => $form->createView(),
                        'messages' => $messages
                    )
                );
            }
        }

        return $this->render(
            'AppBundle:account:register.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }
}