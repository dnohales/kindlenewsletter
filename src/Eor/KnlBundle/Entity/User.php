<?php
namespace Eor\KnlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="Eor\KnlBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User implements UserInterface
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string")
	 * @ORM\GeneratedValue(strategy="NONE")
	 */
	private $id;
	
	/**
	 * @ORM\Column(name="email", type="string", nullable=true)
	 */
	private $email;

	/**
	 * @ORM\Column(name="verified_email", type="string", nullable=true)
	 */
	private $verifiedEmail;

	/**
	 * @ORM\Column(name="name", type="string", nullable=true)
	 */
	private $name;

	/**
	 * @ORM\Column(name="given_name", type="string", nullable=true)
	 */
	private $givenName;

	/**
	 * @ORM\Column(name="family_name", type="string", nullable=true)
	 */
	private $familyName;

	/**
	 * @ORM\Column(name="link", type="string", nullable=true)
	 */
	private $link;

	/**
	 * @ORM\Column(name="picture", type="string", nullable=true)
	 */
	private $picture;

	/**
	 * @ORM\Column(name="gender", type="string", nullable=true)
	 */
	private $gender;

	/**
	 * @ORM\Column(name="birthday", type="string", nullable=true)
	 */
	private $birthday;

	/**
	 * @ORM\Column(name="locale", type="string", nullable=true)
	 */
	private $locale;

	public function setProfileData(array $profileData)
	{
		$this->id = $this->getKey($profileData, 'id');
		$this->email = $this->getKey($profileData, 'email');
		$this->verifiedEmail = $this->getKey($profileData, 'verified_email');
		$this->name = $this->getKey($profileData, 'name');
		$this->givenName = $this->getKey($profileData, 'given_name');
		$this->familyName = $this->getKey($profileData, 'family_name');
		$this->link = $this->getKey($profileData, 'link');
		$this->picture = $this->getKey($profileData, 'picture');
		$this->gender = $this->getKey($profileData, 'gender');
		$this->birthday = $this->getKey($profileData, 'birthday');
		$this->locale = $this->getKey($profileData, 'locale');

	}
	
	private function getKey(array $a, $k)
	{
		return isset($a[$k])? $a[$k]:null;
	}
	
	public function getId()
	{
		return $this->id;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getVerifiedEmail()
	{
		return $this->verifiedEmail;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getGivenName()
	{
		return $this->givenName;
	}

	public function getFamilyName()
	{
		return $this->familyName;
	}

	public function getLink()
	{
		return $this->link;
	}

	public function getPicture()
	{
		return $this->picture;
	}

	public function getGender()
	{
		return $this->gender;
	}

	public function getBirthday()
	{
		return $this->birthday;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function eraseCredentials()
	{
	}

	public function getPassword()
	{
		return '';
	}

	public function getRoles()
	{
		return array();
	}

	public function getSalt()
	{
		return '';
	}

	public function getUsername()
	{
		return $this->getId();
	}

}