<?php

require  'vendor/autoload.php';

use Objects\Mower;
use Services\MowerManager;

/**
 * Class BaseTest
 */
class BaseTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test creation of lawn with the dimensions specified
     */
    public function testCreateLawn()
    {
        $mowerManager = new MowerManager();

        // This creates a 3x3 lawn ie 0, 1, 2
        $mowerManager->createLawn(2, 2);

        // Check num squares on the horizontal axis
        $this->assertEquals(3, count($mowerManager->getLawn()));

        // Check num squares on the vertical axis
        $this->assertEquals(3, count($mowerManager->getLawn()[0]));
    }

    /**
     * Test simple move forward with the heading specified
     */
    public function testMoveMowerForward()
    {
        $mower = new Mower();
        $mower->setX(0);
        $mower->setY(0);
        $mower->setBoundaryX(2);
        $mower->setBoundaryY(2);
        $mower->setHeading(Mower::NORTH);
        $mower->move(Mower::FORWARD);

        // Given these settings $mower should move to (0, 1)
        $this->assertEquals(0, $mower->getX());
        $this->assertEquals(1, $mower->getY());
        $this->assertEquals(Mower::NORTH, $mower->getHeading());
    }

    /**
     * Test a mower cannot move to a spot that does not exist on the lawn
     */
    public function testMoveMowerBeyondLawn()
    {
        $mower = new Mower();
        $mower->setX(0);
        $mower->setY(0);
        $mower->setBoundaryX(2);
        $mower->setBoundaryY(2);
        $mower->setHeading(Mower::WEST);
        $mower->move(Mower::FORWARD);

        // Given these settings $mower should stay still as it is on the boundary
        $this->assertEquals(0, $mower->getX());
        $this->assertEquals(0, $mower->getY());
    }

    /**
     * Test that the mower can rotate
     */
    public function testMowerRotate()
    {
        $mower = new Mower();
        $mower->setHeading(Mower::NORTH);
        $mower->move(MOWER::ROTATE_LEFT);

        $this->assertEquals(MOWER::WEST, $mower->getHeading());
    }

    /**
     * Test that the mower can rotate a full 360
     */
    public function testMowerMaxRotate()
    {
        $mower = new Mower();
        $mower->setHeading(Mower::NORTH);
        $mower->move(Mower::ROTATE_RIGHT);
        $mower->move(Mower::ROTATE_RIGHT);
        $mower->move(Mower::ROTATE_RIGHT);
        $mower->move(Mower::ROTATE_RIGHT);

        // Mower should of done a full 360 and be back to the original heading
        $this->assertEquals(Mower::NORTH, $mower->getHeading());
    }

    /**
     * Test that the mower manager can process the input
     * @throws \Services\BadMethodCallException
     */
    public function testProcessInput()
    {
        $mowerManager = new MowerManager();
        $input = getcwd() . '/tests/test-input/test-1';
        $mowerManager->setInput($input);
        $mowerManager->processInput();

        // test-1 has two mowers
        $this->assertEquals(2, count($mowerManager->getMowers()));

        // the first mower has an orientation of NORTH
        $this->assertEquals(Mower::NORTH, $mowerManager->getMowers()[0]->getHeading());
        // the second mower has an orientation of EAST
        $this->assertEquals(Mower::EAST, $mowerManager->getMowers()[1]->getHeading());
    }

    /**
     * Test that the mower manager can control a mower with the expected behaviour
     */
    public function testMowWithOneMower()
    {
        $mower = new Mower();
        $mower->setHeading(Mower::NORTH);
        $mower->setX(0);
        $mower->setY(0);
        // Mower will go rotate left, right, right again and then move forward
        $mower->setInstructions(array('L', 'R', 'R', 'M'));

        $mowerManager = new MowerManager();
        // Set the mowers to be managed
        $mowerManager->setMowers(array($mower));
        $mowerManager->createLawn(3, 3);
        $mowerManager->mow();
        $mower = $mowerManager->getMowers()[0];

        $this->assertEquals(Mower::EAST, $mower->getHeading());
        $this->assertEquals(1, $mower->getX());
        $this->assertEquals(0, $mower->getY());
    }

    /**
     * Test mowing with no expected collisions
     *
     * @throws \Services\BadMethodCallException
     */
    public function testMowWithNoCollisions()
    {
        $mowerManager = new MowerManager();
        $input = getcwd() . '/tests/test-input/test-1';
        $expectedOutput = getcwd() . '/tests/test-expected-output/test-1';
        $mowerManager->setInput($input);
        $mowerManager->processInput();
        $mowerManager->setExpectedOutput($expectedOutput);
        $mowerManager->processExpectedOutput();
        $mowerManager->mow();
        $mower = $mowerManager->getMowers()[0];
        $expectedOutputMower = $mowerManager->getExpectedOutputMowers()[0];

        $this->assertEquals($expectedOutputMower->getX(), $mower->getX());
        $this->assertEquals($expectedOutputMower->getY(), $mower->getY());
        $this->assertEquals($expectedOutputMower->getHeading(), $mower->getHeading());
    }

    /**
     * Test mowing with expected collisions
     *
     * @throws \Services\BadMethodCallException
     */
    public function testMowWithCollisions()
    {
        $mowerManager = new MowerManager();
        $input = getcwd() . '/tests/test-input/test-3';
        $expectedOutput = getcwd() . '/tests/test-expected-output/test-3';
        $mowerManager->setInput($input);
        $mowerManager->processInput();
        $mowerManager->setExpectedOutput($expectedOutput);
        $mowerManager->processExpectedOutput();
        $mowerManager->mow();

        $mower = $mowerManager->getMowers()[0];
        $expectedOutputMower = $mowerManager->getExpectedOutputMowers()[0];

        // Check the output matches expected output
        // The first mower should be 2 2 E
        $this->assertEquals($expectedOutputMower->getX(), $mower->getX());
        $this->assertEquals($expectedOutputMower->getY(), $mower->getY());
        $this->assertEquals($expectedOutputMower->getHeading(), $mower->getHeading());
        // Given the input it is expected that the mowers collided at 2, 2
        $this->assertEquals(2, $mower->getX());
        $this->assertEquals(2, $mower->getY());
        $this->assertEquals(Mower::EAST, $mower->getHeading());

        $mower = $mowerManager->getMowers()[1];
        $expectedOutputMower = $mowerManager->getExpectedOutputMowers()[1];

        // The second mower should be 2 2 N
        $this->assertEquals($expectedOutputMower->getX(), $mower->getX());
        $this->assertEquals($expectedOutputMower->getY(), $mower->getY());
        $this->assertEquals($expectedOutputMower->getHeading(), $mower->getHeading());
        $this->assertEquals(2, $mower->getX());
        $this->assertEquals(2, $mower->getY());
        $this->assertEquals(Mower::NORTH, $mower->getHeading());
    }

    /**
     * Test that a previous test behaves exactly the same
     *
     * @throws \Services\BadMethodCallException
     */
    public function testRepeat()
    {
        $mowerManager = new MowerManager();
        $input = getcwd() . '/tests/test-input/test-3';
        $expectedOutput = getcwd() . '/tests/test-expected-output/test-3';
        $mowerManager->setInput($input);
        $mowerManager->processInput();
        $mowerManager->setExpectedOutput($expectedOutput);
        $mowerManager->processExpectedOutput();
        $mowerManager->mow();
        $mower = $mowerManager->getMowers()[0];
        $expectedOutputMower = $mowerManager->getExpectedOutputMowers()[0];

        // Check the output matches expected output
        $this->assertEquals($expectedOutputMower->getX(), $mower->getX());
        $this->assertEquals($expectedOutputMower->getY(), $mower->getY());
        $this->assertEquals($expectedOutputMower->getHeading(), $mower->getHeading());
        // Given the input it is expected that the mowers collided at 2, 2
        $this->assertEquals(2, $mower->getX());
        $this->assertEquals(2, $mower->getY());
    }

    /**
     * Test all files in tests/test-input directory
     *
     * @throws \Services\BadMethodCallException
     */
    public function testAllTestInputs()
    {
        $mowerManager = new MowerManager();
        $testDirHandle = opendir(getcwd() . '/tests/test-input');
        while (($file = readdir($testDirHandle)) !== false) {
            if (preg_match('/test/', $file)) {
                $input = getcwd() . '/tests/test-input/' . $file;
                $expectedOutput = getcwd() . '/tests/test-expected-output/' . $file;
                $mowerManager->setInput($input);
                $mowerManager->processInput();
                $mowerManager->setExpectedOutput($expectedOutput);
                $mowerManager->processExpectedOutput();
                $mowerManager->mow();

                foreach ($mowerManager->getMowers() as $k => $mower) {
                    $expectedOutputMower = $mowerManager->getExpectedOutputMowers()[$k];
                    $this->assertEquals($expectedOutputMower->getX(), $mower->getX());
                    $this->assertEquals($expectedOutputMower->getY(), $mower->getY());
                    $this->assertEquals($expectedOutputMower->getHeading(), $mower->getHeading());
                }
            }
        }
        closedir($testDirHandle);
    }
}