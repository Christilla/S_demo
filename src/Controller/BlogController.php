<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Article;
use App\Repository\ArticleRepository;

class BlogController extends Controller
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
      /*   $repo = $this->getDoctrine()->getRepository(Article::class); */

        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/home", name="home")
     */

    public function home() 
    {
        return $this->render('blog/home.html.twig', [
            
        ]);
        
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */

    public function show(Article $article){
        

        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }
}
 