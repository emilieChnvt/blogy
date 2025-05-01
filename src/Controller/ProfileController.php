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
    #[Route('/profile/{id}', name: 'profile', priority:  -1)]
    public function profile(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }




        return $this->render('profile/profile.html.twig', [

        ]);
    }

    #[Route('/profile/addimage', name: 'app_profile_image')]
    public function addImageProfile(
        EntityManagerInterface $manager,
        Request $request,
    ): Response {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $imageProfile = new Image();

        $imageForm = $this->createForm(ImageType::class, $imageProfile);
        $imageForm->handleRequest($request);

        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            if($this->getUser()->getImageProfile()){
                $this->getUser()->setImageProfile(null);
            }
            $imageProfile->setProfile($this->getUser());

            $manager->persist($imageProfile);
            $manager->flush();

            return $this->redirectToRoute('profile', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('profile/add_image.html.twig', [
            'imageForm' => $imageForm->createView(),
        ]);
    }
}
