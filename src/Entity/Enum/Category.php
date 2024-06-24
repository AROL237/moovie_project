<?php
namespace App\Entity\Enum;
use Doctrine\ORM\Mapping as ORM;

enum Category:string
{
    case EROTIC='erotic';
    case DRAMA='drama';
    case ACTION='action';
    case SHOOTING='shooting';
    case HORROR='horror';
    case DISNEY='disney';
    case FICTION='fiction';
}