<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class OrnekTest extends TestCase
{
    /**
     * Deneme testleri
     */
    public function testToplamaIslemi()
    {
        $sayi1=3;
        $sayi2=10;
        $sonuc=$sayi1+$sayi2;
        
        $this->assertEquals(13, $sonuc);
    }

    public function testName()
    {
        $lastName= "yasemin";
        $surName= "calbaz";
        $this->assertEquals("yasemin calbaz",$lastName." ".$surName);
    }
}
