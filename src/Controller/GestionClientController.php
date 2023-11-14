<?php

declare(strict_types=1);

namespace App\Controller;

use ReflectionClass;
use App\Exceptions\AppException;
use Tools\MyTwig;
use App\Entity\Client;
use Tools\Repository;
use App\Repository\ClientRepository;
use Exception;

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

    public function testFindBy(array $params): void {
        $repository = Repository::getRepository($this->classpath);
        $parametres = array("titreCli" => "Monsieur", "villeCli" => "Toulon");
        $clients = $repository->findByTitreCli_and_villeCli($parametres);
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName()) . "/plusieursClients.html.twig";
        MyTwig::afficherVue($vue, array('desClients' => $clients));
    }

    public function rechercheClients(array $params): void {
        $repository = Repository::getRepository($this->classpath);
        $titres = $repository->findColumnDistinctValues('titreCli');
        $cps = $repository->findColumnDistinctValues('cpCli');
        $villes = $repository->findColumnDistinctValues('villeCli');
        $paramsVue['titres'] = $titres;
        $paramsVue['cps'] = $cps;
        $paramsVue['villes'] = $villes;

        $criterePrepares = $this->verifieEtPrepareCriteres($params);

        if (count($criterePrepares) > 0) { // Permet de vérifier si il y a des filtres. On fait le test pour eviter de créer un tableau null sans aucun filtre.
            $clients = $repository->findBy($params);
            $paramsVue['desClients'] = $clients;
            foreach ($criterePrepares as $valeur) {
                ($valeur != "Choisir...") ? ($criteres[] = $valeur) : (null); // Elle permet d'enregistrer le filtre si il n'est pas égale à = Choisir...
            }
            $paramsVue['criteres'] = $criteres;
        }

        $vue = "GestionClientView\\filtreClients.html.twig";
        MyTwig::afficherVue($vue, $paramsVue);
    }

    private function verifieEtPrepareCriteres(array $params): array {
        $args = array(
            'titreCli' => array(
                'filter' => FILTER_VALIDATE_REGEXP | FILTER_SANITIZE_SPECIAL_CHARS,
                'flags' => FILTER_NULL_ON_FAILURE,
                'options' => array('regexp' => '/^(Monsieur|Madame|Mademoiselle) $/'),),
            'cpCli' => array(
                'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags' => FILTER_NULL_ON_FAILURE,
                'options' => array('regexp' => '/ [0-9] {5} /'),
            ),
            'villeCli' => array(FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        );
        $retour = filter_var_array($params, $args, false);
        if (isset($retour['titreCli']) || isset($retour['cpCli']) || isset($retour['villeCli'])) {
            $element = "Choisir...";
            while (in_array($element, $retour)) {
                unset($retour[array_search($element, $retour)]);
            }
        }
        return $retour;
    }
}
