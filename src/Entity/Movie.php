<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\AddFileToMovieController;
use App\Controller\MovieController;
use App\Entity\Enum\Category;
use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[ApiResource(
    uriTemplate: '/movies',
    shortName: 'Movie|Film',
    normalizationContext: ['groups' => ['read:Movie']],
    denormalizationContext: ['groups' => ['write:Movie']]
)]
#[GetCollection(
    paginationItemsPerPage: 5,
)]
#[Get(uriTemplate: '/movies/{id}', requirements: ['id' => '\d+'],
    normalizationContext: ['groups' => ['read:Movie']],
    denormalizationContext: ['groups' => ['write:Movie']]
)]
//register a new movie
#[Post(
    uriTemplate: '/movies',
    formats: [
        "multipart" => "multipart/form-data",
        "json" => "application/json"
    ],
)]
#[Post(uriTemplate: '/movies/{id}/trailer',
    formats: [
        "multipart" => 'multipart/form-data',
    ],
    requirements: ['id' => '\d+'],
    normalizationContext: ['post:trailer'],
    denormalizationContext: ['post:trailer'],
    deserialize: false
)]
//Create a post with media movie file
#[Post(
    uriTemplate: "/movies/media",
    formats: [
        'multipart' => "multipart/form-data",
    ],
    controller: MovieController::class,
    deserialize: false,
)]
//add a media file to existing Movie
#[Post(
    uriTemplate: '/movies/{id}/media',
    formats: [
        "multipart" => "multipart/form-data",
    ],
    requirements: ["id" => "\d+"],
    controller: AddFileToMovieController::class,
    normalizationContext: ['post:Media'],
    denormalizationContext: ['post:Media'],
    deserialize: false
)]
#[Delete(uriTemplate: '/movies/{id}', requirements: ['id' => '\d+'])]
#[ORM\HasLifecycleCallbacks]
#[Uploadable]
class Movie
{

    public function __construct()
    {
        $this->isVisible = false;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(groups: ['read:Movie'])]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    #[Groups(groups: ['read:Movie', 'write:Movie'])]
    private ?string $name = null;

    #[Groups(groups: ['read:Movie', 'write:Movie'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(groups: ['read:Movie', 'write:Movie'])]
    private ?\DateTimeInterface $releaseAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(groups: ['read:Movie'])]
    private ?\DateTimeInterface $createdAt = null;


    #[ORM\Column(type: Types::SIMPLE_ARRAY, enumType: Category::class)]
    #[Groups(groups: ['read:Movie', 'write:Movie'])]
    private array $category = [];

    /**
     * @var string|null $language - property to identify the language of the movie.
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(groups: ['read:Movie', 'write:Movie'])]
    #[ApiProperty(openapiContext: [
        "type" => "string",
        "enum" => "[english,french]",
        "example" => 'one'
    ])]
    private ?string $language = null;

    #[ORM\Column]
    #[Groups(groups: ['read:Movie'])]
    private ?bool $isVisible;

    /**
     * @var Media $mediaFile
     */
    #[ORM\OneToOne(targetEntity: Media::class, mappedBy: 'movie', cascade: ['persist', 'remove',])]
    #[Groups(groups: ['write:Movie', 'read:Movie'])]
    private Media $mediaFile;

    /**
     * @param-out string $trailer a file property, representing thr trailer of the movie
     * @var Trailer| null
     */


    #[ORM\OneToOne(targetEntity: Trailer::class, mappedBy: 'movie', cascade: ['persist', 'remove'],)]
    #[Groups(groups: ['write:Movie', 'read:Movie'])]
    private Trailer|null $trailer = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getReleaseAt(): ?\DateTimeInterface
    {
        return $this->releaseAt;
    }

    public function setReleaseAt(\DateTimeInterface $releaseAt): static
    {
        $this->releaseAt = $releaseAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $this->createdAt = new \DateTimeImmutable('now');

        return $this;
    }


    /**
     * @return Category[]
     */
    public function getCategory(): array
    {
        return $this->category;
    }

    public function setCategory(array $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getIsVisible(): ?bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): static
    {
        $this->isVisible = $isVisible;

        return $this;
    }


    public function getTrailer()
    {
        return $this->trailer;
    }

    public function setTrailer($trailer): static
    {
        $this->trailer = $trailer;

        return $this;
    }

    public function getMediaFile(): Media
    {
        return $this->mediaFile;
    }

    public function setMediaFile(Media $mediaFile): void
    {
        $this->mediaFile = $mediaFile;
    }


}
