<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Review;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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