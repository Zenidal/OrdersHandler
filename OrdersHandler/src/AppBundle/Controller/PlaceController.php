<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class PlaceController extends Controller
{
    public function getPlacesByCompanyNameAction(Request $request)
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Company');
        $company = $repository->findOneByName($request->get('name'));
        $places = $company->getPlaces();
        $result = [];
        foreach($places as $place){
            $result[] = [$place->getId(), $place->getName()];
        }
        return new Response($serializer->serialize($result, 'json'));
    }
}