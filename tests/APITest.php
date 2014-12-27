<?php

namespace Glide;

use Mockery;

class APITest extends \PHPUnit_Framework_TestCase
{
    private $api;

    public function testCreateInstance()
    {
        $this->assertInstanceOf('Glide\API', new API(Mockery::mock('Intervention\Image\ImageManager'), []));
    }

    public function testValidateSucceeded()
    {
        $image = Mockery::mock('Intervention\Image\Image');

        $manager = Mockery::mock('Intervention\Image\ImageManager', function ($mock) use ($image) {
            $mock->shouldReceive('make')->andReturn($image);
        });

        $manipulator = Mockery::mock('Glide\Interfaces\Manipulator', function ($mock) {
            $mock->shouldReceive('validate')->andReturn([]);
        });

        $api = new API($manager, [$manipulator]);

        $request = Mockery::mock('Glide\Request');

        $this->assertNull($api->validate($request, 'source'));
    }

    public function testValidateFailed()
    {
        $this->setExpectedException('Glide\Exceptions\ManipulationException');

        $image = Mockery::mock('Intervention\Image\Image');

        $manager = Mockery::mock('Intervention\Image\ImageManager', function ($mock) use ($image) {
            $mock->shouldReceive('make')->andReturn($image);
        });

        $manipulator = Mockery::mock('Glide\Interfaces\Manipulator', function ($mock) {
            $mock->shouldReceive('validate')->andReturn(['error' => 'Example Error']);
        });

        $api = new API($manager, [$manipulator]);

        $request = Mockery::mock('Glide\Request');

        $this->assertNull($api->validate($request, 'source'));
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('getEncoded')->andReturn('encoded');
        });

        $manager = Mockery::mock('Intervention\Image\ImageManager', function ($mock) use ($image) {
            $mock->shouldReceive('make')->andReturn($image);
        });

        $manipulator = Mockery::mock('Glide\Interfaces\Manipulator', function ($mock) {
            $mock->shouldReceive('run')->andReturn(null);
        });

        $api = new API($manager, [$manipulator]);

        $request = Mockery::mock('Glide\Request');

        $this->assertEquals('encoded', $api->run($request, 'source'));
    }
}