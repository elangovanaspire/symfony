<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/app/example", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
    
    public function newAction(Request $request) {
        $task = new Task();
        $task->setTask('Write a Blog Spot');
        $task->setDueDate(new \DateTime('tomorrow'));
        
        $form = $this->createFormBuilder($task)
                ->add('task','test')
                ->add('dueDate', 'date')
                ->add('save', 'submit', array('label' => 'Create Task'))
                ->getForm();
        
        return $this->render('default/new.html.twig', array('form' => $form->createView()));
    }
}
