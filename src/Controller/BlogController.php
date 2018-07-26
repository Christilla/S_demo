<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */

    public function form(Article $article = null, Request $request, ObjectManager $manager ){

                    if (!$article) {
            $article = new Article(); 

                    }
          

        /*  $article->setTitle("Titre de l'article")
               ->setContent("le contenu de l'article");  */


        $form = $this->createFormBuilder($article)
                     ->add('title')
                     ->add('category', EntityType::class,[
                        'class' => Category::class,
                        'choice_label' => 'title'
                    ])
                     ->add('content')
                     ->add('image')

                     /* ->add('save', SubmitType::class, [
                         'label' => 'Enregistrer'
                     ]) */
                     ->getForm();  

        $form->handleRequest($request);
        /* dump($article); */

        if($form->isSubmitted()&& $form->isValid()) {
            if(!$article->getId()){
                $article->setCreateAt(new \DateTime());
            }
                
                $manager->persist($article);
                $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig',[
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
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
 