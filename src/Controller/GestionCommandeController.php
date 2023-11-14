<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\GestionCommandeModel;
use ReflectionClass;
use App\Exceptions\AppException;
use Tools\Repository;
use Tools\MyTwig;

class GestionCommandeController {

    private $classpath = "App\Entity\Commande";

    public function chercherUne(array $params) {
        $repository = Repository::getRepository($this->classpath);
        $lesIds = $repository->findIds();
        $params['lesId'] = $lesIds;
        $params['objet'] = "Commandes";
        if (array_key_exists('id', $params)) {
            $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
            $uneCommande = $repository->find($id);
            if ($uneCommande) {
                $params['uneCommande'] = $uneCommande;
            } else {
                $params['message'] = "Commande " . $id . " inconnu";
            }
        }
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName()) . "/uneCommande.html.twig";
        MyTwig::afficherVue($vue, $params);
    }

    public function chercherToutes() {
        $repository = Repository::getRepository($this->classpath);
        $commandes = $repository->findAll();
        if ($commandes) {
            $r = new ReflectionClass($this);
            include_once PATH_VIEW . str_replace('Controller', 'View', $r->getShortName()) . "/plusieursCommandes.php";
        } else {
            throw new AppException("Aucune commande à afficher");
        }
    }

    public function commandesUnClient(array $params) {
        $vue = "GestionCommandeView\\commandeClient.html.twig";
        if (array_key_exists('id', $params)) {
            $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
            $repository = Repository::getRepository($this->classpath);
            $listeCommandesClient = $repository->getAllClientCommande($id);
            MyTwig::afficherVue($vue, array('client'=>$listeCommandesClient['client'],'commandes'=>$listeCommandesClient['commandes']));

            // RETOURNE LA VUE
        } else {
            MyTwig::afficherVue($vue, $params);
        }
    }
}
