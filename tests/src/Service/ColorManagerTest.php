<?php

namespace App\Tests\src\Service;

use App\Service\ColorManager;
use PHPUnit\Framework\TestCase;

class ColorManagerTest extends TestCase
{
    public function testBuild()
    {
        $motus = new ColorManager();
        $this->assertEquals(['green'], $motus->build('A', 'A'));
        $this->assertEquals(['green', 'green'], $motus->build('AS', 'AS'));
        $this->assertEquals(['blue'], $motus->build('B', 'A'));
        $this->assertEquals(['blue', 'blue'], $motus->build('BI', 'AS'));
        $this->assertEquals(['blue', 'green', 'blue'], $motus->build('MER', 'TEK'));
        $this->assertEquals(['green', 'green', 'green', 'green', 'green'], $motus->build('MOTUS', 'MOTUS'));
        $this->assertEquals(['blue', 'blue', 'blue', 'blue', 'blue'], $motus->build('CARRE', 'MOTUS'));
        $this->assertEquals(['yellow', 'yellow'], $motus->build('NU', 'UN'));
        $this->assertEquals(['yellow', 'green', 'green', 'green', 'blue'], $motus->build('SOTUA', 'MOTUS'));
        //these following tests handle juste ONE yellow by occurence
        $this->assertEquals(['yellow', 'blue', 'blue'], $motus->build('TTA', 'MOT'));
        $this->assertEquals(['blue', 'blue', 'green', 'blue', 'blue'], $motus->build('TTTTT', 'MOTUS'));
        $this->assertEquals(['yellow', 'yellow', 'yellow', 'blue', 'green', 'yellow', 'green', 'blue'], $motus->build('CHISINAU', 'MICHIGAN'));
        $this->assertEquals(['green', 'yellow', 'blue', 'blue', 'green', 'blue', 'yellow', 'blue' ], $motus->build('MALAISIE', 'MICHIGAN'));
        $this->assertEquals(['green', 'yellow', 'blue', 'blue', 'green', 'blue', 'yellow', 'blue'], $motus->build('MASSILIA', 'MICHIGAN'));
        $this->assertEquals(['blue', 'yellow', 'blue', 'blue', 'blue', 'green', 'blue', 'yellow'], $motus->build('NAGASAKI', 'TITICACA'));
    }
}
