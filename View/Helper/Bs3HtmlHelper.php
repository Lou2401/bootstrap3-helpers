<?php

App::uses('HtmlHelper', 'View/Helper');
App::uses('Hash', 'Utility');

class Bs3HtmlHelper extends HtmlHelper {


	protected $_blockOptions = array();

/**
 * Helpers
 *
 * @var array
 */
	public $helpers = array(
		'Html'
	);

	protected $_blockRendering = false;

/**
 * Helpers
 *
 * @param string
 * @param array
 * @return string
 */
	public function panelHeading($html, $options = array()) {
		$defaults = array('class' => '');
		$options = array_merge($defaults, $options);
		$options = $this->addClass($options, 'panel-heading');
		return $this->Html->tag('div', $html, $options);
	}

	public function panelBody($html, $options = array()) {
		$defaults = array('class' => '');
		$options = array_merge($defaults, $options);
		$options = $this->addClass($options, 'panel-body');
		return $this->Html->tag('div', $html, $options);
	}

	public function panel($headingHtml, $bodyHtml = null, $options = array()) {
		// Block support
		$renderChild = true;
		if ($this->_blockRendering) {
			$options = $bodyHtml;
			$html = $headingHtml;
			$renderChild = false;
		}

		$defaults = array(
			'class' => 'panel-default', 'headingOptions' => array(), 'bodyOptions' => array(),
			'wrapHeading' => true, 'wrapBody' => true
		);
		$options = Hash::merge($defaults, $options);
		$options = $this->addClass($options, 'panel');

		if ($renderChild) {
			$heading = $options['wrapHeading'] ? $this->panelHeading($headingHtml, $options['headingOptions']) : $headingHtml;
			$body = $options['wrapBody'] ? $this->panelBody($bodyHtml, $options['bodyOptions']) : $bodyHtml;
			$html = $heading . $body;
		}

		unset($options['headingOptions'], $options['bodyOptions'], $options['wrapHeading'], $options['wrapBody']);
		return $this->Html->tag('div', $html, $options);
	}

	public function accordion($items = array(), $options = array()) {
		$defaults = array(
			'class' => '', 'id' => str_replace('.', '', uniqid('accordion_', true)),
		);
		$options = Hash::merge($defaults, $options);
		$options = $this->addClass($options, 'panel-group');

		if (is_array($items)) {
			$html = '';
			foreach ($items as $itemHeading => $itemBody) {
				$html .= $this->accordionItem($itemHeading, $itemBody, array('accordionId' => $options['id']));
			}
		} else {
			$html= $items;
		}

		return $this->Html->tag('div', $html, $options);
	}

	public function accordionItem($titleHtml, $bodyHtml = null, $options = array()) {
		// Block support
		$renderChild = true;
		if (is_array($titleHtml)) {

		} elseif (is_array($bodyHtml)) {
			$options = $bodyHtml;
			$html = $headingHtml;
			$renderChild = false;
		}

		$itemBodyId = str_replace('.', '', uniqid('accordion_body_', true));
		$titleLink = $this->Html->link($titleHtml, '#' . $itemBodyId, array(
			'data-toggle' => 'collapse', 'data-parent' => '#' . $options['accordionId']
		));
		$heading = $this->Html->tag('h4', $titleLink, array('class' => 'panel-title'));
		$body = $this->Html->tag('div', $this->panelBody($bodyHtml), array(
			'class' => 'panel-collapse collapse in', 'id' => $itemBodyId
		));

		return $this->panel($heading, $body, array('wrapBody' => false));
	}

	public function dropdown($toggle, $links = array(), $options = array()) {
		$defaults = array(
			'class' => '',
			'toggleClass' => 'btn btn-default',
		);
		$options = Hash::merge($defaults, $options);
		$options = $this->addClass($options, 'dropdown');

		if ($this->_blockRendering) {
			$itemsHtml = $toggle;
			$toggle = $links;
		} else {
			if (is_array($links)) {
				$itemsHtml = '';
				foreach ($links as $item => $itemOptions) {
					$itemHtml = $before = $after = '';
					$liOptions = array();
					if (is_array($itemOptions)) {
						if ($this->_extractOption('active', $itemOptions)) {
							$liOptions['class'] = 'active';
						}

						if ($divider = $this->_extractOption('divider', $itemOptions)) {
							if ($divider === true) {
								$liOptions['class'] = 'divider';
							} else {
								${$divider} = $this->Html->tag('li', '', array('class' => 'divider'));
							}
						}
						$itemHtml = $this->_extractOption('html', $itemOptions, '');
					} else {
						$itemHtml = $itemOptions;
					}

					$itemsHtml .= $before . $this->Html->tag('li', $itemHtml, $liOptions) . $after;
				}
			} else {
				$itemsHtml= $links;
			}
		}

		$toggleOptions = array(
			'type' => 'button',
			'class' => $options['toggleClass'],
			'data-toggle' => 'dropdown'
		);
		$toggleOptions = $this->addClass($toggleOptions, 'sr-only dropdown-toggle');
		$toggleHtml = $this->Html->tag('button', $toggle . ' <span class="caret"></span>', $toggleOptions);
		unset($options['toggleClass']);
		$itemsHtml = $this->Html->tag('ul', $itemsHtml, array('class'=>'dropdown-menu'));

		$html = $toggleHtml . $itemsHtml;

		return $this->Html->tag('div', $html, $options);
	}

/*
  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Action</a></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Another action</a></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Something else here</a></li>
    <li role="presentation" class="divider"></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Separated link</a></li>
  </ul>
  */

/**
 * Handles custom method calls, like findBy<field> for DB models,
 * and custom RPC calls for remote data sources.
 *
 * @param string $method Name of method to call.
 * @param array $params Parameters for the method.
 * @return mixed Whatever is returned by called method
 */
	public function __call($method, $params) {
		if (substr($method, -5) == 'Start') {
			$call = substr($method, 0, strlen($method) - 5);
			if (method_exists($this, $call)) {
				$this->_View->assign($call . '_block', null);
				$this->_blockOptions[$call . '_block_options'] = isset($params[0]) ? $params[0] : array();
				$this->_View->start($call . '_block');
				$this->_blockRendering = true;
			}
		} elseif (substr($method, -3) == 'End') {
			$call = substr($method, 0, strlen($method) - 3);
			if (method_exists($this, $call)) {
				$this->_View->end($call . '_block');
				$html = $this->_View->fetch($call . '_block');
				$generatedHtml = $this->$call($html, $this->_blockOptions[$call . '_block_options']);
				$this->_blockRendering = false;
				return $generatedHtml;
			}
		}
	}



/**
 * Extracts a single option from an options array.
 *
 * @param string $name The name of the option to pull out.
 * @param array $options The array of options you want to extract.
 * @param mixed $default The default option value
 * @return mixed the contents of the option or default
 */
	protected function _extractOption($name, $options, $default = null) {
		if (array_key_exists($name, $options)) {
			return $options[$name];
		}
		return $default;
	}
}