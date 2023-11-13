<?php

declare(strict_types=1);

namespace App\Controller;

use ReflectionClass;
use App\Exceptions\AppException;
use Tools\MyTwig;
use App\Entity\Client;
use Tools\Repository;
use App\Repository\ClientRepository;

class GestionClientController {

    private $classpath = "App\Entity\Client";

    public function chercherUn(array $params) {
        $repository = Repository::getRepository($this->classpath);
        $lesIds = $repository->findIds();
        $params['lesId'] = $lesIds;
        $params['objet'] = "Clients";
        if (array_key_exists('id', $params)) {
            $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
            $unClient = $repository->find($id);
            if ($unClient) {
                $params['unClient'] = $unClient;
            } else {
                $params['message'] = "Client " . $id . " inconnu";
            }
        }
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName()) . "/unClient.html.twig";
        MyTwig::afficherVue($vue, $params);
    }

    public function chercherTous() {
        $repository = Repository::getRepository($this->classpath);
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

    public function creerClient(array $params) {
        if (empty($params)) {
            $vue = "GestionClientView\\creerClient.html.twig";
            MyTwig::afficherVue($vue, array());
        } else {
            try {
                $params = $this->verificationSaisieClient($params);
                $client = new Client($params);
                $repository = Repository::getRepository($this->classpath);
                $repository->insert($client);
                $this->chercherTous();
            } catch (Exception $ex) {
                throw new AppException("Erreur au moment de l'enregistrement");
            }
        }
    }

    public function verificationSaisieClient(array $params) {
        return $params;
    }

    public function nbClients(array $params): void {
        $repository = Repository::getRepository($this->classpath);
        $nbClients = $repository->countRows();
        echo "Nombre de clients: " . $nbClients;
    }

    public function statsClients() {
        $repository = Repository::getRepository($this->classpath);
        $desClients = $repository->statistiquesTousClients();
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName()) . "/statsClients.html.twig";
        MyTwig::afficherVue($vue, array('desClients' => $desClients));
    }

    public function modifierClient(array $params) {
        if (array_key_exists('id', $params)) {
            $repository = Repository::getRepository($this->classpath);
            $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
            $client = $repository->find($id);
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()) . "/modifierClient.html.twig";
            MyTwig::afficherVue($vue, array('client' => $client));
        }
    }

    public function sauvegarderClient(array $params) {
        try {
            $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
            $this->verificationSaisieClient($params);
            $client = new Client($params);
            $repository = Repository::getRepository($this->classpath);
            $repository->update($client, $id);
            return $this->chercherTous();
        } catch (Exception $ex) {
            throw new AppException("Erreur au moment de l'enregistrement");
        }
    }

    public function supprimerClient(array $params) {
        try {
            $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
            $repository = Repository::getRepository($this->classpath);
            $repository->delete($id);
            return $this->chercherTous();
        } catch (Exception $ex) {
            throw new AppException("Erreur au moment de la suppression");
        }
    }
    
    public function testFindBy(array $params) :void{
        $repository = Repository::getRepository($this->classpath);
        $parametres = array("titreCli"=>"Monsieur","villeCli"=>"Toulon");
        $clients = $repository->findByTitreCli_and_villeCli($parametres);
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName()) ."/tousClients.html.twig";
        MyTwig::afficherVue($vue, array('lesClients'=> $clients));
    }
}
