<?php

class NewTeacherTest extends TestCase
{
    public function testOpen()
    {
        $newTeacher = new Plat\Files\Custom\Mailer\NewTeacher;

        $this->assertInternalType('string', $newTeacher->open());

        return $newTeacher;
    }

    /**
     * @depends testOpen
     */
    public function testGetTeachers($newTeacher)
    {
        $newTeachers = $newTeacher->getTeachers();

        $this->assertInternalType('array', $newTeachers);

        $this->assertArrayHasKey('teachers', $newTeachers);

        $this->assertCount(87, $newTeachers['teachers']);
    }
}
