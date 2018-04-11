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
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use jarne\toohga\entity\URL;

class RandomIdGenerator extends AbstractIdGenerator {
    /**
     * Generate an ID for an entity
     *
     * @param EntityManager $em
     * @param \Doctrine\ORM\Mapping\Entity $entity
     * @return int
     * @throws \Exception
     */
    public function generate(EntityManager $em, $entity): int {
        $urls = $em->getRepository("jarne\\toohga\\entity\\URL")
            ->findAll();

        if(empty($urls)) {
            return 0;
        }

        asort($urls);

        $i = 0;

        foreach($urls as $url) {
            if($url instanceof URL) {
                if($url->getId() !== $i) {
                    return $i;
                }

                try {
                    $now = new \DateTime();
                    $maxTime = new \DateInterval("P2W");
                } catch(\Exception $exception) {
                    throw new \Exception("Failed to parse the date");
                }

                $deleteDate = $now->sub($maxTime);

                if($url->getCreated() <= $deleteDate) {
                    try {
                        $em->remove($url);
                    } catch(ORMException $exception) {
                        throw new \Exception("Failed to remove an outdated entry");
                    }

                    try {
                        $em->flush();
                    } catch(OptimisticLockException $exception) {
                        throw new \Exception("Version check of an object failed");
                    } catch(ORMException $exception) {
                        throw new \Exception("Failed to flush the data into the database");
                    }
                }
            }

            $i++;
        }

        if($i < (33 ** 8)) {
            return $i++;
        }

        throw new \Exception("There are no unused IDs available anymore");
    }
}
