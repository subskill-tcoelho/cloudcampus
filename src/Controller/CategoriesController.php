<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Posts;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use App\Services\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    private $_em;

    public function __construct(ManagerRegistry $registry){
        $this->_em = $registry;
    }

    #[Route('/categories', name: 'app_categories')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('categories/index.html.twig', ['categories' => $categoriesRepository->findAll()]);
    }




    /**
     * @Route("/categories/new", name="category_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $categories = new Categories();
        $form = $this->createForm(CategoriesType::class, $categories);


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $categories->setTitle($categories->getTitle());
            $entityManager = $this->_em->getManager();
            $entityManager->persist($categories);
            $entityManager->flush();
        }

        $this->addFlash(
            'success',
            'category.created.successfully'
        );

        return $this->render('categories/index.html.twig', ['categories' => $categories, 'form' => $form->createView()]);

    }

    /**
     * @Route("/categories/edit/{id}", name="category_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Categories $category, FileUploader $fileUploader): Response
    {

        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->_em->getManager()->flush();
            return $this->redirectToRoute('posts_index');
        }

        return $this->render('categories/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/categories/delete/{id}", name="category_delete", methods="GET")
     */
    public function delete(Request $request, Categories $category): Response
    {
        $entityManager = $this->_em->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('posts_index');

    }

}
