<?php

class QRTest extends TestCase
{
	public function testOpen()
	{
		$qr = new Plat\Files\Custom\QR;

		$this->assertInternalType('string', $qr->open());

		return $qr;
	}

	/**
     * @depends testOpen
     */
	public function testInit($qr)
	{
		//$this->assertInternalType('array', $qr->init());
	}

	/**
     * @depends testOpen
     */
	public function testGetQRCodesA($qr)
	{
		$qrs = $qr->getQRCodesA();

		$this->assertInternalType('array', $qrs);

		$this->assertArrayHasKey('teachers', $qrs);

		$this->assertCount(10, $qrs['teachers']);

		foreach ($qrs['teachers'] as $teacher) {
			$this->assertObjectHasAttribute('qr', $teacher);
			$this->assertObjectHasAttribute('url', $teacher);
		}
	}

	/**
     * @depends testOpen
     */
	public function testGetQRCodesB($qr)
	{
		$qrs = $qr->getQRCodesB();

		$this->assertInternalType('array', $qrs);

		$this->assertArrayHasKey('teachers', $qrs);

		$this->assertCount(10, $qrs['teachers']);

		foreach ($qrs['teachers'] as $teacher) {
			$this->assertObjectHasAttribute('qr', $teacher);
			$this->assertObjectHasAttribute('url', $teacher);
		}
	}

	/**
     * @depends testOpen
     */
	public function testGetQRCodesT($qr)
	{
		$qrs = $qr->getQRCodesB();

		$this->assertInternalType('array', $qrs);

		$this->assertArrayHasKey('teachers', $qrs);

		$this->assertCount(10, $qrs['teachers']);

		foreach ($qrs['teachers'] as $teacher) {
			$this->assertObjectHasAttribute('qr', $teacher);
			$this->assertObjectHasAttribute('url', $teacher);
		}
	}		
}
