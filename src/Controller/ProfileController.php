<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\UserProfileType;
use App\Form\AddressFormType;
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
        $hasError = false;
        $success = false;
        //dd($user); 

        if (!$user instanceof User) {
            return $this->redirectToRoute('home');
        }

        $profileForm = $this->createForm(UserProfileType::class, $user);
        $profileForm->handleRequest($request);

        $addressForm = $this->createForm(AddressFormType::class, $user->getAddress());
        $addressForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Profile updated successfully!');
            $success = true;
        }

        if ($addressForm->isSubmitted() && $addressForm->isValid()) {
            $address = $addressForm->getData();
            $address->setUser($user);

            $entityManager->persist($address);
            $entityManager->flush();
            $this->addFlash('success', 'Adresse mise à jour avec succès !');
            $success = true;
        }

        return $this->render('profile.html.twig', [
            'profileForm' => $profileForm->createView(),
            'addressForm' => $addressForm->createView(),
            'user' => $user,
            'hasError' => $hasError,
            'success' => $success,
        ]);
    }

    // Méthode pour uploader la photo de profil
    #[Route("/upload-profile-picture", name:"upload_profile_picture", methods: ["POST"])]
    public function uploadProfilePicture(Request $request, EntityManagerInterface $entityManager, Security $security): Response {

        $imageData = $request->request->get('image');
        $imageName = $request->request->get('imageName');

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

       if ($user instanceof User) {
            $profilePicture = $entityManager->getRepository(ProfilePicture::class)->findOneBy(['user' => $user]);

            if (!$profilePicture) {
                $profilePicture = new ProfilePicture();
                $profilePicture->setUser($user);
            }

            $profilePicture->setPath('pp/' . $imageName);
            $entityManager->persist($profilePicture);
            $entityManager->flush();
        }

        return $this->json(['success' => 'Image uploaded successfully']);
    }
}