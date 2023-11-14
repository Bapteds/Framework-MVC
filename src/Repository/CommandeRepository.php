<?php

namespace App\Repository;

use Tools\Repository;

class CommandeRepository extends Repository {

    public function getAllClientCommande(int $id) {
        $mesobjets = array();
        $mesobjets['commandes'] = $this->getCommandeFromIdClient($id);
        $mesobjets['client']=$this->getClientFromCommande($id);
        return $mesobjets;
        
    }

    private function getCommandeFromIdClient(int $id) {
        $repository = Repository::getRepository("App\Entity\Commande");
        $commande = $repository->findCommandeFromIdClient($id);
        return $commande;
    }

    private function getClientFromCommande(int $id) {
        $repository = Repository::getRepository("App\Entity\Client");
        $client = $repository->find($id);
        return $client;
    }
}
