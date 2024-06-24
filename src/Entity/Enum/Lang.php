<?php
namespace App\Entity\Enum;
use Doctrine\ORM\Mapping as ORM;

enum Lang :string
{
    case ENGLISH='english';
    case FRENCH='french';
}
