<?php

namespace App\Models\Entities;

use Dobine\Properties\{Ids\Id, Knp\Translation as KnpTranslation, Textable};
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: "`zk_cinemas_translations`")]
class CinemaTranslation implements TranslationInterface {
	use Id, Textable, KnpTranslation;
}
