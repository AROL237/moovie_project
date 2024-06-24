<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Movie;
use PHPUnit\TextUI\RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Vich\UploaderBundle\Storage\StorageInterface;
use function PHPUnit\Framework\assertIsCallable;

class AddFileToMovieController extends AbstractController
{
    public function __construct(private StorageInterface $storage)
    {

    }

    public function __invoke(Request $request)
    {

        $attributes = $request->attributes->all();
        $file = $request->files->get('file');
        /**
         * @var Movie $data
         */
        $data = $request->attributes->get('data');
        if ($data == null)
            return $this->json(data: ["message" => "unknown movie id", "status" => 400], status: 400);

        if ($file == null)
            return $this->json(data: ["message" => "file property is null", "status" => 400], status: 400);

        /**
         * @var Media $media
         */
        $media = new Media($this->storage);
        $media->setFile($file);
        $data->addMediaList($media);

        return $data;
        // TODO: Implement __invoke() method.
    }
}
