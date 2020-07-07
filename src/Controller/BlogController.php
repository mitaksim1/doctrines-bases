<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Review;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="blog")
     */
    public function index()
    {
        // On cherche tous les articles
        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $allPosts = $postRepository->findAll();

        return $this->render('blog/index.html.twig', [
            'posts' => $allPosts,
        ]);
    }

    /**
     * @Route("/post/{post}/show", name="blog_post_single")
     */
    public function postShow(Post $post) 
    {
        // dump($post);exit;
        return $this->render('blog/single_post.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/author/list", name="blog_author_list")
     */
    public function authorList() 
    {
        // On cherche tous les auteurs
        $authorRepository = $this->getDoctrine()->getRepository(Author::class);
        $allAuthors = $authorRepository->findAll();

        return $this->render('blog/author_list.html.twig', [
            'authors' => $allAuthors
        ]);
    }

    /**
     * @Route("/add_review", name="add_review", methods={"POST"})
     */
    public function reviewAdd(Request $request)
    {
        // On utilise $request pour récupérer toutes les données du formulaire
        // On crée ensuite un objet Review
        $review = new Review();

        // On lui associe les données trouvées dans le formulaire
        $review->setUsername($request->request->get('username'));
        $review->setBody($request->request->get('content'));

        // Relation entre le commentaire et l'article
        $postId = $request->request->get('post_id');
        $post = $this->getDoctrine()->getRepository(Post::class)->find($postId);

        $review->setPost($post);

        // On doit récupérer d'abord l'entity manager
        $em = $this->getDoctrine()->getManager();

        // On demande à Doctrine de persister le nouvel objet
        // la requête n'est pas exécutée juste preparée
        $em->persist($review);

        // le flush applique les requêtes qui on été préparées par Doctrine
        $em->flush();

        // On redirige l'utilisateur vers la page de l'article
        // Il faut donc préciser l'id du Post à mettre dans l'url
        return $this->redirectToRoute('blog_post_single', ['post' => $postId]);
    }

    /**
     * @Route("post/add", name="blog_post_add")
     */
    public function postAdd(Request $request)
    {
        // dump($request->getMethod());exit;
        // On teste si le controlleur est exécutée avec une méthode POST
        if ($request->getMethod() == 'POST') {
            // Si c'est le cas, on récupére les données du formulaire
            $title = $request->request->get('title');
            $body = $request->request->get('body');
            $image = $request->request->get('image'); 
            $authorId = $request->request->get('author_id');

            // Il nous faut un objet Author et non un id
            $author = $this->getDoctrine()->getRepository(Author::class)->find($authorId);

            // On crée un nouvel objet POST
            $post = new Post();

            // On ajoute à cet objet toutes les données du formulaire
            $post->setTitle($title)->setBody($body)->setImage($image)->setAuthor($author);

            // On persiste cet objet POST
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);

            // On flush
            $em->flush();

            // On redirige l'utilisateur vers la page de l'article
            return $this->redirectToRoute('blog_post_single', ['post' => $post->getId()]);
        }
           
        // Si on ne reçoit pas d'info en POST, on affiche un formulaire
        return $this->render('blog/post_add.html.twig');
    }



    /**
     *  Cette route crée un article prédéfini sans formulaire
     * @Route("/post/create", name="blog_post_create")
     */
    public function postCreate() {
        // Créer un nouvel objet Post
        $post = new Post();

        // Notre post ici est vide, il n'est pas en bdd
        $post->setTitle('Premier post, post de test');
        $post->setBody('Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis magni ipsum rem porro impedit enim possimus illum harum? Recusandae quam, possimus vitae debitis molestias deleniti quaerat, ratione quibusdam perspiciatis nostrum explicabo et dolore voluptatum quidem. Nobis ad asperiores deserunt eum!');
        $post->setNbLike(0);

        // Notre classe Post, pour la propriété author, attend un objet de la classe Author, si possible un objet existant.
        // On va donc récupérer le seul auteur crée jusqu'ici
        $author = $this->getDoctrine()->getRepository(Author::class)->find(1);

        // Ajout auteur selon son post
        $post->setAuthor($author);

        // Enregistrement des données, persist()
        $entityManager = $this->getDoctrine()->getManager();

        // On demande à Doctrine de se préparer à enregistrer ces données (persister)
        $entityManager->persist($post);

        // Les données sont persistés, on pourra alors demander à Doctrine de les "flush" (sauvegarde/mettre à jour) la bdd.
        $entityManager->flush();

        return $this->json('Article cree');
    }

    /**
     * @Route("/review/create", name="blog_review_create")
     */
     public function createReview() {
        $review = new Review();

        $review->setUsername('Donald');
        $review->setBody('Hurricane dorian looks like it will be hitting Florida late Sunday night. Be prepared and please follow state and Federal instructions, it will be a very big Hurricane, perhaps one of the biggest!');

        $postRepository = $this->getDoctrine()->getRepository(Post::class);

        // On récupère l'article qui a l'id 2
        $post = $postRepository->find(2);

        $review->setPost($post);

        // L'objet Review est prêt, on le persiste
        $em = $this->getDoctrine()->getManager();
        $em->persist($review);
        $em->flush();

        return $this->json('Review cree');
     }

}
