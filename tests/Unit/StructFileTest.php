<?php

class StructFileTest extends TestCase
{
    public function testIsFull()
    {
    	$file = Files::find(17648);
      	$user = User::find(2038);
        $sf = new Plat\Files\StructFile($file,$user);

        $this->assertInternalType('bool', $sf->is_full());

        return $sf;
    }

    /**
    * @depends testIsFull
    */

    public function testGetViews($sf)
    {
       	$this->assertInternalType('array', $sf->get_views());
       	$this->assertArraySubset(['open', 'intern', 'integrate', 'organize'], $sf->get_views());
       	$this->assertNotEmpty($sf->get_views());
       	$this->assertCount(4, $sf->get_views());
       	$this->assertNotNull($sf->get_views());
    }

    /**
    * @depends testIsFull
    */

    public function testOpen($sf)
    {
       	$this->assertInternalType('string', $sf->open());
       	$this->assertNotEmpty($sf->open());
    }

    /**
    * @depends testIsFull
    */

    public function testTemplateHelp($sf)
    {
       	$this->assertInternalType('object', $sf->templateHelp());
    }

    /**
    * @depends testIsFull
    */

    public function testTemplateExplain($sf)
    {
       	$this->assertInternalType('object', $sf->templateExplain());
    }

    /**
    * @depends testIsFull
    */

    public function testOrganize($sf)
    {
       	$this->assertInternalType('string', $sf->organize());
    }

    /**
    * @depends testIsFull
    */

    /*public function testGetEachItems($sf)
    {
       	$this->assertInternalType('array', $sf->getEachItems());
    }*/

    /**
    * @depends testIsFull
    */

    public function testPopulations($sf)
    {
       	// $this->assertInternalType('array', $sf->populations);
       	$this->assertObjectHasAttribute('populations', $sf);
       	// $this->assertAttributeContains('populations', $sf);
    }

    /**
    * @depends testIsFull
    */

    public function testCalibration($sf)
    {
       	$this->assertInternalType('array', $sf->calibration());
       	$this->assertNotNull($sf->calibration());
    }

    /**
    * @depends testIsFull
    */

    public function testSetLevel($sf)
    {
    }

    /**
    * @depends testIsFull
    */

    public function testGetCategories($sf)
    {
    	$categories = $sf->getCategories();
    	$this->assertArrayHasKey('個人資料', $categories);
    	$this->assertArrayHasKey('就學資訊', $categories);
    	$this->assertArrayHasKey('完成教育專業課程', $categories);
    	$this->assertArrayHasKey('卓越師資培育獎學金', $categories);
    	$this->assertArrayHasKey('實際參與實習', $categories);
    	$this->assertArrayHasKey('教師資格檢定', $categories);
    	$this->assertArrayHasKey('教師專長', $categories);
    	$this->assertArrayHasKey('教甄資料', $categories);
    	$this->assertArrayHasKey('在職教師', $categories);
    	$this->assertArrayHasKey('閩南語檢定', $categories);

    	foreach ($categories as $categorie) {
    	    $this->assertInternalType('array', $categorie);
    	}
    }

}
