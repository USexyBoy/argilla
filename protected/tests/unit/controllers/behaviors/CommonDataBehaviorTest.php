<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.controllers.behaviors
 */
class CommonDataBehaviorTest extends CDbTestCase
{
  protected $fixtures = array(
    'contact_field' => 'ContactField',
    'contact_group' => 'ContactGroup',
    'text_block' => 'TextBlock',
    'seo_counters' => 'Counter',
    'seo_link_block' => 'LinkBlock',
    'info' => 'Info',
  );

  public function setUp()
  {
    Yii::app()->setUnitEnvironment('Index', 'index');

    parent::setUp();
  }

  public function testTextBlock()
  {
    $textBlock = Yii::app()->controller->textBlock('main');
    $this->assertNotEmpty($textBlock);
    $this->assertEquals($textBlock->content, 'Текст 2');

    $textBlock = Yii::app()->controller->textBlock('mainNotVisible');
    $this->assertEmpty($textBlock);

    $textBlock = Yii::app()->controller->textBlock('doesNotExist');
    $this->assertEmpty($textBlock);
  }

  public function testTextBlocks()
  {
    $textBlocks = Yii::app()->controller->textBlocks('main');
    $this->assertCount(2, $textBlocks);
    $this->assertEquals($textBlocks[0]->content, 'Текст 2');
    $this->assertEquals($textBlocks[1]->content, 'Текст 1');

    $textBlocks = Yii::app()->controller->textBlock('mainNotVisible');
    $this->assertEmpty($textBlocks);

    $textBlocks = Yii::app()->controller->textBlocks('doesNotExist');
    $this->assertEmpty($textBlocks);
  }

  public function testGetCounters()
  {
    // выбирется все кроме флага на главной
    Yii::app()->setUnitEnvironment('Info', 'index', array('url' => 'o_kompanii'));

    $counters = Yii::app()->controller->getCounters();

    $this->assertCount(2, $counters);
    $this->assertContains('Код счетчика rambler', $counters);
    $this->assertContains('Код счетчика google', $counters);
    $this->assertNotContains('Код счетчика yandex', $counters);

    $this->assertEmpty(array_diff($counters, Yii::app()->controller->counters));

    // выбирается все
    Yii::app()->setUnitEnvironment('Index', 'index');

    $counters = Yii::app()->controller->getCounters();
    $this->assertCount(4, $counters);
    $this->assertContains('Код счетчика rambler', $counters);
    $this->assertContains('Код счетчика google', $counters);
    $this->assertNotContains('Код счетчика yandex', $counters);
    $this->assertContains('Код счетчика google на главной', $counters);
    $this->assertContains('Код счетчика yandex на главной', $counters);

    $this->assertEmpty(array_diff($counters, Yii::app()->controller->counters));
  }

  public function testGetCopyrights()
  {
    Yii::app()->setUnitEnvironment('Index', 'index');

    Yii::app()->request->setRequestUri('/');

    $copyrights = Yii::app()->controller->copyrights;
    $this->assertCount(3, $copyrights);

    $this->assertContains('Код 4', $copyrights);
    $this->assertContains('Код 2', $copyrights);
    $this->assertContains('Код 1 '.date("Y"), $copyrights);

    $copyrights = Yii::app()->controller->getCopyrights('socials');
    $this->assertCount(2, $copyrights);
    $this->assertContains('Код 7', $copyrights);
    $this->assertContains('Код 8', $copyrights);

    $copyrights = Yii::app()->controller->getCopyrights('doesNotExistsKey');
    $this->assertEmpty($copyrights);

    Yii::app()->request->setRequestUri('url/');

    $copyrights = Yii::app()->controller->copyrights;
    $this->assertCount(2, $copyrights);

    $copyrights = Yii::app()->controller->getCopyrights('socials');
    $this->assertCount(2, $copyrights);

    $this->assertContains('Код 5', $copyrights);
    $this->assertContains('Код 7', $copyrights);

    Yii::app()->request->setRequestUri('/');

    $copyrights = Yii::app()->controller->getCopyrights('new');
    $this->assertCount(1, $copyrights);
  }

  public function testGetContacts()
  {
    $contacts = Yii::app()->controller->contacts;

    $this->assertNotEmpty($contacts['phones']);
    $this->assertContains('8 800 000 00 00', $contacts['phones']);

    $contacts = Yii::app()->controller->getContacts('phones');
    $this->assertNotEmpty($contacts);
    $this->assertContains('8 800 300 40 50', $contacts);

    $contacts = Yii::app()->controller->getContacts('icq');
    $this->assertEmpty($contacts);
  }
}