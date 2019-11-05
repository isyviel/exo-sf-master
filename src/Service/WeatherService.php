<?php

//Formater les données récupérées par le service afin que le tableau en retour renvoi de vraies valeurs

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class WeatherService
{
    private $client;
    private $apiKey;

    public function __construct($apiKey)
    {
        $this->client = HttpClient::create();
        $this->apiKey = $apiKey;
    }

    //Appel à l'API Darksy
    public function toApi($coordinates)
    { 
        try
        { 
            $response = $this->client->request('GET', 'https://api.darksky.net/forecast/' . $this->apiKey . $coordinates);
        }
        catch (\Exception $e) 
        {
           die('Erreur : ' . $e->getMessage());
        }
        return $response;
    }

    /**
     * @return array
     */

    public function getWeather($coordinates)
    {
        $response = $this->toApi($coordinates); 
        //return un int 200 si la requête est bien passée
        //TO DO: tester le status code
        $statusCode = $response->getStatusCode();
        //return string(31) "application/json; charset=utf-8" 
        $contentType = $response->getHeaders()['content-type'][0];
        //le transforme en tableaux associatifs
        $content = $response->toArray();
        
        return [
            'temps' => $content["hourly"]["summary"],
            'date' => $content["currently"]["time"],
            'temperature' => $content["currently"]["temperature"],
            'vent' => $content["currently"]["windSpeed"]
        ];
    }
}
?>
