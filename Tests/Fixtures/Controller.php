<?php

namespace Havvg\Bundle\DRYBundle\Tests\Fixtures;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

use Havvg\Bundle\DRYBundle\Controller\Extension\I18n;
use Havvg\Bundle\DRYBundle\Controller\Extension\Log;
use Havvg\Bundle\DRYBundle\Controller\Extension\Security;
use Havvg\Bundle\DRYBundle\Controller\Extension\Session;

class Controller extends BaseController
{
    use I18n;
    use Log;
    use Security;
    use Session;
}
