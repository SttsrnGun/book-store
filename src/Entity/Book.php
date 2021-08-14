<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Enum\BookTag;
use Acelaya\Doctrine\Type\PhpEnumType;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

PhpEnumType::registerEnumTypes([
    BookTag::class,
]);

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=BookRepository::class)
 * @ApiFilter(PropertyFilter::class, arguments={
 *      "parameterName": "properties", 
 *      "overrideDefaultProperties": false, 
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *      "tag": "partial",
 * })
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $detail;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $about;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imagePath;

    /**
     * @ORM\OneToMany(targetEntity=ReviewBook::class, mappedBy="book")
     */
    private $reviewBooks;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $tag = [];

    public function __construct()
    {
        $this->reviewBooks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * @return Collection|ReviewBook[]
     */
    public function getReviewBooks(): Collection
    {
        return $this->reviewBooks;
    }

    public function getAverageReviewScore(): ?float
    {
        $count = 0.0;
        for ($i = 0; $i < count($this->getReviewBooks()); $i++) {
            $count = $count + $this->getReviewBooks()[$i]->getscore();
        }
        if(count($this->getReviewBooks())){
            return $count/count($this->getReviewBooks());
        }
        return 0;
    }

    public function getReviewCount(): ?int
    {
        return count($this->getReviewBooks());
    }

    public function addReviewBook(ReviewBook $reviewBook): self
    {
        if (!$this->reviewBooks->contains($reviewBook)) {
            $this->reviewBooks[] = $reviewBook;
            $reviewBook->setBook($this);
        }

        return $this;
    }

    public function removeReviewBook(ReviewBook $reviewBook): self
    {
        if ($this->reviewBooks->removeElement($reviewBook)) {
            // set the owning side to null (unless already changed)
            if ($reviewBook->getBook() === $this) {
                $reviewBook->setBook(null);
            }
        }

        return $this;
    }

    public function getTag(): ?array
    {
        return $this->tag;
    }

    public function setTag(?array $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}
