<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LanguageController extends AbstractController
{

    #[Route('/change-language/{lang}', name: 'app_change_language')]
    public function changeLanguage(Request $request, string $lang): RedirectResponse
    {
        // Stocker la langue dans la session
        $request->getSession()->set('_locale', $lang);

        // Rediriger vers la page d'origine ou la page d'accueil si aucune page d'origine
        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?? '/');
    }
}