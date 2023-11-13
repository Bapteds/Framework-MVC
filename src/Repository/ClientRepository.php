<?php

declare(strict_types=1);

namespace App\Repository;

use Tools\Repository;

class ClientRepository extends Repository {

    public function statistiquesTousClients(): array {
        $repository = Repository::getRepository("App\Entity\Client");
        $nb = $repository->executeSQL("SELECT DISTINCT count(idClient) as nb,client.id,nomCli,prenomCli,villeCli FROM commande RIGHT JOIN client on client.id=commande.idClient GROUP By nomCli");
        
        return $nb;
        
    }

}
