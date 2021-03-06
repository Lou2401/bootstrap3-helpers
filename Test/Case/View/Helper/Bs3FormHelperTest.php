<?php
/**
 * FormHelperTest file
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Codaxis (http://codaxis.com)
 * @author        augusto-cdxs (https://github.com/augusto-cdxs/
 * @link          https://github.com/Codaxis/parsley-helper
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('ClassRegistry', 'Utility');
App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('Model', 'Model');
App::uses('Security', 'Utility');
App::uses('CakeRequest', 'Network');
App::uses('HtmlHelper', 'View/Helper');
App::uses('Bs3FormHelper', 'Bs3Helpers.View/Helper');
App::uses('FormHelper', 'Helpers.View/Helper');
App::uses('Router', 'Routing');

/**
 * ContactTestController class
 *
 * @package       Cake.Test.Case.View.Helper
 */
class ContactTestController extends Controller {

/**
 * uses property
 *
 * @var mixed null
 */
	public $uses = null;
}

/**
 * Contact class
 *
 * @package       Cake.Test.Case.View.Helper
 */
class Contact extends CakeTestModel {

/**
 * useTable property
 *
 * @var boolean
 */
	public $useTable = false;

/**
 * Default schema
 *
 * @var array
 */
	protected $_schema = array(
		'id' => array('type' => 'integer', 'null' => '', 'default' => '', 'length' => '8'),
		'name' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '255'),
		'email' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '255'),
		'phone' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '255'),
		'gender' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '1'),
		'active' => array('type' => 'boolean', 'null' => '', 'default' => ''),
		'password' => array('type' => 'string', 'null' => '', 'default' => '', 'length' => '255'),
		'published' => array('type' => 'date', 'null' => true, 'default' => null, 'length' => null),
		'created' => array('type' => 'date', 'null' => '1', 'default' => '', 'length' => ''),
		'updated' => array('type' => 'datetime', 'null' => '1', 'default' => '', 'length' => null),
		'age' => array('type' => 'integer', 'null' => '', 'default' => '', 'length' => null)
	);

/**
 * validate property
 *
 * @var array
 */
	public $validate = array(
		'non_existing' => array(),
		'idontexist' => array(),
		'imrequired' => array('rule' => array('between', 5, 30), 'allowEmpty' => false),
		'imrequiredonupdate' => array('notEmpty' => array('rule' => 'alphaNumeric', 'on' => 'update')),
		'imrequiredoncreate' => array('required' => array('rule' => 'alphaNumeric', 'on' => 'create')),
		'imrequiredonboth' => array(
			'required' => array('rule' => 'alphaNumeric'),
		),
		'string_required' => 'notEmpty',
		'imalsorequired' => array('rule' => 'alphaNumeric', 'allowEmpty' => false),
		'imrequiredtoo' => array('rule' => 'notEmpty'),
		'required_one' => array('required' => array('rule' => array('notEmpty'))),
		'imnotrequired' => array('required' => false, 'rule' => 'alphaNumeric', 'allowEmpty' => true),
		'imalsonotrequired' => array(
			'alpha' => array('rule' => 'alphaNumeric', 'allowEmpty' => true),
			'between' => array('rule' => array('between', 5, 30)),
		),
		'imalsonotrequired2' => array(
			'alpha' => array('rule' => 'alphaNumeric', 'allowEmpty' => true),
			'between' => array('rule' => array('between', 5, 30), 'allowEmpty' => true),
		),
		'imnotrequiredeither' => array('required' => true, 'rule' => array('between', 5, 30), 'allowEmpty' => true),
		'iamrequiredalways' => array(
			'email' => array('rule' => 'email'),
			'rule_on_create' => array('rule' => array('maxLength', 50), 'on' => 'create'),
			'rule_on_update' => array('rule' => array('between', 1, 50), 'on' => 'update'),
		),
		'boolean_field' => array('rule' => 'boolean')
	);

/**
 * schema method
 *
 * @return void
 */
	public function setSchema($schema) {
		$this->_schema = $schema;
	}

