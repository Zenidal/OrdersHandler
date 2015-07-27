<?php

namespace AppBundle\Controller;

require_once('headers/headers.php');

use AppBundle\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class CompanyController extends Controller
{
    public function indexAction()
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Company');
        $companies = $repository->findAll();
        $result = [];
        foreach ($companies as $company) {
            $result[] = ['id' => $company->getId(), 'name' => $company->getName()];
        }
        return new Response($serializer->serialize($result, 'json'));
    }

    public function placesByCompanyAction($id)
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Company');
        /** @var Company $company */
        try {
            $company = $repository->find($id);
        } catch (NotFoundHttpException $ex) {
            $response = new Response();
            return $response->setContent(json_encode(['errorMessage' => $ex->getMessage()]));
        }
        $places = $company->getPlaces();
        $result = [];
        foreach ($places as $place) {
            $result[] = ['id' => $place->getId(), 'name' => $place->getName()];
        }
        return new Response($serializer->serialize($result, 'json'));
    }
}