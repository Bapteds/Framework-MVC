<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\GestionClientModel;
use ReflectionClass;
use App\Exceptions\AppException;
use Tools\MyTwig;
use App\Entity\Client;
use Tools\Repository;

class GestionClientController {

    public function chercherUn() {
        try {
            
        } catch (Error $ex) {
            throw new AppException("Client " . $id . " inconnu");
        }
    }

    public function chercherTous() {
        $repository = Repository::getRepository("App\Entity\Client");
        $clients = $repository->findAll();
        if ($clients) {
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()) . "/plusieursClients.html.twig";
            MyTwig::afficherVue($vue, array('desClients' => $clients, 'nombreClient' => count($clients)));
//include_once PATH_VIEW . str_replace('Controller','View',$r->getShortName()). "/plusieursClients.php";
        } else {
            throw new AppException("Aucun client à afficher");
        }
    }

    public function creerClient() {
        $vue = "GestionClientView\\creerClient.html.twig";
        MyTwig::afficherVue($vue, array());
    }

    public function enregistreClient(array $params) {
        try {
            $client = new Client($params);
            $modele = new GestionClientModel();
            $modele->enregistreClient($client);
        } catch (Exception $ex) {
            throw new AppException("Erreur à l'enregistrement d'un nouveau client");
        }
    }
}