/**
 * hasAndBelongsToMany property
 *
 * @var array
 */
	public $hasAndBelongsToMany = array('ContactTag' => array('with' => 'ContactTagsContact'));

/**
 * hasAndBelongsToMany property
 *
 * @var array
 */
	public $belongsTo = array('User' => array('className' => 'UserForm'));
}

/**
 * ContactTagsContact class
 *
 * @package       Cake.Test.Case.View.Helper
 */
class ContactTagsContact extends CakeTestModel {

/**
 * useTable property
 *
 * @var boolean
 */
	public $useTable = false;

/**
 * Default schema
 *
 * @var array
 */
	protected $_schema = array(
		'contact_id' => array('type' => 'integer', 'null' => '', 'default' => '', 'length' => '8'),
		'contact_tag_id' => array(
			'type' => 'integer', 'null' => '', 'default' => '', 'length' => '8'
		)
	);

/**
 * schema method
 *
 * @return void
 */
	public function setSchema($schema) {
		$this->_schema = $schema;
	}

}

/**
 * FormHelperTest class
 *
 * @package       Cake.Test.Case.View.Helper
 * @property FormHelper $Form
 */
class Bs3FormHelperTest extends CakeTestCase {


/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		Configure::write('Config.language', 'eng');
		Configure::write('App.base', '');
		Configure::delete('Asset');
		$this->Controller = new ContactTestController();
		$this->View = new View($this->Controller);

		$this->Form = new Bs3FormHelper($this->View);
		$this->Form->Html = new HtmlHelper($this->View);
		$this->Form->request = new CakeRequest('contacts/add', false);
		$this->Form->request->here = '/contacts/add';
		$this->Form->request['action'] = 'add';
		$this->Form->request->webroot = '';
		$this->Form->request->base = '';

		ClassRegistry::addObject('Contact', new Contact());

		$this->oldSalt = Configure::read('Security.salt');

		$this->dateRegex = array(
			'daysRegex' => 'preg:/(?:<option value="0?([\d]+)">\\1<\/option>[\r\n]*)*/',
			'monthsRegex' => 'preg:/(?:<option value="[\d]+">[\w]+<\/option>[\r\n]*)*/',
			'yearsRegex' => 'preg:/(?:<option value="([\d]+)">\\1<\/option>[\r\n]*)*/',
			'hoursRegex' => 'preg:/(?:<option value="0?([\d]+)">\\1<\/option>[\r\n]*)*/',
			'minutesRegex' => 'preg:/(?:<option value="([\d]+)">0?\\1<\/option>[\r\n]*)*/',
			'meridianRegex' => 'preg:/(?:<option value="(am|pm)">\\1<\/option>[\r\n]*)*/',
		);

		Configure::write('Security.salt', 'foo!');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Form->Html, $this->Form, $this->Controller, $this->View);
		Configure::write('Security.salt', $this->oldSalt);
	}

