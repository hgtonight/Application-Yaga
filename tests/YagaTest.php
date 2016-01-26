<?php
/* Copyright 2016 Zachary Doll*/
class YagaTest extends PHPUnit_Framework_TestCase {
    public function testCanBeCreated()
    {
        include "../library/class.yaga.php";
        $object = new Yaga();
        $this->assertTrue($object);
    }
}
