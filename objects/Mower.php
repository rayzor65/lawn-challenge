<?php

namespace Objects;

class Mower
{
    const NORTH = 0;
    const EAST = 90;
    const SOUTH = 180;
    const WEST = 270;
    const ROTATE_LEFT = 'L';
    const ROTATE_RIGHT = 'R';
    const FORWARD = 'M';

    protected $x;
    protected $y;
    protected $instructions;
    protected $heading;
    protected $boundaryX;
    protected $boundaryY;

    /**
     * @return mixed
     */
    public function getBoundaryX()
    {
        return $this->boundaryX;
    }

    /**
     * @param mixed $boundaryX
     */
    public function setBoundaryX($boundaryX)
    {
        $this->boundaryX = $boundaryX;
    }

    /**
     * @return mixed
     */
    public function getBoundaryY()
    {
        return $this->boundaryY;
    }

    /**
     * @param mixed $boundaryY
     */
    public function setBoundaryY($boundaryY)
    {
        $this->boundaryY = $boundaryY;
    }

    /**
     * @return mixed
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @param mixed $x
     */
    public function setX($x)
    {
        $this->x = $x;
    }

    /**
     * @return mixed
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param mixed $y
     */
    public function setY($y)
    {
        $this->y = $y;
    }

    /**
     * @return mixed
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * @param mixed $instructions
     */
    public function setInstructions($instructions)
    {
        $this->instructions = $instructions;
    }

    /**
     * @param $instruction
     */
    public function move($instruction)
    {
        if (in_array($instruction, array(self::ROTATE_LEFT, self::ROTATE_RIGHT))) {
            $this->rotate($instruction);
        } else if ($instruction == self::FORWARD) {
            $this->forward();
        }
    }

    /**
     * @param $direction
     */
    protected function rotate($direction)
    {
        if ($direction == self::ROTATE_LEFT) {
            $this->setHeading($this->getHeading() - 90);
        } else if ($direction == self::ROTATE_RIGHT) {
            $this->setHeading($this->getHeading() + 90);
        }

        // Make sure we are in between 0 and 360 degrees
        if ($this->getHeading() == 360) {
            $this->setHeading(self::NORTH);
        } else if ($this->getHeading() < 0) {
            $this->setHeading(360 + $this->getHeading());
        }
    }

    /**
     * Move the mower forward one spot, result depends on current heading
     */
    protected function forward()
    {
        if ($this->getHeading() == self::NORTH) {
            // x, y+1
            $this->setY($this->getY() + 1);
        } else if ($this->getHeading() == self::EAST) {
            // x+1, y
            $this->setX($this->getX() + 1);
        } else if ($this->getHeading() == self::SOUTH) {
            // x, y-1
            $this->setY($this->getY() - 1);
        } else if ($this->getHeading() == self::WEST) {
            // x-1, y
            $this->setX($this->getX() - 1);
        }

        // make sure the moves are within boundaries
        // only applies if boundaries are set
        if ($this->getBoundaryX() > 0 && $this->getBoundaryY() > 0) {
            if ($this->getX() < 0) {
                $this->setX(0);
            } else if ($this->getX() > $this->getBoundaryX()) {
                $this->setX($this->getBoundaryX());
            } else if ($this->getY() < 0) {
                $this->setY(0);
            } else if ($this->getY() > $this->getBoundaryY()) {
                $this->setY($this->getBoundaryY());
            }
        }
    }

    /**
     * @return mixed
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * @param mixed $heading
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;
    }
}