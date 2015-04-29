<?php

namespace Services;

use Objects\Mower;

/**
 * Class MowerManager
 */
class MowerManager
{
    protected $input;
    protected $output;
    protected $expectedOutput;
    // An array of arrays
    protected $lawn;
    protected $mowerStartCoordinates;
    protected $mowerInstructions;
    protected $mowers;
    // An array of mowers which reflect where the input mowers should be after mowing has completed
    protected $expectedOutputMowers;

    /**
     * All mowers begin mowing
     */
    public function mow()
    {
        // keep tally of which instruction we are up to
        $instructionTally = 0;
        $maxNumInstructions = $this->findMaxNumberOfInstructions();

        // move mowers
        for ($i = 0; $i < $maxNumInstructions; $i++) {
            foreach ($this->getMowers() as $k => $mower) {
                $currentInstructions = $mower->getInstructions();

                // If the mower is not out of instructions
                if (isset($currentInstructions[$instructionTally])) {
                    $instruction = $currentInstructions[$instructionTally];
                    $mower->move($instruction);
                    $mowers[$k] = $mower;

                    // check for a collision
                    if ($this->mowerHasCollisions()) {
                        break 2;
                    }
                }
            }
            $instructionTally++;
        }

        $this->setMowers($mowers);
    }

    /**
     * @return bool
     */
    protected function mowerHasCollisions()
    {
        $hasCollision = false;

        foreach ($this->getMowers() as $k => $mower) {
            foreach ($this->getMowers() as $j => $otherMower) {
                // Do not compare with yourself
                if ($k == $j) {
                    continue;
                }
                // Check mowers have collided
                if ($mower->getX() == $otherMower->getX() && $mower->getY() == $otherMower->getY()) {
                    $hasCollision = true;
                    break 2;
                }
            }
        }

        return $hasCollision;
    }

    /**
     * Process input, set up lawn size and mowers
     */
    public function processInput()
    {
        if ( ! $this->getInput()) {
            throw new BadMethodCallException('Please specify an input');
        }

        $fileHandle = fopen($this->getInput(), "r");
        if ($fileHandle) {
            while (($line = fgets($fileHandle)) !== false) {
                $fileLines[] = $line;
            }
            fclose($fileHandle);
        }

        // Use the first line of the file to make the lawn
        $maxCoordinates = explode(' ', $fileLines[0]);
        $this->createLawn($maxCoordinates[0], $maxCoordinates[1]);
        array_shift($fileLines);

        // Make arrays of mower starting points and their instructions
        $mowerStartCoordinates = array();
        $mowerInstructions = array();
        foreach ($fileLines as $k => $v) {
            if ($k%2 == 0) {
                $mowerStartCoordinates[] = trim($v);
            } else {
                $mowerInstructions[] = trim($v);
            }
        }

        $this->setMowerInstructions($mowerInstructions);
        $this->setMowerStartCoordinates($mowerStartCoordinates);
        $this->createMowers(count($mowerStartCoordinates));

        // program the mowers with the instructions given
        $mowers = $this->getMowers();
        foreach ($mowers as $k => $mower) {
            $currentMowerInstructions = str_split(trim($this->getMowerInstructions()[$k]));
            $mower->setInstructions($currentMowerInstructions);
            $currentMowerStartCoordinates = explode(' ', trim($this->getMowerStartCoordinates()[$k]));
            $mower->setX($currentMowerStartCoordinates[0]);
            $mower->setY($currentMowerStartCoordinates[1]);
            $mower->setHeading($this->mapHeadingStrToDegrees($currentMowerStartCoordinates[2]));
            $mowers[$k] = $mower;
        }

        $this->setMowers($mowers);
    }

