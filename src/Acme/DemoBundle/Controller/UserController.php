<?php
namespace Acme\DemoBundle\Controller;

use Acme\DemoBundle\Model\User;
use Acme\DemoBundle\Model\UserQuery;
use Acme\DemoBundle\Form\Type\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserController extends Controller
{
     /**
     * @Rest\View
     */
    public function allAction()
    {
      
        $users = UserQuery::create()->find();
     
        #return array('users' => $users);
       //    return new Response('<html>'.$users.'</html>');    
       // return $this->render('user:all.html.twig', array('name' => $name));
       return $this->render('AcmeDemoBundle:User:all.html.twig', array('users' => $users));
    }

    /**
     * @Rest\View
     */
    public function getAction($id)
    {
        $user = UserQuery::create()->findPk($id);

        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found');
        }
        //var_dump($user); exit;
        //return new Response('<html>'.$user.'</html>');
       // return array('user' => $user);
       return $this->render('user/get.html.twig', array('user' => $user));
    }
    
    
    public function newAction(Request $request)
    {
        echo "==="; exit;
        $user = new User();
        $form = $this->createForm(new UserType(), $user);

        $request = $this->getRequest();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $user->save();

                //return $this->redirect($this->generateUrl('book_success'));
            }
        }

        return $this->render('AcmeDemoBundle:User:new.html.twig', array(
            'form' => $form->createView(),
        ));
       // return $this->processForm(new User());
    }
    
    private function processForm(User $user)
    {
        $statusCode = $user->isNew() ? 201 : 204;

        $form = $this->createForm(new UserType(), $user);
        $form->handleRequest($this->getRequest());
        var_dump($form); exit;
        if ($form->isValid()) {
            $user->save();

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'acme_demo_user_get', array('id' => $user->getId()),
                        true // absolute
                    )
                );
            }

            return $response;
        }

        //return View::create($form, 400);
        
        return $this->render('AcmeDemoBundle:User:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
     public function editAction($id)
    {
        if (null === $user = UserQuery::create()->findPk($id)) {
            $user = new User();
            $user->setId($id);
        }

        return $this->processForm($user);
    }
    
     /**
     * @Rest\View(statusCode=204)
     */
    public function removeAction(User $user)
    {
        $user->delete();
    }
}