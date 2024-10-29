<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\UserProfileType;
use App\Entity\ProfilePicture;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
        $success = false;
        //dd($user); 

        if(!$user) {
            return $this->redirectToRoute('home');
        }

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

    #[Route("/upload-profile-picture", name:"upload_profile_picture", methods: ["POST"])]
    public function uploadProfilePicture(Request $request, EntityManagerInterface $entityManager, Security $security): Response {

        $imageData = $request->request->get('image'); // L'image rognée
        $imageName = $request->request->get('imageName'); // Le nom de l'image

        // Vérifiez si l'image et le nom sont présents
        if (!$imageData || !$imageName) {
            return $this->redirectToRoute('profile');
        }

        // Convertir l'URL de données en fichier
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $decodedImage = base64_decode($imageData);

        // Définir le chemin de sauvegarde
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/pp/';
        $filePath = $uploadDir . $imageName;

        // Enregistrer l'image sur le serveur
        try {
            file_put_contents($filePath, $decodedImage);
        } catch (FileException $e) {
            return $this->json(['error' => 'Could not save image: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = $security->getUser();
        $success = true;

       // Vérifiez si l'utilisateur est une instance de User
       if ($user instanceof User) {
            // Vérifiez si une entrée pour l'image de profil existe déjà
            $profilePicture = $entityManager->getRepository(ProfilePicture::class)->findOneBy(['user' => $user]);

            if (!$profilePicture) {
                // Si aucune image de profil n'existe, en créez une nouvelle
                $profilePicture = new ProfilePicture();
                $profilePicture->setUser($user);
            }

            // Mettre à jour le chemin de la photo de profil
            $profilePicture->setPath('pp/' . $imageName);
            $entityManager->persist($profilePicture);
            $entityManager->flush();
        }

        // Retourner une réponse de succès
        return $this->json(['success' => 'Image uploaded successfully']);

        
    }

}