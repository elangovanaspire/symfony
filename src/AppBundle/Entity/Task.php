<?php
namespace AppBundle\Entity;

class Task {
    protected $task;
    protected $dueDate;
    
    public function getTask() {
        return $this->task;
    }
    
    public function setTask() {
        $this->task= $task;
    }
    
    public function getDueDate() {
        return $this->dueDate();
    }
    
    public function setDueDate(\DateTime $dudate = null) {
        $this->dueDate = $dueDate;
    }
}



