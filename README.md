From root project directory run the following

$ ./vendor/phpunit/phpunit/phpunit tests/BaseTest

Some assumptions
- All the test input is in the right format
- ~PHP 5.5
- Format is like below

Eg test-1
5 5 #size

1 2 N #mower1 position

LMLMLMLMM #mower1 instructions

3 3 E #mower2 position

MMRMMRMRRM #mower2 instructions

The challenge
-----------------

__Part 1__

Nigel has been tinkering in his shed again, and has hacked together a robotic controller for his lawn mowers. He plans to program each of them to be able to mow all the various lawns in his street on their own (another get rich scheme for Nigel!). Luckily for Nigel, all the lawns in his street are rectangular.
A mower's position and location is represented by a combination of x and y co-ordinates and a letter representing one of the four cardinal compass points (North, South, East, West). A lawn is divided up into a grid to simplify navigation. An example position might be 0, 0, N, which means the mower is in the bottom left corner and facing North.
In order to control a mower, Nigel sends a simple string of letters. The possible letters are 'L', 'R' and 'M'. 'L' and 'R' makes the mower spin 90 degrees left or right respectively, without moving from its current spot. 'M' means move forward one grid point, and maintain the same heading.

Assume that the square directly North from (x, y) is (x, y+1).

0,2	1,2	2,2

0,1	1,1	2,1

0,0	1,0	2,0

INPUT

 The first line of input is the upper-right coordinates of the lawn that is being mowed (i.e the size), the lower-left coordinates are assumed to be 0,0.
 The rest of the input is information pertaining to the mowers that are going to do the mowing. Each mower has two lines of input. The first line gives the mower's position, and the second line is a series of instructions telling the mower how to mow the current lawn.
 The position is made up of two integers and a letter separated by spaces, corresponding to the x and y co-ordinates and the mower's orientation.
 Mowers must not be permitted to bump into each other or run each other over - your program should detect this and fail appropriately.


OUTPUT

The output for each mower should be its final co-ordinates and heading.

SAMPLE INPUT AND OUTPUT

Test Input:

5 5

1 2 N

LMLMLMLMM

3 3 E

MMRMMRMRRM


Expected Output:

1 3 N

5 1 E


__Part 2__

Nigel hasn't stopped there, no! He has an opportunity to wire his robots onto the neighbours lawnmowers as well to make the work even easier. But he doesn't want to reprogram the mowers every time he adds a mower.

INPUT

Nigel would prefer to change his input file to contain the size of the lawn to be mowed, but now include the number of mowers that will be mowing. Once again, the first line represents the upper-right coordinates of the lawn that is being mowed, but a third number indicates the number of mowers that will mow this lawn.

OUTPUT

This time the output should be the instructions that each mower will receive to mow the current lawn most efficiently. An efficient instruction set minimises the difference between the areas of lawn that each mower covers. There may be multiple optimal solutions.

SAMPLE INPUT AND OUTPUT

Test Input:

5 5 3

Expected Output (one possibility):

0 0 N

MMMMMRMRMMMMM

2 0 N

MMMMMRMRMMMMM

4 0 N

MMMMMRMRMMMMM

