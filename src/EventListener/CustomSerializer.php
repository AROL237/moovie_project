<?php

namespace App\EventListener;

use ApiPlatform\State\SerializerContextBuilderInterface;
use phpDocumentor\Reflection\DocBlock\Serializer;

class CustomSerializer implements SerializerInterface
{

    public function __construct(private SerializerContextBuilderInterface $builder)
    {
    }

    public function onKernelSerialization($event):void
    {
//        dd($data);



    }


}