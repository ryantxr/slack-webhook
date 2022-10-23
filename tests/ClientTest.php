<?php declare(strict_types = 1);
use PHPUnit\Framework\TestCase;

/**
*  Corresponding Class to test YourClass class
*
*  @author yourname
*/
class ClientTest extends TestCase
{
    /**
     * Just check if the YourClass has no syntax error 
     *
     * This is just a simple check to make sure your library has no syntax error. This helps you troubleshoot
     * any typo before you even use this library in a real project.
     *
     */
    public function testIsThereAnySyntaxError()
    {
        $var = new \Ryantxr\Slack\Webhook\Client([]);
        $this->assertTrue(is_object($var));
        unset($var);
    }
}
