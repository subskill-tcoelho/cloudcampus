<?php

namespace App\Controller;

use App\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    #[Route('/api/regions', name: 'app_api')]
    public function addRegion(SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $getRegion = file_get_contents('https://geo.api.gouv.fr/regions');

        $arrRegion = $serializer->decode($getRegion, "json");

        $objRegion = $serializer->deserialize($getRegion, 'App\Entity\Region[]', 'json');

        $region_return = '';
        foreach ($objRegion as $region):
            $entityManager->persist($region);
        endforeach;

        $entityManager->flush();


        //        $entityManager = $this->getDoctrine()->getManager();
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

    #[Route('/api/regions/post', name: 'app_api_post')]
    public function postRegion(Request $request, ValidatorInterface $validator , SerializerInterface $serializer, ManagerRegistry $managerRegistry): Response
    {

        $region_json = $request->getContent();

        $entityManager = $managerRegistry->getManager();

        $region = $serializer->deserialize($region_json, Region::class, 'json');
        $errors = $validator->validate($region);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }else{
            $entityManager->persist($region);
            $entityManager->flush();


            //        $entityManager = $this->getDoctrine()->getManager();
            return new JsonResponse("success", Response::HTTP_CREATED, [], true);
        }

    }
}
