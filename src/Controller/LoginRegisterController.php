<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class LoginRegisterController extends AbstractController
{
    

    #[Route('/login-register', name: 'login-register-form')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $error = $request->getSession()->get('error');
        $request->getSession()->remove('error');

        // Chargement du formulaire d'inscription
        $user = new User();
        $registerForm = $this->createForm(RegistrationFormType::class, $user);
        $registerForm->handleRequest($request);
        $registerSuccess = false;
        $existingUserByEmail = false;
        $existingUserByName = false;

        //dd($registerForm->isValid(), $registerForm);


        if ($registerForm->isSubmitted()) {
            if($registerForm->isValid())  {

                // Vérification si l'email ou le nom existent déjà dans la base de données
                $existingUserByEmail = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
                $existingUserByName = $entityManager->getRepository(User::class)->findOneBy(['name' => $user->getName()]);

                // Si aucun doublon n'est trouvé, on continue l'inscription
                if (!$existingUserByEmail && !$existingUserByName) {
                    // Encoder le mot de passe avec le PasswordHasherInterface
                    $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                    $user->setPassword($hashedPassword);

                    // Sauvegarder l'utilisateur dans la base de données
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $registerSuccess = true;
                }
            }
        }
        
        // Afficher la page home avec les mangas
        return $this->render('login-register.html.twig', [
            'error' => $error,
            'registerForm' => $registerForm->createView(),
            'registerSuccess' => $registerSuccess,
            'mailExist' =>  $existingUserByEmail,
            'nameExist' => $existingUserByName,
        ]);
    }
}
