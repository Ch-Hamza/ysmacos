<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OrderInfo
 *
 * @ORM\Table(name="order_info")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrderInfoRepository")
 */
class OrderInfo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]*$/",
     *     message="Invalid Name",
     *     )
     *
     * @ORM\Column(name="customerFirstName", type="string", length=255)
     */
    private $customerFirstName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]*$/",
     *     message="Invalid Last Name",
     * )
     *
     * @ORM\Column(name="customerLastName", type="string", length=255)
     */
    private $customerLastName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]*$/",
     *     message="Invalid Company Name",
     * )
     *
     * @ORM\Column(name="customerCompany", type="string", length=255)
     */
    private $customerCompany;

    /**
     * @var int
     *
     * @Assert\Regex(
     *     pattern="/\d{8}/",
     *     htmlPattern="/\d{8}/",
     *     message="Invalid Phone Number",
     * )
     *
     * @ORM\Column(name="customerPhone", type="integer")
     */
    private $customerPhone;

    /**
     * @var string
     *
     * @Assert\Email(
     *     message = "'{{ value }}' is not a valid email.",
     *     checkMX = true,
     * )
     *
     * @ORM\Column(name="customerEmail", type="string", length=255)
     */
    private $customerEmail;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message="City can't be blank",
     * )
     *
     * @ORM\Column(name="customer_city", type="string", length=255, nullable=true)
     */
    private $customerCity;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message="Address can't be blank",
     * )
     *
     * @ORM\Column(name="customer_address", type="string", length=255, nullable=true)
     */
    private $customerAddress;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/\d{4}/",
     *     message="Invalic Code",
     * )
     *
     * @ORM\Column(name="customer_postal_code", type="string", length=255, nullable=true)
     */
    private $postalCode;


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
     * Set customerFirstName
     *
     * @param string $customerFirstName
     *
     * @return OrderInfo
     */
    public function setCustomerFirstName($customerFirstName)
    {
        $this->customerFirstName = $customerFirstName;

        return $this;
    }

    /**
     * Get customerFirstName
     *
     * @return string
     */
    public function getCustomerFirstName()
    {
        return $this->customerFirstName;
    }

    /**
     * Set customerLastName
     *
     * @param string $customerLastName
     *
     * @return OrderInfo
     */
    public function setCustomerLastName($customerLastName)
    {
        $this->customerLastName = $customerLastName;

        return $this;
    }

    /**
     * Get customerLastName
     *
     * @return string
     */
    public function getCustomerLastName()
    {
        return $this->customerLastName;
    }

    /**
     * Set customerPhone
     *
     * @param integer $customerPhone
     *
     * @return OrderInfo
     */
    public function setCustomerPhone($customerPhone)
    {
        $this->customerPhone = $customerPhone;

        return $this;
    }

    /**
     * Get customerPhone
     *
     * @return int
     */
    public function getCustomerPhone()
    {
        return $this->customerPhone;
    }

    /**
     * Set customerEmail
     *
     * @param string $customerEmail
     *
     * @return OrderInfo
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    /**
     * Get customerEmail
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * @return string
     */
    public function getCustomerCompany()
    {
        return $this->customerCompany;
    }

    /**
     * @param string $customerCompany
     */
    public function setCustomerCompany($customerCompany)
    {
        $this->customerCompany = $customerCompany;
    }

    /**
     * @return string
     */
    public function getCustomerCity()
    {
        return $this->customerCity;
    }

    /**
     * @param string $customerCity
     */
    public function setCustomerCity($customerCity)
    {
        $this->customerCity = $customerCity;
    }

    /**
     * @return string
     */
    public function getCustomerAddress()
    {
        return $this->customerAddress;
    }

    /**
     * @param string $customerAddress
     */
    public function setCustomerAddress($customerAddress)
    {
        $this->customerAddress = $customerAddress;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }
}

