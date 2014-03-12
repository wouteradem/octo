<?php

/**
 * Form base model for table: form
 */

namespace Octo\Model\Base;

use Octo\Model;
use b8\Store\Factory;

/**
 * Form Base Model
 */
class FormBase extends Model
{
    /**
    * @var array
    */
    public static $sleepable = array();

    /**
    * @var string
    */
    protected $tableName = 'form';

    /**
    * @var string
    */
    protected $modelName = 'Form';

    /**
    * @var array
    */
    protected $data = array(
        'id' => null,
        'title' => null,
        'recipients' => null,
        'definition' => null,
        'thankyou_message' => null,
);

/**
* @var array
*/
protected $getters = array(
// Direct property getters:
'id' => 'getId',
'title' => 'getTitle',
'recipients' => 'getRecipients',
'definition' => 'getDefinition',
'thankyou_message' => 'getThankyouMessage',

// Foreign key getters:
);

/**
* @var array
*/
protected $setters = array(
// Direct property setters:
'id' => 'setId',
'title' => 'setTitle',
'recipients' => 'setRecipients',
'definition' => 'setDefinition',
'thankyou_message' => 'setThankyouMessage',

// Foreign key setters:
);

/**
* @var array
*/
public $columns = array(
'id' => array(
'type' => 'int',
'length' => 11,
'primary_key' => true,
'auto_increment' => true,
'default' => null,
),
'title' => array(
'type' => 'varchar',
'length' => 250,
),
'recipients' => array(
'type' => 'text',
'nullable' => true,
'default' => null,
),
'definition' => array(
'type' => 'mediumtext',
'default' => null,
),
'thankyou_message' => array(
'type' => 'mediumtext',
'nullable' => true,
'default' => null,
),
);

/**
* @var array
*/
public $indexes = array(
'PRIMARY' => array('unique' => true, 'columns' => 'id'),
);

/**
* @var array
*/
public $foreignKeys = array(
);

/**
* Get the value of Id / id.
*
* @return int
*/
public function getId()
{
$rtn    = $this->data['id'];

return $rtn;
}

/**
* Get the value of Title / title.
*
* @return string
*/
public function getTitle()
{
$rtn    = $this->data['title'];

return $rtn;
}

/**
* Get the value of Recipients / recipients.
*
* @return string
*/
public function getRecipients()
{
$rtn    = $this->data['recipients'];

return $rtn;
}

/**
* Get the value of Definition / definition.
*
* @return string
*/
public function getDefinition()
{
$rtn    = $this->data['definition'];

return $rtn;
}

/**
* Get the value of ThankyouMessage / thankyou_message.
*
* @return string
*/
public function getThankyouMessage()
{
$rtn    = $this->data['thankyou_message'];

return $rtn;
}

/**
* Set the value of Id / id.
*
* Must not be null.
* @param $value int
*/
public function setId($value)
{
$this->validateNotNull('Id', $value);
$this->validateInt('Id', $value);

if ($this->data['id'] === $value) {
return;
}

$this->data['id'] = $value;

$this->setModified('id');
}

/**
* Set the value of Title / title.
*
* Must not be null.
* @param $value string
*/
public function setTitle($value)
{
$this->validateNotNull('Title', $value);
$this->validateString('Title', $value);

if ($this->data['title'] === $value) {
return;
}

$this->data['title'] = $value;

$this->setModified('title');
}

/**
* Set the value of Recipients / recipients.
*
* @param $value string
*/
public function setRecipients($value)
{
$this->validateString('Recipients', $value);

if ($this->data['recipients'] === $value) {
return;
}

$this->data['recipients'] = $value;

$this->setModified('recipients');
}

/**
* Set the value of Definition / definition.
*
* Must not be null.
* @param $value string
*/
public function setDefinition($value)
{
$this->validateNotNull('Definition', $value);
$this->validateString('Definition', $value);

if ($this->data['definition'] === $value) {
return;
}

$this->data['definition'] = $value;

$this->setModified('definition');
}

/**
* Set the value of ThankyouMessage / thankyou_message.
*
* @param $value string
*/
public function setThankyouMessage($value)
{
$this->validateString('ThankyouMessage', $value);

if ($this->data['thankyou_message'] === $value) {
return;
}

$this->data['thankyou_message'] = $value;

$this->setModified('thankyou_message');
}

/**
* Get Submission models by FormId for this Form.
*
* @uses \Octo\Store\SubmissionStore::getByFormId()
* @uses \Octo\Model\Submission
* @return \Octo\Model\Submission[]
*/
public function getFormSubmissions()
{
return Factory::getStore('Submission', 'Octo')->getByFormId($this->getId());
}



public static function getByPrimaryKey($value, $useConnection = 'read')
{
return Factory::getStore('Form', 'Octo')->getByPrimaryKey($value, $useConnection);
}


public static function getById($value, $useConnection = 'read')
{
return Factory::getStore('Form', 'Octo')->getById($value, $useConnection);
}


}
