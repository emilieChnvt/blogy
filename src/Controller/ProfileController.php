<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use App\Form\ImageType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Imagine\Image\Profile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'profile')]
    public function profile(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();



        return $this->render('profile/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/addImageProfile/{id}', name: 'app_profile_addimageprofile')]
    public function addImageProfile(
        int $id,
        EntityManagerInterface $manager,
        Request $request,
        UserRepository $userRepository,
        PostRepository $postRepository
    ): Response {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
        $imageProfile = new Image();
        $imageForm = $this->createForm(ImageType::class, $imageProfile);
        $imageForm->handleRequest($request);

        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            $imageProfile->setProfile($user);

            $manager->persist($imageProfile);
            $manager->flush();

            return $this->redirectToRoute('profile', ['id' => $user->getId()]);
        }

        return $this->render('profile/add_image.html.twig', [
            'imageForm' => $imageForm->createView(),
        ]);
    }
}
