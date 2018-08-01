<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


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

    public function show(Article $article, Request $request, ObjectManager $manager){
        
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id'=> $article->getId()
            ]);
        }

        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }

    
}
 