<?php

namespace App\Entity;

use App\Repository\TrailerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Vich\UploaderBundle\Storage\StorageInterface;

#[ORM\Entity(repositoryClass: TrailerRepository::class)]
#[Uploadable]
#[ORM\HasLifecycleCallbacks]
class Trailer
{
    public function __construct(private readonly StorageInterface $storage)
    {
        $this->setLastUpdated(new \DateTime('now'));
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(groups: ["read:Movie"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fileName = null;

    #[ORM\Column]
    #[Groups(groups: ["read:Movie"])]
    private ?int $size = null;
    /**
     * @var File|null $file
     */
    #[ORM\Column(nullable: true)]
    #[Groups(groups: ['write:Movie'])]
    #[UploadableField(mapping: 'trailer', fileNameProperty: 'fileName', size: "size")]
    private  $file;

    /**
     * @var Movie |null $movie
     */
    #[ORM\OneToOne(targetEntity: Movie::class, inversedBy: 'trailer')]
    private ?Movie $movie = null;

    /**
     * @var string|null $url
     */

    #[Groups(groups: ['read:Movie'])]
    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(groups: ['read:Movie'])]
    private ?\DateTimeInterface $lastUpdated = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }


    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

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

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): void
    {
        $this->file = $file;
        $this->url = $this->storage->resolveUri($this, 'file');
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): void
    {
        $this->movie = $movie;
    }

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->lastUpdated;
    }

    #[ORM\PreUpdate]
    public function setLastUpdated(\DateTimeInterface $lastUpdated): static
    {
        $this->lastUpdated = $lastUpdated;
        return $this;
    }


    #[ORM\PreUpdate]
    #[ORM\PrePersist]
    public function onPersistOrUpdate(): void
    {
        $this->setLastUpdated(new \DateTime('now'));
    }

}
