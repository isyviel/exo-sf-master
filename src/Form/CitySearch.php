<?php

//src/Form/Task.php
namespace App\Form;

use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints as Assert;

class CitySearch extends AbstractType
{
   /**
    * @Assert\NotBlank
    */
   public $userCity; 
   
   public function getCity()
   {
       return $this->userCity;
   }
       
   public function setCity($userCity)
   {
       $this->userCity = $userCity;
   }    
}
?>