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
                $user->setConfirmationLink($this->getRequest()->getHost().'/email_confirmation?id='.md5(uniqid(null, true)));

                $user->setRole($this->getDoctrine()->getEntityManager()->getRepository('AppBundle:Role')->findRoleByName(RoleType::ROLE_CUSTOMER));

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Registration confirmation')
                    ->setFrom('send@example.com')
                    ->setTo('1ochka1994@mail.ru')
                    ->setBody(
                        $this->renderView(
                            'Emails/registration.html.twig',
                            [
                                'firstName' => $user->getFirstName(),
                                'surname' => $user->getSurname(),
                                'confirmationLink' => $user->getConfirmationLink()
                            ]
                        ),
                        'text/html'
                    );
                $this->get('mailer')->send($message);

                return $this->redirectToRoute('register_success');
            } else {
                $errors = $this->get('validator')->validate($form);
                $messages = [];
                foreach($errors as $error){
                    $messages[] = $error->getMessage();
                }
                return $this->render(
                    'AppBundle:account:register.html.twig', array(
                        'form' => $form->createView(),
                        'errorMessages' => $messages
                    )
                );
            }
        }

        return $this->render(
            'AppBundle:account:register.html.twig',[
                'form' => $form->createView()
            ]
        );
    }

    public function registerSuccessAction()
    {
        return $this->render(
            'AppBundle:account:registerSuccess.html.twig'
        );
    }
}