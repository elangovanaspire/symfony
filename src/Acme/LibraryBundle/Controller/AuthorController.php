<?php
namespace Acme\LibraryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Acme\LibraryBundle\Model\Author;
use Acme\LibraryBundle\Model\AuthorQuery;
use Acme\LibraryBundle\Form\Type\AuthorType;

class AuthorController extends Controller
{
    public function newAction()
    {
        $author = new Author();
        $form = $this->createForm(new AuthorType(), $author);
        
        $request = $this->getRequest();        
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $author->save();

                return $this->redirect($this->generateUrl('book_success'));
            }
        }
        return $this->render('AcmeLibraryBundle:Author:new.html.twig', array(
            'form' => $form->createView(),
        ));
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
    
   
}