    /**
     * Process expected output
     */
    public function processExpectedOutput()
    {
        if (!$this->getExpectedOutput()) {
            throw new BadMethodCallException('Please specify an output');
        }

        $fileHandle = fopen($this->getExpectedOutput(), "r");
        if ($fileHandle) {
            while (($line = fgets($fileHandle)) !== false) {
                $fileLines[] = trim($line);
            }
            fclose($fileHandle);
        }

        $expectedOutputMowers = array();
        foreach ($fileLines as $k => $line) {
            $endingCoordinates = explode(' ', $line);
            $mower = new Mower();
            $mower->setX($endingCoordinates[0]);
            $mower->setY($endingCoordinates[1]);
            $mower->setHeading($this->mapHeadingStrToDegrees($endingCoordinates[2]));
            $expectedOutputMowers[] = $mower;
        }

        $this->setExpectedOutputMowers($expectedOutputMowers);
    }

    /**
     * @param $numMowers
     */
    public function createMowers($numMowers)
    {
        $mowers = array();
        for ($i = 0; $i < $numMowers; $i++) {
            $mowers[] = new Mower();
        }

        $this->setMowers($mowers);
    }

    /**
     * @param $mowerInstructions
     * @return mixed
     */
    protected function getMaxNumberOfMoves($mowerInstructions)
    {
        $numMovesOfMowers = array_map('strlen', $mowerInstructions);

        return max($numMovesOfMowers);
    }

    /**
     * @param $x
     * @param $y
     */
    public function createLawn($x, $y)
    {
        $lawn = array();
        for ($i = 0; $i <= $x; $i++) {
            for ($j = 0; $j <= $y; $j++) {
                $lawn[$i][$j] = '';
            }
        }

        $this->setLawn($lawn);
    }

    /**
     * @return mixed
     */
    public function getLawn()
    {
        return $this->lawn;
    }

    /**
     * @param mixed $lawn
     */
    public function setLawn($lawn)
    {
        $this->lawn = $lawn;
    }

    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param mixed $input
     */
    public function setInput($input)
    {
        $this->input = $input;
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return mixed
     */
    public function getMowerStartCoordinates()
    {
        return $this->mowerStartCoordinates;
    }

    /**
     * @param mixed $mowerStartCoordinates
     */
    public function setMowerStartCoordinates($mowerStartCoordinates)
    {
        $this->mowerStartCoordinates = $mowerStartCoordinates;
    }

    /**
     * @return []
     */
    public function getMowerInstructions()
    {
        return $this->mowerInstructions;
    }

    /**
     * @param [] $mowerInstructions
     */
    public function setMowerInstructions($mowerInstructions)
    {
        $this->mowerInstructions = $mowerInstructions;
    }

    /**
     * @return Mower[]
     */
    public function getMowers()
    {
        return $this->mowers;
    }

    /**
     * @param Mower[] $mowers
     */
    public function setMowers($mowers)
    {
        $this->mowers = $mowers;
    }

    /**
     * @param $headingStr
     * @return int|null
     */
    public function mapHeadingStrToDegrees($headingStr)
    {
        $heading = NULL;
        if ($headingStr == 'N') {
            $heading = Mower::NORTH;
        } else if ($headingStr == 'E') {
            $heading = Mower::EAST;
        } else if ($headingStr == 'S') {
            $heading = Mower::SOUTH;
        } else if ($headingStr == 'W') {
            $heading = Mower::WEST;
        }

        return $heading;
    }

    /**
     * @return mixed
     */
    public function getExpectedOutput()
    {
        return $this->expectedOutput;
    }

    /**
     * @param mixed $expectedOutput
     */
    public function setExpectedOutput($expectedOutput)
    {
        $this->expectedOutput = $expectedOutput;
    }

    /**
     * @return Mower[]
     */
    public function getExpectedOutputMowers()
    {
        return $this->expectedOutputMowers;
    }

    /**
     * @param Mower[] $expectedOutputMowers
     */
    public function setExpectedOutputMowers($expectedOutputMowers)
    {
        $this->expectedOutputMowers = $expectedOutputMowers;
    }

    /**
     * @return int
     */
    public function findMaxNumberOfInstructions()
    {
        $maxInstructions = 0;
        foreach ($this->getMowers() as $mower) {
            $currentMowerInstructionsCount = count($mower->getInstructions());
            if ($currentMowerInstructionsCount > $maxInstructions) {
                $maxInstructions = $currentMowerInstructionsCount;
            }
        }

        return $maxInstructions;
    }
}