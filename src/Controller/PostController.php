<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Posts;
use App\Form\PostsType;
use App\Repository\PostsRepository;
use App\Services\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;

/**
 * @Route("/posts")
 */
class PostController extends AbstractController
{
    private $slugger;
    private $_em;

    public function __construct(ManagerRegistry $registry, SluggerInterface $slugger){
        $this->_em = $registry;
        $this->slugger = $slugger;
    }

    /**
     * @Route("/", name="posts_index", methods="GET")
     */
    public function index(PostsRepository $postsRepository): Response
    {
//        dd($postsRepository->findAll());
        return $this->render('posts/index.html.twig', ['posts' => $postsRepository->findAll()]);
    }

    /**
     * @Route("/new", name="posts_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $posts = new Posts();
        $form = $this->createForm(PostsType::class, $posts);


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
//            $toDelete = [' ', '\'', ',', '_', '/'];
//            $slug = str_replace($toDelete, '-', $posts->getTitle());
//            $posts->setSlug($slug);

            $brochureFile = $form->get('image')->getData();
            if($brochureFile){
                $brochureFileName = $fileUploader->upload($brochureFile);
                $posts->setImage($brochureFileName);
            }
            $posts->setSlug($this->slugger->slug($posts->getTitle())->lower());
            $posts->setPublishedAt(new \DateTime());
            $entityManager = $this->_em->getManager();
            $entityManager->persist($posts);
            $entityManager->flush();
        }

        $this->addFlash(
            'success',
            'post.created.successfully'
        );

        return $this->render('posts/new.html.twig', ['posts' => $posts, 'form' => $form->createView()]);

    }

    /**
     * @Route("/edit/{id}", name="post_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Posts $post, FileUploader $fileUploader): Response
    {

//        if ($post->getImage() != ''){
//            $post->setImage(new File($this->getParameter('brochures_directory').'/'.$post->getImage()));
//        }

        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->_em->getManager()->flush();
            return $this->redirectToRoute('posts_index');
        }

        return $this->render('posts/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="post_delete", methods="POST")
     */
    public function delete(Request $request, Posts $post): Response
    {
//        if ($this->isCsrfTokenValid('delete'.$post.id)){
            $entityManager = $this->_em->getManager();
            $entityManager->remove($post);
            $entityManager->flush();

            return $this->redirectToRoute('posts_index');
//        }else{
//            return $this->redirectToRoute('posts_delete');
//
//        }
    }

    /**
     * @Route("/{slug}", name="posts_single", methods="GET")
     */
    public function single(string $slug, PostsRepository $postsRepository): Response
    {
        return $this->render('posts/single.html.twig', ['posts' => $postsRepository->findBy(array('slug' => $slug))]);

    }

}
