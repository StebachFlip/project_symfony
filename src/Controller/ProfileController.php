<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Form\UserProfileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
        $success = false;
        //dd($user); 

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Sauvegarde des changements si tout est valide
                $entityManager->persist($user);
                $entityManager->flush();
                $success = true;

                $this->addFlash('success', 'Profile updated successfully!');
            } else {
                // Envoi de l'indicateur d'erreur au template
                return $this->render('profile.html.twig', [
                    'form' => $form->createView(),
                    'hasError' => true, // pour afficher une notification d'erreur
                    'user' => $user,
                    'success' => $success
                ]);
            }
        }

        return $this->render('profile.html.twig', [
            'form' => $form->createView(),
            'hasError' => false,
            'user' => $user,
            'success' => $success
        ]);
    }
}