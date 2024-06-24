<?php

namespace App\Controller;

use App\Entity\Enum\Category;
use App\Entity\Enum\Lang;
use App\Entity\Media;
use App\Entity\Movie;
use App\Entity\Trailer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Vich\UploaderBundle\Storage\StorageInterface;
use function Sodium\add;
use function Symfony\Component\Clock\now;


class MovieController extends AbstractController
{
    public function __construct(private readonly StorageInterface $storageInterface, private readonly EntityManagerInterface $entityManager)
    {
    }


    public function __invoke(Request $request): JsonResponse|Movie
    {
        // new code implementation

        $requestProperties = $request->request->all();

        if (empty($requestProperties))
            return $this->json(data: ['message' => 'object properties required'], status: 400);

        // request must contain a movie file.

        if ($request->files->get("mediaFile") == null)
            return $this->json(data: ["message" => 'please provide the movie file', "status" => 422], status: 422);

        // language property
        $lang = $request->request->get('language');
        if (Lang::tryFrom($lang) == null)
            return $this->json(data: ["error_message" => 'invalid Language, only english | french films are allowed'], status: 422);


        // category property list.
        $category = $request->request->get("category");
        $category = explode(",", $category);
        foreach ($category as $item) {
            if (Category::tryFrom($item) == null)
                return $this->json(data: ["error_message" => 'invalid category value, ', $category, "' not supported"], status: 422);
        }

        // list of categories
        $list = array_map(fn ($item)=>Category::from($item),$category);

        $movie = new Movie();
        $movie->setName($request->request->get('name'));
        $movie->setCategory($list);
        $movie->setDescription($request->request->get('description'));
        $movie->setLanguage($lang);

        // release data property: in case it is not a valid date format of not present.
        $releaseAt = date_create($request->request->get('releaseAt'));
        $movie->setReleaseAt($releaseAt ? $releaseAt : new \DateTime('now'));

        //files
        $file = $request->files->get("mediaFile");
        $media = new Media($this->storageInterface);
        $media->setFile($file);
        $media->setMovie($movie);

        //assigning file
        $movie->setMediaFile($media);

        //assigning trailer file
        $trailerFile = $request->files->get('trailer');
        if ($trailerFile != null) {
            $trailer = new Trailer($this->storageInterface);
            $trailer->setFile($trailerFile);
            $trailer->setMovie($movie);
            $movie->setTrailer($trailer);

        }


        $this->entityManager->persist($movie);
        $this->entityManager->flush();


        return $movie;

    }

}
