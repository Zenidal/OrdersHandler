<?php

namespace AppBundle\Controller;

require_once('headers/headers.php');

use AppBundle\Entity\Token;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class AuthorizationController extends Controller
{
    public function indexAction(Request $request)
    {
        try {
            $username = null;
            $password = null;
            if($request->isMethod('GET')){
                $username = $request->get('username');
                $password = $request->get('password');
            }
            if($request->isMethod('POST')){
                $data = json_decode($request->getContent(), true);
                $username = $data['username'];
                $password = $data['password'];
            }

            if (!isset($username) || !isset($password)) {
                throw new BadRequestHttpException("You must pass username and password fields");
            }

            $em = $this->getDoctrine()->getManager();
            /** @var UserRepository $repository */
            $repository = $em->getRepository('AppBundle\Entity\User');
            $user = $repository->loadUserByUsername($username);

            if (!$user instanceof User) {
                throw new AccessDeniedHttpException("No matching user account found");
            }

            $encoderFactory = $this->get('security.encoder_factory');
            /** @var PasswordEncoderInterface $encoder */
            $encoder = $encoderFactory->getEncoder($user);

            $encodedPassword = $encoder->encodePassword($password, $user->getSalt());

            if ($encodedPassword != $user->getPassword()) {
                throw new AccessDeniedHttpException("Bad credentials.");
            }

            $token = $user->getToken();
            if(!$token){
                $token = new Token();
                $token->setValue($this->generateApiKey());
                $token->setUser($user);
                $user->setToken($token);
                $em->persist($token);
                $em->flush();
            }
            else {
                $token->setValue($this->generateApiKey());
                $token->setUser($user);
                $user->setToken($token);
                $em->flush();
            }
            $response = new Response();
            return $response->setContent(json_encode(['apiKey' => $token->getValue()]));
        } catch (\Exception $ex) {
            $response = new Response();
            return $response->setContent(json_encode([
                'errorMessage' => $ex->getMessage()
            ]));
        }
    }

    private function generateApiKey()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $apiKey = null;
        for ($i = 0; $i < 64; $i++) {
            $apiKey .= $characters[rand(0, strlen($characters) - 1)];
        }
        return base64_encode(sha1(uniqid('ue' . rand(rand(), rand())) . $apiKey));
    }
}