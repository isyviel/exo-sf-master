<?php

//Transformation d'une chaîne de caractères en coordonnées GPS

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class LocalisationService
{
    private $client; 

    public function __construct()
    {
        $this->client = HttpClient::create();
    }

    //Appel à l'API Open Street Map
    public function toApi($cityString)
    { 
        try
        { 
            $response = $this->client->request('GET', 'https://nominatim.openstreetmap.org/search?format=json&city='.$cityString.'&zoom=18');
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
   
    public function getCoordinates($cityString)
    {
        $response = $this->toApi($cityString);
        //return un int 200 si la requête est bien passée
        //TO DO: tester le status code
        $statusCode = $response->getStatusCode();
        //return string(31) "application/json; charset=utf-8" 
        $contentType = $response->getHeaders()['content-type'][0];
        //le transforme en tableaux associatifs
        $content = $response->toArray();
        
        $coordinates = '/'.$content[0]['lat'].','. $content[0]['lon'];
        return $coordinates;
    }
}
?>