<?php
/**
	@author: Krystian Podemski, impSolutions.pl
	@release: 07.2013
	@version: 1.0.0
	@desc: Do not let index your Terms of Service by Google
**/
if (!defined('_PS_VERSION_'))
	exit;

class imp_blocktos extends Module
{

	public function __construct()
	{
		$this->name = 'imp_blocktos';
		if (version_compare(_PS_VERSION_, '1.4', '>'))
			$this->tab = 'front_office_features';
		else
			$this->tab = 'impSolutions';

		$this->version = '1.0.0';

		if (version_compare(_PS_VERSION_, '1.4', '>'))
			$this->author = 'impSolutions.pl';

		parent::__construct();

		$this->displayName = $this->l('No Index Terms of Service');
		$this->description = $this->l('Do not let index your Terms of Service by Google');
	}

	public function install()
	{
		if (!parent::install()
			OR !$this->registerHook('header')
			OR !Configuration::updateValue('BLOCKTOS_CMS', '1')
		)
			return false;
		return true;

	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('BLOCKTOS_CMS')
			OR !parent::uninstall())
			return false;
		return true;
	} 
	

	public function hookHeader()
	{
		global $smarty;

		$cms = Tools::getValue('id_cms', 0);

		if($cms && $cms > 0)
		{
			if($cms == Configuration::get('BLOCKTOS_CMS'))
			{
				$smarty->assign('nobots', true);
			}
		}
	}
      
    public function getContent()
    {
    	$this->_html = '';

    	if(Tools::isSubmit('submitSettings'))
    	{
    		foreach($_POST as $key => $value)
    		{
				Configuration::updateValue($key,$value);
    		}

    		$this->_html .= $this->displayConfirmation($this->l('Success'));
    	}
    	
    	$this->_html .= '<h2>'.$this->displayName.'</h2>';
    	$this->_html .= '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">';
    	$this->_html .= '<fieldset>';
    	$this->_html .= '<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>';

    	#page
    	$cms_pages = CMS::listCms();
    	$this->_html .= '<label>'.$this->l('CMS page that you want to block').'</label><div class="margin-form">';
    	$this->_html .= '<select name="BLOCKTOS_CMS">';
    	foreach($cms_pages as $page):
    		$selected = Configuration::get('BLOCKTOS_CMS') == $page['id_cms'] ? 'selected="selected"' : '';
    		$this->_html .= '<option value="'.$page['id_cms'].'" '.$selected.'>'.$page['meta_title'].'</option>';
		endforeach;
		$this->_html .= '</select></div>';

		$this->_html .= '<div class="margin-form">';
		$this->_html .= '<input type="submit" value="'.$this->l('Save').'" name="submitSettings" class="button">';
		$this->_html .= '</div>';

    	$this->_html .= '</fieldset>';
    	$this->_html .= '</form>';

    	$this->_html .= '<div style="width: 351px; margin: 20px auto">';
    	$this->_html .= '<a href="http://www.facebook.com/impSolutionsPL" title="" target="_blank"><img alt="" src="'.$this->_path.'impsolutions.png" /></a>';
    	$this->_html .= '</div>';

    	return $this->_html;
    }  
}
