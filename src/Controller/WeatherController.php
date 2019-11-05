<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Intl\Timezones;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;

use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;

//Classes créées pour l'exercice
use App\Form\CitySearch;
use App\Service\WeatherService;
use App\Service\LocalisationService;


class WeatherController extends AbstractController
{
    private $weatherService;
    private $localisationService;
    private $cityString;
    
    public function __construct(WeatherService $weather,LocalisationService $localisation)
    {
        $this->weatherService = $weather;
        $this->localisationService = $localisation;
        //définir la météo de la page d'accueil sur Toulouse
        $this->cityString = 'Toulouse';
    }

    /**
     * @Route("/weather", name="weather")
     */

    public function index()
    {
        return $this->render('weather/index.html.twig', [
            'controller_name' => 'WeatherController'
        ]);
    }

    //convertir la température envoyée par darksy(degrés Fahrenheit) en degrés Celsius
    public function convertToCelsius($temperature)
    {
        return floor(($temperature - 32)/1.8);
    }
   
    /**
     * @Route("/",name="home")
     */
    
    public function home(Request $request)
    {
        //instanciation du formulaire
        $userCity = new CitySearch();
        $userCity->setCity($this->cityString);

        //initialisation du formulaire de recherche de la ville
        $form = $this->createForm(CitySearch::class, $userCity)
                     ->add('city', TextType::class, [
                           'required' => false
                      ]);
        $form->handleRequest($request);

        //accès aux données du formulaire seulement si celui-ci est rempli
        if ($form->isSubmitted() && $form->isValid())
        {
            $userCity = $form->getData();
            //affectation de la valeur saisie par l'utilisateur dans la variable utilisée pour les appels API
            $this->cityString = $userCity->getCity();
        }

        //appels aux API
        $coordinates = $this->localisationService->getCoordinates($this->cityString);
        $weather = $this->weatherService->getWeather($coordinates);

        //passage au TWIG
        return $this->render('weather/home.html.twig',
        [
            'date' => $weather['date'],
            'title' => 'Météo ce jour à '.$this->cityString,
            'home_city' => $this->cityString,
            'temps' => $weather['temps'],
            'temperature' =>$this->convertToCelsius($weather['temperature']) . ' °C',
            'wind' => floor($weather['vent']) . ' km/h',
            'formCity' => $form->createView()
        ]);
    }
}
?>