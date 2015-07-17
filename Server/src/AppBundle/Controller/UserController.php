<?php

namespace AppBundle\Controller;

require_once('headers/headers.php');

use AppBundle\Entity\User;
use AppBundle\Form\Type\RoleType;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    public function indexAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);
            $validator = $this->get('validator');

            $user = new User();
            $user->setUsername($data['username']);
            $user->setPassword($data['password']);
            $user->setFirstName($data['firstName']);
            $user->setSurname($data['surname']);
            $user->setEmail($data['email']);
            $user->setConfirmationLink($data['confirmationLink']);
            $user->setRole($this->getDoctrine()->getEntityManager()->getRepository('AppBundle:Role')->findRoleByName(RoleType::ROLE_CUSTOMER));
            $user->setIsActive(false);
            foreach($data['companies'] as $company){
                $id = $company['id'];
                $user->addCompany($this->getDoctrine()->getEntityManager()->getRepository('AppBundle:Company')->find($id));
            }

            $errors = $validator->validate($user);

            if (count($errors) === 0) {
                $factory = $this->get('security.encoder_factory');
                /** @var PasswordEncoderInterface $encoder */
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);

                $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls');
                $transport->setUsername('1ochka1994@gmail.com');
                $transport->setPassword('sa375292884545');
                $mailer = Swift_Mailer::newInstance($transport);
                $message = Swift_Message::newInstance();
                $message->setSubject('Registration confirmation');
                $message->setFrom(array('1ochka1994@gmail.com' => 'Alexandr'));
                $message->setTo(array($user->getEmail()));
                $message->setBody('Hello '.$user->getFirstName().' '.$user->getSurname().' If you want to confirm your registration with username '.$user->getUsername().', go to link '.$user->getConfirmationLink(),
                    'text/html'
                );
                $mailer->send($message);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $response = new Response();
                return $response->setContent(json_encode(['message' => 'User successfully registered. Check your mail to confirm registration.']), 201);
            }

            $response = new Response();
            $errorsString = null;
            foreach ($errors as $error) {
                $errorsString .= $error->getMessage().' ';
            }
            return $response->setContent(json_encode(['errorMessage' => $errorsString]), 200);
        }
        if ($request->isMethod('OPTIONS')) {
            return new Response('', 200);
        }
        return new Response('', 404);
    }

    public function emailConfirmationAction(Request $request)
    {
        if($request->isMethod('POST')){
            $response = new Response();
            $data = json_decode($request->getContent(), true);
            $repository = $this->getDoctrine()->getRepository('AppBundle:User');
            $user = $repository->findOneBy(
                array('confirmationLink' => $data['confirmationLink'])
            );
            if (!$user || $user->getIsActive()) {
                try {
                    throw $this->createNotFoundException('This link is invalid.');
                } catch (NotFoundHttpException $ex) {
                    return $response->setContent(json_encode(['errorMessage' => $ex->getMessage()]), 200);
                }

            }

            $user->setIsActive(true);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $response->setContent(json_encode(['message' => 'Account was successfully confirmed .']), 200);
        }
        if($request->isMethod('OPTIONS')){
            return new Response('', 200);
        }
        if($request->isMethod('GET')){
            $response = new Response();
            $repository = $this->getDoctrine()->getRepository('AppBundle:User');
            $link = $request->get('confirmationLink');
            $user = $repository->findOneBy(
                array('confirmationLink' => $request->get('confirmationLink'))
            );
            if (!$user || $user->getIsActive()) {
                try {
                    throw $this->createNotFoundException('This link is invalid.');
                } catch (NotFoundHttpException $ex) {
                    return $response->setContent(json_encode(['errorMessage' => $ex->getMessage()]), 200);
                }

            }

            $user->setIsActive(true);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $response->setContent(json_encode(['message' => 'Account was successfully confirmed .']), 200);
        }
        return new Response('', 404);
    }
}