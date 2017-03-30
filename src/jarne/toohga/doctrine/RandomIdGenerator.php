<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 28.03.17
 * Time: 18:14
 */

namespace jarne\toohga\doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use jarne\password\Password;

class RandomIdGenerator extends AbstractIdGenerator {
    public function generate(EntityManager $em, $entity) {
        $password = new Password();
        $entity_name = $em->getClassMetadata(get_class($entity))->getName();

        while(true) {
            $id = $password->generateEasyToRemember(6);

            if(!$em->find($entity_name, $id)) {
                return $id;
            }
        }

        throw new \Exception("Can't find an unsed ID");
    }
}