<?php

namespace App\Controller;
use App\Entity\Book;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller; 
use Symfony\Component\HttpFoundation\Response; 

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BooksController extends Controller{

    /**
     * @Route("book/author")
     */
    public function autorAction() {
        return $this->render('BooksStore/author.html.twig');
    }

    /**
     * @Route("/books/display", name="app_book_display")
     */

    public function displayAction(){
        $bk = $this->getDoctrine()
            ->getRepository(Book::class)
            ->findAll();
        return $this->render('BooksStore/display.html.twig', array('data'=>$bk));
    }

    /**
     * @Route("/books/new", name="app_book_new")
     */
    public function newAction(Request $request) {
        $book = new Book();
        $form = $this->createFormBuilder($book)
            ->add('name', TextType::class)
            ->add('author', TextType::class)
            ->add('price', TextType::class)
            ->add('save', SubmitType::class, array('label'=>'Submit'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $book = $form->getData();
            $doct = $this->getDoctrine()->getManager();
            $doct->persist($book);
            $doct->flush();

            return $this->redirectToRoute('app_book_display');
        }
        else{
            return $this->render('BooksStore/new.html.twig', array('form'=>$form->createView(),));
        }

    }

    /**
     * @Route("/books/update/{id}", name="app_book_update")
     */

    public function updateAction($id, Request $request){
        
        $doct = $this->getDoctrine()->getManager();
        $bk = $doct->getRepository(Book::class)->find($id);

        if (!$bk) {
            throw $this->createNotFoundException(
                'No book found for id '.$id
            );
        }

        $form = $this->createFormBuilder($bk)
            ->add('name', TextType::class)
            ->add('author', TextType::class)
            ->add('price', TextType::class)
            ->add('save', SubmitType::class, array('label'=>'Submit'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $doct = $this->getDoctrine()->getManager();

            $doct->persist($book);

            $doct->flush();
            return $this->redirectToRoute('app_book_display'); 
        }
        else {
            return $this->render('BooksStore/new.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @Route("books/delete/{id}", name="app_book_delete")
     */
    public function deleteAction($id) {
        $doct = $this->getDoctrine()->getManager();
        $bk = $doct->getRepository(Book::class)->find($id);

        if (!$bk) {
            throw $this->createNotFoundException("No book found for  id ".$id);
        }
        $doct->remove($bk);
        $doct->flush();
        return $this->redirectToRoute('app_book_display');
    }
}

?>
