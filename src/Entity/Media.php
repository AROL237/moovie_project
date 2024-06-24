<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\MediaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Vich\UploaderBundle\Storage\StorageInterface;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[Uploadable]
class Media
{
    public function __construct(private readonly ?StorageInterface $storageInterface)
    {
        $this->setLastUpdate(new \DateTime('now'));
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(groups: ['read:Movie'])]
    private ?string $fileName = null;

    #[ORM\Column(length: 255)]
    #[Groups(groups: ['read:Movie'])]
    private ?string $size = null;

    /**
     * @var File|null
     */
    #[ORM\Column(type: Types::BLOB)]
    #[Groups(groups: ["write:Movie"])]
    #[UploadableField(mapping: 'movie', fileNameProperty: 'fileName', size: "size",)]
    private $file;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(groups: ['read:Movie'])]
    private ?\DateTimeInterface $lastUpdate = null;

    #[ORM\OneToOne(targetEntity: Movie::class, inversedBy: 'mediaFile')]
    #[Groups(groups: ["read:Media"])]
    private ?Movie $movie = null;

    #[ORM\Column(length: 255)]
    #[Groups(groups: ["read:Movie"])]
    private ?string $url = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }


    public function setLastUpdate(\DateTimeInterface $lastUpdate): static
    {
        $this->lastUpdate = $lastUpdate;
        return $this;
    }

    #[ORM\PreUpdate]
    #[ORM\PrePersist]
    public function onPersistOrUpdate(): static
    {
        $this->setLastUpdate(new \DateTime('now'));
        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): void
    {
        $this->file = $file;
        $this->url = $this->storageInterface->resolveUri($this, 'file');;

    }

    public function setMovie(?Movie $movie): static
    {
        $this->movie = $movie;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {

        $this->url = $url;

        return $this;
    }

}
