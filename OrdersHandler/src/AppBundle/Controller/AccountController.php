<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\RoleType;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Form\Type\RegistrationType;
use AppBundle\Form\Model\Registration;
use AppBundle\Entity\User;
use AppBundle\Entity\Role;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
                $user->setConfirmationLink($request->getSchemeAndHttpHost().'/email_confirmation/'.md5(uniqid(null, true)));

                $user->setRole($this->getDoctrine()->getEntityManager()->getRepository('AppBundle:Role')->findRoleByName(RoleType::ROLE_CUSTOMER));

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls');
                $transport->setUsername('1ochka1994@gmail.com');
                $transport->setPassword('sa375292884545');
                $mailer = Swift_Mailer::newInstance($transport);
                $message = Swift_Message::newInstance();
                $message->setSubject('Registration confirmation');
                $message->setFrom(array('1ochka1994@gmail.com' => 'Alexandr'));
                $message->setTo(array($user->getEmail()));
                $message->setBody(
                    $this->renderView(
                            'Emails/registration.html.twig',
                            [
                                'firstName' => $user->getFirstName(),
                                'surname' => $user->getSurname(),
                                'username' => $user->getUsername(),
                                'confirmationLink' => $user->getConfirmationLink()
                            ]
                        ),
                        'text/html'
                    );
                $mailer->send($message);

                return $this->render('AppBundle:account:registerSuccess.html.twig');
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

    public function emailConfirmationAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repository->findOneBy(
            array('confirmationLink' => $request->getUri())
        );
        if (!$user) {
            try {
                throw $this->createNotFoundException('This link is invalid.');
            } catch (NotFoundHttpException $ex) {
                $this->addFlash(
                    'notice',
                    $ex->getMessage()
                );
                return $this->redirectToRoute('default');
            }

        }

        $user->setIsActive(true);
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->render('AppBundle:account:registerSuccess.html.twig', array(
                'isConfirmed' => true
            )
        );
    }
}