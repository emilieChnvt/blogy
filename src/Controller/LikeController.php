<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Post;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LikeController extends AbstractController
{
    #[Route('/post/like/{id}', name: 'post_like')]
    public function toggleLike(Post $post, LikeRepository $likeRepository, EntityManagerInterface $manager, Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return $this->json(['error' => 'User not authenticated'], 403);
        }

        $user = $this->getUser();
        $isLiked = false;

        if ($post->isLikedBy($user)) {
            $like = $likeRepository->findOneBy(['post' => $post, 'author' => $user]);
            $manager->remove($like);
        } else {
            $like = new Like();
            $like->setPost($post);
            $like->setAuthor($user);
            $manager->persist($like);
            $isLiked = true;
        }

        $manager->flush();
        return $this->json([
            'isLiked' => $isLiked,

        ]);
    }
}
