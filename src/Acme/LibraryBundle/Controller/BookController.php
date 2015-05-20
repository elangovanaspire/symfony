<?php
namespace Acme\LibraryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Acme\LibraryBundle\Model\Book;
use Acme\LibraryBundle\Model\BookQuery;
use Acme\LibraryBundle\Form\Type\BookType;

class BookController extends Controller
{
    public function newAction()
    {
        $book = new Book();
        $form = $this->createForm(new BookType(), $book);
        
        $request = $this->getRequest();        
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $book->save();

                return $this->redirect($this->generateUrl('book_success'));
            }
        }
        return $this->render('AcmeLibraryBundle:Book:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    public function getAction($id)
    {
        $book = BookQuery::create()->findPk($id);

        if (!$book instanceof Book) {
            throw new NotFoundHttpException('Book not found');
        }
        //var_dump($user); exit;
        //return new Response('<html>'.$user.'</html>');
       // return array('user' => $user);
       return $this->render('AcmeLibraryBundle:Book:get.html.twig', array('user' => $book));
    }
}