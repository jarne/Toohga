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
use jarne\toohga\entity\URL;

class RandomIdGenerator extends AbstractIdGenerator {
    public function generate(EntityManager $em, $entity) {
        $urls = $em->getRepository("jarne\\toohga\\entity\\URL")
            ->findAll();

        if(empty($urls)) {
            return 0;
        }

        asort($urls);

        $i = 0;

        foreach($urls as $url) {
            if($url instanceof URL) {
                if($url->getId() != $i) {
                    return $i;
                }

                $now = new \DateTime();
                $maxTime = new \DateInterval("P2W");

                $deleteDate = $now->sub($maxTime);

                if($url->getCreated() <= $deleteDate) {
                    $em->remove($url);
                    $em->flush();
                }
            }

            $i++;
        }

        if($i < (33 ** 8)) {
            return $i++;
        }

        throw new \Exception("Can't find an unsed ID");
    }
}