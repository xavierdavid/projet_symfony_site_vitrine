<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap', defaults: ['_format' => 'xml'])]
    public function index(Request $request, ArticleRepository $articleRepository, ProductRepository $productRepository): Response
    {
        // Récupération du nom d'hôte du site depuis l'URL
        $hostname = $request->getSchemeAndHttpHost();
        // Initialisation d'un tableau pour stocker les URL du site
        $urls = []; // Ou array()
        // On génère les URLS statiques du site (sans paramètre) et on les insère dans le tableau
        $urls[] = ['loc' => $this->generateUrl('app_home')]; // Page 'Accueil''
        $urls[] = ['loc' => $this->generateUrl('app_about')]; // Page 'A propos''
        $urls[] = ['loc' => $this->generateUrl('app_products')]; // Page 'Services''
        $urls[] = ['loc' => $this->generateUrl('app_articles')]; // Page 'Actualités'
        $urls[] = ['loc' => $this->generateUrl('app_contact')]; // Page 'Contact'
        $urls[] = ['loc' => $this->generateUrl('app_login')]; // Page 'Connexion'
        $urls[] = ['loc' => $this->generateUrl('app_legal_notice')]; // Page 'Mentions légales'
        // On génère les URLS dynamiques du site (avec paramètres) et on les insère dans le tableau
        // URLS des objets Article
        foreach($articleRepository->findAll() as $article) {
            // Pour chaque objet Article, on récupère l'image de couverture
            $coverImage = [
                'loc' => '/uploads/'.$article->getCoverImage(),
                'title' => $article->getCoverImage()
            ];
            // On génère chacune des URLS des objets Article et on les insère dans le tableau
            $urls[] = [
                'loc' => $this->generateUrl('app_article_detail', [
                    'slug' => $article->getSlug()
                    ]),
                    'image' => $coverImage,
                    'lastmod' => $article->getUpdatedAt()->format('Y-m-d')
                ];
        } 
        // URLS des objets Product
        foreach($productRepository->findAll() as $product) {
            // Pour chaque objet Product, on récupère l'image de couverture
            $coverImage = [
                'loc' => '/uploads/'.$product->getCoverImage(),
                'title' => $product->getCoverImage()
            ];
            // On génère chacune des URLS des objets Product et on les insère dans le tableau
            $urls[] = [
                'loc' => $this->generateUrl('app_product_detail', [
                    'slug' => $product->getSlug()
                    ]),
                    'image' => $coverImage,
                    'lastmod' => $product->getUpdatedAt()->format('Y-m-d')
                ];
        } 
        // Fabrication de la réponse
        $response = new Response(
            $this->renderView('sitemap/index.html.twig', [
                'urls' => $urls,
                'hostname' => $hostname
                // Ou compact('urls', 'hostname')
            ]),
            200 // Code de réussite de la requête (facultatif)
        );
        // Ajout des entêtes HTTP (notamment XML)
        $response->headers->set('Content-Type', 'text/xml');
        // Envoi de la réponse
        return $response;
    }
}
