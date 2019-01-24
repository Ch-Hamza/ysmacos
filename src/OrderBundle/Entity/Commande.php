<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\CommandeRepository")
 */
class Commande
{
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="OrderBundle\Entity\OrderItem", mappedBy="commande", cascade={"persist", "remove"})
     */
    private $items;

    /**
     * @ORM\OneToOne(targetEntity="OrderBundle\Entity\OrderInfo", cascade={"persist", "remove"})
     */
    private $orderInfo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="saleDate", type="datetime")
     */
    private $saleDate;

    /**
     * @ORM\Column(type="boolean", length=255, nullable=true)
     * @var bool
     */
    private $enabled;

    /**
     * @ORM\Column(type="boolean", length=255, nullable=true)
     * @var bool
     */
    private $archived;


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
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return mixed
     */
    public function getOrderInfo()
    {
        return $this->orderInfo;
    }

    /**
     * @param mixed $orderInfo
     */
    public function setOrderInfo($orderInfo)
    {
        $this->orderInfo = $orderInfo;
    }

    /**
     * @return \DateTime
     */
    public function getSaleDate()
    {
        return $this->saleDate;
    }

    /**
     * @param \DateTime $saleDate
     */
    public function setSaleDate($saleDate)
    {
        $this->saleDate = $saleDate;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function addItem(OrderItem $item)
    {
        $item->setCommande($this);
        $this->items[] = $item;
        return $this;
    }

    public function removeItem(OrderItem $item)
    {
        return $this->items->removeElement($item);
    }

    /**
     * @return bool
     */
    public function isArchived()
    {
        return $this->archived;
    }

    /**
     * @param bool $archived
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;
    }
}

