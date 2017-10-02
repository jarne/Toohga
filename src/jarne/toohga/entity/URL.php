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
     * @var int
     *
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="jarne\toohga\doctrine\RandomIdGenerator")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var string
     *
     * @Column(name="client", type="string")
     */
    private $client;

    /**
     * @var string
     *
     * @Column(name="target", type="text")
     */
    private $target;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return URL
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set client
     *
     * @param string $client
     *
     * @return URL
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return string
     */
    public function getClient()
    {
        return $this->client;
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
