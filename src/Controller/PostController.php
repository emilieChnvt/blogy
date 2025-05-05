<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\ImageType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class PostController extends AbstractController
{
    #[Route('/posts', name: 'app_posts')]
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        $pagination = $paginator->paginate(
            $postRepository->findAll(),
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('post/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/post/show/{id}', name: 'app_post_show', priority: -1)]
    public function show(Post $post, PostRepository $postRepository, EntityManagerInterface $manager, Request $request): Response
    {


        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
       $commentForm->handleRequest($request);
       if($commentForm->isSubmitted() && $commentForm->isValid()){
           $comment->setPost($post);
           $comment->setCreateAt(new \DateTime());
           $comment->setAuthor($this->getUser());
           $manager->persist($comment);
           $manager->flush();
           return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
       }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route('/post/create', name: 'app_post_create')]
    public function create(PostRepository $postRepository, EntityManagerInterface $entityManager, Request $request): Response
    {

        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_login');
        }
        $post = new Post();

        $postForm = $this->createForm(PostType::class, $post);
        $postForm->handleRequest($request);


        if($postForm->isSubmitted() && $postForm->isValid()){
            $existingPost = $postRepository->findOneBy(['title' => $post->getTitle()]);
            if($existingPost){
                return $this->redirectToRoute('app_post_create', );
            }

            $post->setAuthor($this->getUser());
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_posts');
        }
        return $this->render('post/create.html.twig', [
            'postForm' => $postForm,
        ]);

    }

    #[Route('/post/edit/{id}', name: 'app_post_edit')]
    public function edit(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$post){
            return $this->redirectToRoute('app_posts');
        }
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_login');
        }
        $postForm = $this->createForm(PostType::class, $post);
        $postForm->handleRequest($request);
        if($postForm->isSubmitted() && $postForm->isValid()){
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }
        return $this->render('post/edit.html.twig', [
            'postForm' => $postForm->createView(),
        ]);
    }

    #[Route('/post/delete/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Post $post, EntityManagerInterface $entityManager): Response
    {
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            throw new AccessDeniedException('Vous n\'avez pas les droits suffisants pour accéder à cette page.');

        }
        if($post){
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_posts');
    }

    #[Route('/post/addImage/{id}', name: 'app_post_addImage',)]
    public function addImage(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$post){
            return $this->redirectToRoute('app_posts');
        }
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_login');
        }

        $image = new Image();
        $imageForm = $this->createForm(ImageType::class, $image);
        $imageForm->handleRequest($request);
        if($imageForm->isSubmitted() && $imageForm->isValid()){

            $image->setPost($post);
            $entityManager->persist($image);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }
        return $this->render('post/image.html.twig', [
            'post' => $post,
            'imageForm' => $imageForm->createView(),
        ]);
    }

    #[Route('/post/image/delete/{id}', name: 'app_post_deleteImage')]
    public function deleteImage(Image $image, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        $post = $image->getPost();
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_login');
        }
        $entityManager->remove($image);
        $entityManager->flush();
        return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);

    }
}
