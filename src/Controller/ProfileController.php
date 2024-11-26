<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\UserProfileType;
use App\Form\AddressFormType;
use App\Entity\ProfilePicture;
use App\Entity\User;
use App\Entity\Card;
use App\Form\PasswordFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(Request $request, EntityManagerInterface $entityManager, Security $security, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $security->getUser();
        $hasError = false;
        $success = false;
        $oldPasswordInvalid = false;
        $newPasswordInvalid = false;
        //dd($user); 

        if (!$user instanceof User) {
            return $this->redirectToRoute('home');
        }
        
        // Récupération des infos bancaires
        $cards = $user->getCards()->toArray();
        //dd($cards); // Afficher les cartes

        // Chargement du formulaire de données personnelles
        $profileForm = $this->createForm(UserProfileType::class, $user);
        $profileForm->handleRequest($request);

        // Chargement du formulaire de coordonnées
        $addressForm = $this->createForm(AddressFormType::class, $user->getAddress());
        $addressForm->handleRequest($request);

        // Chargement du formulaire de changement de mot de passe
        $passwordChange = new \App\Model\PasswordChange();
        $passwordForm = $this->createForm(PasswordFormType::class, $passwordChange);
        $passwordForm->handleRequest($request);


        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Profile updated successfully!');
            $success = true;
        }

        else if ($addressForm->isSubmitted() && $addressForm->isValid()) {
            $address = $addressForm->getData();
            $address->setUser($user);

            $entityManager->persist($address);
            $entityManager->flush();
            $this->addFlash('success', 'Adresse mise à jour avec succès !');
            $success = true;
        }

        // logique de changement de mot de passe
        else if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            // Vérification de l'ancien mot de passe
            if (!$passwordHasher->isPasswordValid($user, $passwordChange->oldPassword)) {
                $oldPasswordInvalid = true;
            }

            // Vérification que les nouveaux mots de passe correspondent
            if ($passwordChange->newPassword !== $passwordChange->confirmPassword) {
                $newPasswordInvalid = true;
            }

            // Si tout est valide, changement de mot de passe
            if(!$oldPasswordInvalid || !$newPasswordInvalid) {
                $newEncodedPassword = $passwordHasher->hashPassword($user, $passwordChange->newPassword);
                $user->setPassword($newEncodedPassword);

                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');
                $success = true;
            }  
        }

        return $this->render('profile.html.twig', [
            'profileForm' => $profileForm->createView(),
            'addressForm' => $addressForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'user' => $user,
            'hasError' => $hasError,
            'success' => $success,
            'oldPassError' => $oldPasswordInvalid,
            'newPassError' => $newPasswordInvalid,
            'cards'=> $cards
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

    // Méthode pour supprimer une carte 
    #[Route('/delete-card/{id}', name: 'delete_card', methods: ['DELETE'])]
    public function deleteCard($id, Request $request, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $csrfToken = $request->get('_token');

        // Vérification du token CSRF
        if (!$this->isCsrfTokenValid('delete-card', $csrfToken)) {
            return new JsonResponse(['message' => 'Token CSRF invalide'], 400);
        }

        $user = $security->getUser();

        // Récupérer la carte
        $card = $entityManager->getRepository(Card::class)->find($id);

        // Vérification : carte existe et appartient à l'utilisateur
        if (!$card || $card->getUser() !== $user) {
            return new JsonResponse(['message' => 'Carte introuvable ou non autorisée'], 404);
        }

        // Supprimer la carte
        $entityManager->remove($card);
        $entityManager->flush();

        // Vérification après suppression
        $deletedCard = $entityManager->getRepository(Card::class)->find($id);
        if ($deletedCard) {
            return new JsonResponse(['message' => 'Erreur lors de la suppression de la carte'], 500);
        }

        return new JsonResponse(['message' => 'Carte supprimée avec succès'], 200);
    }

}