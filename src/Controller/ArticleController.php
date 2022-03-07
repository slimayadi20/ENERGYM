<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;

use App\Form\ArticleType;
use App\Form\CommentaireType;

use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Snipe\BanBuilder\CensorWords;


/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/admin/display", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/client/display", name="article_client_index", methods={"GET"})
     */
    public function indexFront(ArticleRepository $articleRepository): Response
    {

        return $this->render('article_front/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }


    /**
     * @Route("/client/display/{id}", name="article_client_show", methods={"GET","POST"})
     */
    public function showFront(Article $article,Request $request,CommentaireRepository $commentaireRepository): Response
    {
        $mostCommentedArticles = $commentaireRepository->mostCommentedArticle();

        $comment1 = new Commentaire();
        $comment1->setArticle($article);
        $user = $this->getUser();

        $comment1->setUser($user);
        $comment1->setDateCreation(new \DateTime('now'));
        $form = $this->createForm(CommentaireType::class,$comment1 );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contenuComment = $form->getData()->getContenu();
            $censor = new CensorWords;
            $badwords = $censor->setDictionary('fr');
            $cleanedComment = $censor->censorString($contenuComment);
            $comment1->setContenu($cleanedComment['clean']);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment1);
            $entityManager->flush();

            return $this->redirectToRoute('article_client_show',['id'=>$article->getId()], Response::HTTP_SEE_OTHER);
        }
        $comments = $this->getDoctrine()->getRepository(Commentaire::class)->findByArticle($article->getId());
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

        return $this->render('article_front/show.html.twig', [
            'article' => $article,
            'comments' => $comments,
            'articles' => $articles,
            'form' => $form->createView(),
            'mostCommentedArticles'=>$mostCommentedArticles

        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $article->setDateCreation(date('Y-m-d '));
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $articleFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($articleFile) {
                $originalFilename = pathinfo($articleFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $originalFilename.'-'.uniqid().'.'.$articleFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $articleFile->move(
                        $this->getParameter('post_images_directory'),
                        $newFilename
                    );
                    $article->setImage($newFilename);

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents

            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $originalFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('post_images_directory'),
                        $newFilename
                    );
                    $article->setImage($newFilename);

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents

            }
            $entityManager->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"POST"})
     */
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
    }




    /**
     * @Route("/afficherArticle/searchajax ", name="ajaxsearcharticle")
     */
    public function searchArticle(Request $request,ArticleRepository $ar)
    {
        $requestString = $request->get('searchValue');
        $articles = $ar->articleSearch($requestString);
        return $this->render('article/articleajax.html.twig', [
            "articles" => $articles
        ]);
    }
}
