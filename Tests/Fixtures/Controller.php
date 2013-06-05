<?php

namespace Havvg\Bundle\DRYBundle\Tests\Fixtures;

use Havvg\Bundle\DRYBundle\Controller\I18nTrait;
use Havvg\Bundle\DRYBundle\Controller\LogTrait;
use Havvg\Bundle\DRYBundle\Controller\SecurityTrait;
use Havvg\Bundle\DRYBundle\Controller\SessionTrait;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

class Controller extends BaseController
{
    use I18nTrait;
    use LogTrait;
    use SecurityTrait;
    use SessionTrait;
}
