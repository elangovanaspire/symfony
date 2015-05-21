<?php
namespace Acme\LibraryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Acme\LibraryBundle\Model\Author;
use Acme\LibraryBundle\Model\AuthorQuery;
use Acme\LibraryBundle\Form\Type\AuthorType;

class AuthorController extends Controller
{
    public function indexAction()
    {
      
        $author = AuthorQuery::create()->find();
     
        #return array('users' => $users);
       //    return new Response('<html>'.$users.'</html>');    
       // return $this->render('user:all.html.twig', array('name' => $name));
       return $this->render('AcmeLibraryBundle:Author:index.html.twig', array('authors' => $author));
    }
    public function newAction()
    {
        return $this->processForm(new Author());
    }
    
    public function getAction($id)
    {
        $author = AuthorQuery::create()->findPk($id);

        if (!$author instanceof Author) {
            throw new NotFoundHttpException('Author not found');
        }
        //var_dump($user); exit;
        //return new Response('<html>'.$user.'</html>');
       // return array('user' => $user);
       return $this->render('AcmeLibraryBundle:Author:get.html.twig', array('author' => $author));
    }
    
    
    private function processForm(Author $book)
    {
        $statusCode = $book->isNew() ? 201 : 204;

        $form = $this->createForm(new AuthorType(), $book);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $book->save();

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'acme_library_author_get', array('id' => $book->getId()),
                        true // absolute
                    )
                );
            }

            return $response;
        }

       // return View::create($form, 400);
        
        return $this->render('AcmeLibraryBundle:Author:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
     public function editAction($id)
    {
        if (null === $book = AuthorQuery::create()->findPk($id)) {
            $book = new Author();
            $book->setId($book);
        }

        return $this->processForm($book);
    }
    
     public function removeAction(Book $book)
    {
        $book->delete();
        return $this->render('AcmeLibraryBundle:Author:index.html.twig');
    }
   
}