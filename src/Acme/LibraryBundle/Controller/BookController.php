<?php
namespace Acme\LibraryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Acme\LibraryBundle\Model\Book;
use Acme\LibraryBundle\Model\BookQuery;
use Acme\LibraryBundle\Form\Type\BookType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookController extends Controller
{
   /* public function newAction()
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
    }*/
    
     public function indexAction()
    {
      
        $book = BookQuery::create()->find();
     
        #return array('users' => $users);
       //    return new Response('<html>'.$users.'</html>');    
       // return $this->render('user:all.html.twig', array('name' => $name));
       return $this->render('AcmeLibraryBundle:Book:index.html.twig', array('books' => $book));
    }
    
    public function newAction()
    {
        return $this->processForm(new Book());
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
    
    private function processForm(Book $book)
    {
        $statusCode = $book->isNew() ? 201 : 204;

        $form = $this->createForm(new BookType(), $book);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $book->save();

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'acme_library_book_get', array('id' => $book->getId()),
                        true // absolute
                    )
                );
            }
           // return $this->redirect('http://dev.symfony.com/app_dev.php/book/');
             return $this->generateUrl('/book');
            return $response;
        }

       // return View::create($form, 400);
        
        return $this->render('AcmeLibraryBundle:Book:new.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
     public function editAction($id)
    {
        if (null === $book = BookQuery::create()->findPk($id)) {
            $book = new Book();
            $book->setId($book);
        }

        return $this->processForm($book);
    }
    
     public function removeAction(Book $book)
    {
        $book->delete();
        return $this->redirect('http://dev.symfony.com/app_dev.php/book/');
    }
}