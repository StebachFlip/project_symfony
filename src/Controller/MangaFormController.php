<?php 
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Entity\Manga;
use App\Entity\Image;
use App\Form\MangaFormType;
use App\Enum\mangaStatus;
use App\Repository\OrderItemRepository;
use App\Repository\ImageRepository;
use App\Repository\MangaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MangaFormController extends AbstractController
{
    private $entityManager;
    private $mangaRepository;
    private $imageRepository;

    public function __construct(EntityManagerInterface $entityManager, MangaRepository $mangaRepository, ImageRepository $imageRepository)
    {
        $this->entityManager = $entityManager;
        $this->mangaRepository = $mangaRepository;
        $this->imageRepository = $imageRepository;
    }

    #[Route('/manga-form/{id?}', name: 'manga_form', methods: ['GET', 'POST'])]
    public function index(Request $request, Security $security, EntityManagerInterface $em, ?int $id = null): Response
    {        
        $user = $security->getUser();

        // Vérifier si l'utilisateur a les droits d'accès
        if (!$user instanceof User || !$user->getRole()) {
            return $this->redirectToRoute('home');
        }

        // Si un ID est fourni, récupérer le manga existant
        $manga = $id ? $em->getRepository(Manga::class)->find($id) : new Manga();

        // Si le manga n'existe pas (en cas d'ID invalide)
        if ($id && !$manga) {
            $this->addFlash('error', 'Le manga demandé n\'existe pas.');
            return $this->redirectToRoute('manga_list'); // Rediriger vers la liste des mangas ou une autre page appropriée
        }

        // Créer le formulaire en liant les données du manga (nouveau ou existant)
        $form = $this->createForm(MangaFormType::class, $manga);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        return $this->render('manga-form.html.twig', [
            'form' => $form->createView(),
            'manga' => $manga, // Passer le manga pour le formulaire ou d'autres besoins
        ]);
    }

    #[Route('/admin/manga/addOrUpdate', name: 'add_or_update_manga', methods: ['POST'])]
    public function addOrUpdateManga(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Récupérer l'ID du manga (si il existe) et les autres données
        $name = $request->request->get('name');
        $author = $request->request->get('author');
        $price = $request->request->get('price');
        $quantity = $request->request->get('quantity');
        $description = $request->request->get('description');
        $mangaId = $request->request->get('id');

        // Vérification de la validité des données
        if (empty($name) || empty($author) || empty($price) || empty($quantity) || empty($description)) {
            return new JsonResponse(['status' => 'error', 'message' => $name], 400);
        }

        // Si un ID est présent, on cherche à modifier un manga existant
        if ($mangaId) {
            $manga = $em->getRepository(Manga::class)->find($mangaId);
            
            if (!$manga) {
                return new JsonResponse(['status' => 'error', 'message' => 'Manga non trouvé'], 404);
            }
        } else {
            // Sinon, on crée un nouveau manga
            $manga = new Manga();
            $manga->setRating(0);
        }

        // Mise à jour des informations du manga
        $manga->setName($name);
        $manga->setAuthor($author);
        $manga->setPrice($price);
        $manga->setStock($quantity);
        $manga->setDescription($description);
        $manga->setLink(str_replace(' ', '', $name));
        $manga->setStatus(status: mangaStatus::AVAILABLE);

        // Enregistrement du manga dans la base de données
        $em->persist($manga);
        $em->flush();

        // Réponse JSON avec l'ID du manga (créé ou mis à jour)
        return new JsonResponse([
            'status' => 'success',
            'message' => $mangaId ? 'Manga mis à jour avec succès' : 'Manga créé avec succès',
            'mangaId' => $manga->getId()
        ]);
    }

    #[Route('/admin/manga/uploadImage', name: 'manga_upload_image', methods: ['POST'])]
    public function uploadImage(Request $request, EntityManagerInterface $em)
    {
        // Récupérer l'image en base64 et l'ID du manga
        $imageData = $request->request->get('image');
        $mangaId = $request->request->get('mangaId');

        // Vérifier que l'image et l'ID du manga sont présents
        if (!$imageData || !$mangaId) {
            return new JsonResponse(['status' => 'error', 'message' => 'Image ou ID manquant']);
        }

        // Décoder l'image base64
        $imageData = base64_decode($imageData);

        // Générer un nom de fichier unique pour l'image
        $imageName = 'manga_' . $mangaId . '_' . uniqid() . '.jpg';  // On peut adapter cette partie pour PNG ou autre extension

        // Définir le chemin d'enregistrement
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/images/Manga/';
        $imagePath = $uploadDir . $imageName;

        // Créer le répertoire si nécessaire
        $filesystem = new Filesystem();
        $filesystem->mkdir($uploadDir, 0777);

        // Enregistrer l'image sur le serveur
        if (file_put_contents($imagePath, $imageData)) {
            // Créer une nouvelle entité Image
            $image = new Image();
            $image->setUrl($imageName); // URL relative pour la base de données
            $image->setFormat('jpg');  // Assumer que c'est du JPG, tu peux ajuster selon ton besoin

            // Récupérer l'entité Manga depuis la base de données
            $manga = $em->getRepository(Manga::class)->find($mangaId);

            if (!$manga) {
                return new JsonResponse(['status' => 'error', 'message' => 'Manga non trouvé']);
            }

            // Vérifier si une image existe déjà pour ce manga
            if ($manga->getImage()) {
                $oldImage = $manga->getImage();
                $oldImagePath = $this->getParameter('kernel.project_dir') . '/public/' . $oldImage->getUrl();
                
                // Supprimer l'ancienne image physique si elle existe
                $filesystem = new Filesystem();
                if ($filesystem->exists($oldImagePath)) {
                    $filesystem->remove($oldImagePath);
                }

                // Supprimer l'ancienne image de la base de données
                $em->remove($oldImage);
                $em->flush();
            }

            // Associer la nouvelle image au manga
            $image->setManga($manga);

            // Sauvegarder l'image dans la base de données
            $em->persist($image);
            $em->flush(); // Sauvegarder dans la base de données

            // Retourner une réponse JSON indiquant le succès
            return new JsonResponse([
                'status' => 'success',
                'message' => 'Image sauvegardée avec succès',
                'imagePath' => $imagePath
            ]);
        }

        // Si l'enregistrement échoue
        return new JsonResponse(['status' => 'error', 'message' => 'Erreur lors de l\'enregistrement de l\'image']);
    }



    #[Route('/delete-manga/{id}', name: 'delete_manga', methods: ['POST', 'DELETE'])]
    public function deleteManga(int $id, MangaRepository $mangaRepository, OrderItemRepository $orderItemRepository, Request $request, EntityManagerInterface $entityManager, SessionInterface $session)
    {
        // Vérifier si le manga existe
        $manga = $mangaRepository->find($id);

        if (!$manga) {
            $this->addFlash('error', 'Le manga n\'existe pas.');
            $session->set('error', "Le manga n'existe pas");
            return $this->redirectToRoute('admin_pannel'); // Redirige vers la liste des mangas
        }

        // Vérifier si le manga est associé à une commande dans OrderItem
        $orderItems = $orderItemRepository->findBy(['manga' => $manga]); // On suppose qu'il y a une relation entre OrderItem et Manga

        if (count($orderItems) > 0) {
            // Le manga est lié à une commande, donc on ne le supprime pas
            $this->addFlash('error', 'Ce manga est associé à une commande et ne peut pas être supprimé.');
            $session->set('error', "Ce manga est associé à une commande et ne peut pas être supprimé.");
            return $this->redirectToRoute('admin_pannel');
        }

        $filesystem = new Filesystem();
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/images/Manga/' . $manga->getImage()->getUrl();

        if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
        }

        $image = $manga->getImage();


        // Supprimer le manga
        $entityManager->remove($image);
        $entityManager->remove($manga);
        $entityManager->flush();

        // Ajout d'un message de succès
        $this->addFlash('success', 'Le manga a été supprimé avec succès.');
        $session->set('suppr', true);
        $session->set('error', "");
        

        // Rediriger vers la liste des mangas après la suppression
        return $this->redirectToRoute('admin_pannel');
    }
}
