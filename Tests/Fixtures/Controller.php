<?php

namespace Havvg\Bundle\CommonControllerBundle\Tests\Fixtures;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

use Havvg\Bundle\CommonControllerBundle\Controller\Extension\I18n;
use Havvg\Bundle\CommonControllerBundle\Controller\Extension\Log;
use Havvg\Bundle\CommonControllerBundle\Controller\Extension\Security;
use Havvg\Bundle\CommonControllerBundle\Controller\Extension\Session;

class Controller extends BaseController
{
    use I18n;
    use Log;
    use Security;
    use Session;
}
