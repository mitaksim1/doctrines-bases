<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
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
}
