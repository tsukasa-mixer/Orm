<?php
/**
 *
 *
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 03/01/14.01.2014 23:57
 */

namespace Tsukasa\Orm\Tests;

use Tsukasa\Orm\Tests\Models\Product;
use Tsukasa\Orm\Tests\Models\User;

class ValidationTest extends OrmDatabaseTestCase
{
    public function testClass()
    {
        $model = new User();
        $this->assertFalse($model->isValid());
        $this->assertEquals(1, count($model->getErrors()));
        $this->assertEquals(['username' => [
            'This value should not be blank.'
        ]], $model->getErrors());
        $model->username = '123456';
        $this->assertSame('123456', $model->username);
        $this->assertTrue($model->isValid());
        $this->assertEquals([], $model->getErrors());
    }

    public function testClosure()
    {
        $model = new Product();
        $model->setAttributes(['name' => '12']);
        $this->assertFalse($model->isValid());
        $this->assertEquals(1, count($model->getErrors()));
        $this->assertEquals(['name' => ['This value is too short. It should have 3 characters or more.']], $model->getErrors());
        $model->setAttributes(['name' => '123']);
        $this->assertTrue($model->isValid());
        $this->assertEquals([], $model->getErrors());
    }

    public function testCustomValidation()
    {
        /* @var $nameField \Tsukasa\Orm\Fields\Field */
        $model = new User();
        $this->assertFalse($model->isValid());

        $this->assertEquals(['username' => [
            'This value should not be blank.'
        ]], $model->getErrors());

        $nameField = $model->getField('username');
        $this->assertEquals([
            'This value should not be blank.'
        ], $nameField->getErrors());

        $model->username = 'hi';
        $this->assertEquals('hi', $model->username);
        $this->assertFalse($model->isValid());
        $this->assertEquals('hi', $model->username);

        $model->username = 'This is very long name for bad validation example';
        $model->isValid();
        $this->assertEquals(['username' => ['This value is too long. It should have 20 characters or less.']], $model->getErrors());
    }
}
