<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 08.03.17
 * Time: 20:46
 */

namespace jarne\toohga\entity;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;

/**
 * URL
 *
 * @Table(name="urls")
 * @Entity
 */
class URL {
    /**
     * @var string
     *
     * @Column(name="id", type="string")
     * @Id
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="jarne\toohga\doctrine\RandomIdGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="target", type="text")
     */
    private $target;


    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set target
     *
     * @param string $target
     *
     * @return URL
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }
}