/**
 * testFormCreate method
 *
 * @return void
 */
	public function testFormCreate() {
		$this->Form->request['_Token'] = array('key' => 'testKey');
		$encoding = strtolower(Configure::read('App.encoding'));
		$result = $this->Form->create('Contact');
		$expected = array(
			'form' => array('action' => '/contacts/add', 'role' => 'form', 'id' => 'ContactAddForm', 'method' => 'post', 'accept-charset' => $encoding),
			'div' => array('style' => 'display:none;'),
			array('input' => array('type' => 'hidden', 'name' => '_method', 'value' => 'POST')),
			array('input' => array(
				'type' => 'hidden', 'name' => 'data[_Token][key]', 'value' => 'testKey', 'id'
			)),
			'/div',
		);
		$this->assertTags($result, $expected);

		$result = $this->Form->create('Contact');
		$expected = array(
			'form' => array('action' => '/contacts/add', 'role' => 'form', 'id' => 'ContactAddForm', 'method' => 'post', 'accept-charset' => $encoding),
			'div' => array('style' => 'display:none;'),
			array('input' => array('type' => 'hidden', 'name' => '_method', 'value' => 'POST')),
			array('input' => array(
				'type' => 'hidden', 'name' => 'data[_Token][key]', 'value' => 'testKey', 'id'
			)),
			'/div',
		);
		$this->assertTags($result, $expected);
		$this->assertTags($this->Form->getFormStyle(), 'default');

		$result = $this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$expected = array(
			'form' => array('action' => '/contacts/add', 'role' => 'form', 'class' => 'form-horizontal', 'id' => 'ContactAddForm', 'method' => 'post', 'accept-charset' => $encoding),
			'div' => array('style' => 'display:none;'),
			array('input' => array('type' => 'hidden', 'name' => '_method', 'value' => 'POST')),
			array('input' => array(
				'type' => 'hidden', 'name' => 'data[_Token][key]', 'value' => 'testKey', 'id'
			)),
			'/div',
		);
		$this->assertTags($result, $expected);
		$this->assertTags($this->Form->getFormStyle(), 'horizontal');

		$result = $this->Form->create('Contact', array('formStyle' => 'inline'));
		$expected = array(
			'form' => array('action' => '/contacts/add', 'role' => 'form', 'class' => 'form-inline', 'id' => 'ContactAddForm', 'method' => 'post', 'accept-charset' => $encoding),
			'div' => array('style' => 'display:none;'),
			array('input' => array('type' => 'hidden', 'name' => '_method', 'value' => 'POST')),
			array('input' => array(
				'type' => 'hidden', 'name' => 'data[_Token][key]', 'value' => 'testKey', 'id'
			)),
			'/div',
		);
		$this->assertTags($result, $expected);
		$this->assertTags($this->Form->getFormStyle(), 'inline');

		Configure::write('Bs3Form.inputDefaults.my-style', array());
		$result = $this->Form->create('Contact', array('formStyle' => 'my-style'));
		$expected = array(
			'form' => array('action' => '/contacts/add', 'role' => 'form', 'id' => 'ContactAddForm', 'method' => 'post', 'accept-charset' => $encoding),
			'div' => array('style' => 'display:none;'),
			array('input' => array('type' => 'hidden', 'name' => '_method', 'value' => 'POST')),
			array('input' => array(
				'type' => 'hidden', 'name' => 'data[_Token][key]', 'value' => 'testKey', 'id'
			)),
			'/div',
		);
		$this->assertTags($result, $expected);
		$this->assertTags($this->Form->getFormStyle(), 'my-style');
	}

/**
 * testFormCreate method
 *
 * @return void
 */
	public function testFormEnd() {
		/*$this->Form->request['_Token'] = array('key' => 'testKey');
		$encoding = strtolower(Configure::read('App.encoding'));
		$this->Form->create('Contact');
		$result = $this->Form->end();
		$expected = array(
			array('div' => array('style' => 'display:none;')),
			array('input' => array(
				'type' => 'hidden', 'name' => 'data[_Token][fields]',
				'value' => 'preg:/\d+/', 'id' => 'preg:/TokenFields\d+/'
			)),
			array('input' => array(
				'type' => 'hidden', 'name' => 'data[_Token][unlocked]',
				'value' => '', 'id' => 'preg:/TokenUnlocked\d+/'
			)),
			'/div',
			'/form',
		);
		$this->assertTags($result, $expected);
		exit;*/

		$this->Form->create('Contact');
		$result = $this->Form->end('Submit');
		$expected = array(
			'div' => array('class' => 'submit'),
			'input' => array('type' => 'submit', 'value' => 'Submit'),
			'/div',
			'/form'
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->end('Submit');
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('div' => array('class' => 'col-sm-10 col-sm-offset-2')),
					array('input' => array('type' => 'submit', 'value' => 'Submit')),
				'/div',
			'/form'
		);
		$this->assertTags($result, $expected);
	}

