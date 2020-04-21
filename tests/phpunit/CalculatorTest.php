<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;


final class CalculatorTest extends TestCase
{
    public function testCalculatorDaysForGivenDate(): void
    {
        $from = new \DateTime("2021-01-03 00:00:00");
        $to = new \DateTime("2021-01-05 23:59:59");
        $calc = new \Moosh\Analysis\ScriptCalculator('Hours test', $from, $to);
        $days = $calc->get_days();
        
        $this->assertCount(3, $days);
        
        // The first key should be 2 - which is the 3rd day of the year.
        reset($days);
        $first = key($days);
        $this->assertEquals(2, $first);

        // The last key should be 4 - which is the 5th day of the year.
        end($days);
        $last = key($days);
        $this->assertEquals(4, $last);
    }

    public function testCalculatorHoursForGivenDate(): void
    {
        $from = new \DateTime("2021-01-02 00:00:00");
        $to = new \DateTime("2021-01-03 23:59:59");
        $calc = new \Moosh\Analysis\ScriptCalculator('Hours test', $from, $to);
        $hours = $calc->get_hours();

        $this->assertCount(48, $hours);

        // The first key should be 22021-01-02 00.
        reset($hours);
        $first = key($hours);
        $this->assertEquals('2021-01-02:00', $first);

        // The last key should be 2021-01-03 23.
        end($hours);
        $last = key($hours);
        $this->assertEquals('2021-01-03:23', $last);
    }    
}
