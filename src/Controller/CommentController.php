<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{
    #[Route('/comment/delete/{id}', name: 'app_comment_delete')]
    public function delete(Comment $comment, EntityManagerInterface $manager): Response
    {
        if($comment){
            $manager->remove($comment);
            $manager->flush();
        }

        return $this->redirectToRoute('app_post_show', ['id' => $comment->getPost()->getId()]);
    }

    #[Route('/comment/edit/{id}', name: 'app_comment_edit')]
    public function edit(Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        if($comment){
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $manager->persist($comment);
                $manager->flush();
                return $this->redirectToRoute('app_post_show', ['id' => $comment->getPost()->getId()]);
            }
            return $this->render('comment/edit.html.twig', [
                'form' => $form,
            ]);
        }
    }
}