/**
 * testInputWithForm method
 *
 * @return void
 */
	public function testInputWithForm() {
		// Default form
		$this->Form->create('Contact');
		$result = $this->Form->input('name');
		$result .= $this->Form->input('email');
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
			'/div',
			array('div' => array('class' => 'form-group')),
				array('label' => array('for' => 'ContactEmail', 'class' => 'control-label')),
					'Email',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][email]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'email', 'id' => 'ContactEmail',
				)),
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testInputWithForm method
 *
 * @return void
 */
	public function testInput() {
		// Default form
		$this->Form->create('Contact');
		$result = $this->Form->input('name');
		$expected = array(
			'div' => array('class' => 'form-group'),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
			'/div',
		);
		$this->assertTags($result, $expected);

		// Inline form
		$this->Form->create('Contact', array('formStyle' => 'inline'));
		$result = $this->Form->input('name');
		$expected = array(
			'div' => array('class' => 'form-group'),
				array('label' => array('for' => 'ContactName', 'class' => 'sr-only')),
					'Name',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control',
					'maxlength' => '255', 'type' => 'text', 'id' => 'ContactName'
				)),
			'/div',
		);
		$this->assertTags($result, $expected);

		// Horizontal form
		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->input('name');
		$expected = array(
			'div' => array('class' => 'form-group'),
				array('label' => array('for' => 'ContactName', 'class' => 'col-sm-2 control-label')),
					'Name',
				'/label',
				array('div' => array('class' => 'col-sm-10')),
					array('input' => array(
						'name' => 'data[Contact][name]', 'class' => 'form-control',
						'maxlength' => '255', 'type' => 'text', 'id' => 'ContactName'
					)),
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testInputLabel method
 *
 * @return void
 */
	public function testInputLabel() {
		$this->Form->create('Contact');
		$result = $this->Form->input('name', array('label' => 'My label'));
		$expected = array(
			'div' => array('class' => 'form-group'),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'My label',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
			'/div',
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->input('name', array('label' => array('text' => 'My label', 'class' => 'my-label-class')));
		$expected = array(
			'div' => array('class' => 'form-group'),
				array('label' => array('for' => 'ContactName', 'class' => 'my-label-class')),
					'My label',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testHidden method
 *
 * @return void
 */
	public function testHidden() {
		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->input('id');
		$expected = array(
			array('input' => array('type' => 'hidden', 'name' => 'data[Contact][id]', 'id' => 'ContactId'))
		);
		$this->assertTags($result, $expected);
	}

/**
 * testCheckbox method
 *
 * @return void
 */
	public function testCheckboxAndRadio() {
		$this->Form->create('Contact');
		$result = $this->Form->input('active', array('label' => false, 'checkboxLabel' => 'My checkbox label'));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('div' => array('class' => 'checkbox')),
					'<label',
						array('input' => array('type' => 'hidden', 'name' => 'data[Contact][active]', 'id' => 'ContactActive_', 'value' => 0)),
						array('input' => array('type' => 'checkbox', 'name' => 'data[Contact][active]', 'value' => 1, 'id' => 'ContactActive')),
						' My checkbox label',
					'/label',
				'/div',
			'/div'
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->input('active', array('label' => 'Horizontal label', 'checkboxLabel' => 'My checkbox label'));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('label' => array('for' => 'ContactActive', 'class' => 'col-sm-2 control-label')),
					'Horizontal label',
				'/label',
				array('div' => array('class' => 'col-sm-10')),
					array('div' => array('class' => 'checkbox')),
						'<label',
							array('input' => array('type' => 'hidden', 'name' => 'data[Contact][active]', 'id' => 'ContactActive_', 'value' => 0)),
							array('input' => array('type' => 'checkbox', 'name' => 'data[Contact][active]', 'value' => 1, 'id' => 'ContactActive')),
							' My checkbox label',
						'/label',
					'/div',
				'/div',
			'/div'
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact');
		$result = $this->Form->input('gender', array('label' => false, 'type' => 'radio', 'options' => array('F' => 'Female', 'M' => 'Male')));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('input' => array('type' => 'hidden', 'name' => 'data[Contact][gender]', 'id' => 'ContactGender_', 'value' => '')),
				array('div' => array('class' => 'radio')),
					array('label' => array('for' => 'ContactGenderF')),
						array('input' => array('type' => 'radio', 'name' => 'data[Contact][gender]', 'id' => 'ContactGenderF', 'value' => 'F')),
						' Female',
					'/label',
				'/div',
				array('div' => array('class' => 'radio')),
					array('label' => array('for' => 'ContactGenderM')),
						array('input' => array('type' => 'radio', 'name' => 'data[Contact][gender]', 'id' => 'ContactGenderM', 'value' => 'M')),
						' Male',
					'/label',
				'/div',
			'/div'
		);
		$this->assertTags($result, $expected);
	}

/**
 * testRadio method
 *
 * @return void
 */
	public function testRadio() {
		$this->Form->create('Contact');
		$result = $this->Form->input('active', array('label' => false, 'checkboxLabel' => 'My checkbox label'));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('div' => array('class' => 'checkbox')),
					'<label',
						array('input' => array('type' => 'hidden', 'name' => 'data[Contact][active]', 'id' => 'ContactActive_', 'value' => 0)),
						array('input' => array('type' => 'checkbox', 'name' => 'data[Contact][active]', 'value' => 1, 'id' => 'ContactActive')),
						' My checkbox label',
					'/label',
				'/div',
			'/div'
		);
		$this->assertTags($result, $expected);

		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->input('active', array('label' => 'Horizontal label', 'checkboxLabel' => 'My checkbox label'));
		$expected = array(
			array('div' => array('class' => 'form-group')),
				array('label' => array('for' => 'ContactActive', 'class' => 'col-sm-2 control-label')),
					'Horizontal label',
				'/label',
				array('div' => array('class' => 'col-sm-10')),
					array('div' => array('class' => 'checkbox')),
						'<label',
							array('input' => array('type' => 'hidden', 'name' => 'data[Contact][active]', 'id' => 'ContactActive_', 'value' => 0)),
							array('input' => array('type' => 'checkbox', 'name' => 'data[Contact][active]', 'value' => 1, 'id' => 'ContactActive')),
							' My checkbox label',
						'/label',
					'/div',
				'/div',
			'/div'
		);
		$this->assertTags($result, $expected);
	}

/**
 * testStaticControl method
 *
 * @return void
 */
	public function testStaticControl() {
		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->staticControl('The label', 'The html content');
		$expected = array(
			'div' => array('class' => 'form-group'),
				array('label' => array('class' => 'col-sm-2 control-label')),
					'The label',
				'/label',
				array('div' => array('class' => 'col-sm-10')),
					array('div' => array('class' => 'form-control-static')),
						'The html content',
					'/div',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testInputWrapping method
 *
 * @return void
 */
	public function testInputWrapping() {
		$this->Form->create('Contact', array('formStyle' => 'horizontal'));
		$result = $this->Form->input('name', array('externalWrap' => 'col-sm-10', 'wrap' => 'col-sm-6'));
		$expected = array(
			'div' => array('class' => 'form-group'),
				array('label' => array('for' => 'ContactName', 'class' => 'col-sm-2 control-label')),
					'Name',
				'/label',
				array('div' => array('class' => 'col-sm-10')),
					array('div' => array('class' => 'row')),
						array('div' => array('class' => 'col-sm-6')),
							array('input' => array(
								'name' => 'data[Contact][name]', 'class' => 'form-control',
								'maxlength' => '255', 'type' => 'text', 'id' => 'ContactName'
							)),
						'/div',
					'/div',
				'/div',
			'/div',
		);
		$this->assertTags($result, $expected);
	}

/**
 * testFeedback method
 *
 * @return void
 */
	public function testFeedback() {
		$this->Form->create('Contact');
		$result = $this->Form->input('name', array('feedback' => 'fa-check'));
		$expected = array(
			array('div' => array('class' => 'form-group has-feedback')),
				array('label' => array('for' => 'ContactName', 'class' => 'control-label')),
					'Name',
				'/label',
				array('input' => array(
					'name' => 'data[Contact][name]', 'class' => 'form-control', 'maxlength' => '255',
					'type' => 'text', 'id' => 'ContactName',
				)),
				array('i' => array('class' => 'fa fa-check form-control-feedback')),
				'/i',
			'/div',
		);
		$this->assertTags($result, $expected);
	}
}
