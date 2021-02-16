<?php

namespace App\Components\Helpers;

use App\Components\Helpers\DatatableBuilderHelper;

class FormBuilderHelper
{

	protected $model = [];
	protected $config = [];
	protected $data = [];

	public function __construct($model = null,$data = [],$config = [])
	{
		$this->model = $model;
		$this->config = $config;
		$this->data = $data;
	}

	public static function setupDefaultConfig($name, $attributes = [], $select = false)
	{
		$default = config('formbuilder');
		$config = array_merge($default, $attributes);
		$config['elOptions'] = array_merge($default['elOptions'], $attributes['elOptions'] ?? []);
		$config['addons'] = empty($attributes['addons']) ? [] : array_merge($default['addons'], $attributes['addons']);

		// SETUP LABEL
		$config['textFormat'] = implode(' ', explode('_', $name));
		$config['labelText'] = $config['customLabel'] = $config['labelText'] ?? ucwords($config['textFormat']);
		$config['labelText'] = isset($config['elOptions']['required']) ? $config['labelText'] . ' ' . $config['requiredLabelText'] : $config['labelText'];
		$config['labelText'] = $config['boldLabel'] ? '<strong>' . $config['labelText'] . '</strong>' : $config['labelText'];

		// SETUP INFO
		if (!empty($config['info'])) {
			$config['info'] = str_replace('<<field>>', $config['info'], $config['infoTemplate']);
		}

		// SETUP FORM ALIGNMENT
		$config['labelContainerClass'] = $config['formAlignment'] === 'vertical' ? $config['labelContainerClassVertical'] : $config['labelContainerClass'] ?? $config['labelContainerClassHorizontal'];
		$config['inputContainerClass'] = $config['formAlignment'] === 'vertical' ? $config['inputContainerClassVertical'] : $config['inputContainerClass'] ?? $config['inputContainerClassHorizontal'];

		// SETUP ADDONS
		$config['addonsConfig'] = $config['addons'];

		$config['containerClass'] = $attributes['containerClass'] ?? 'row col-md-12 form-group';

		// FOR ELEMENT PROPERTY
		$config['elOptions']['id'] = $config['elOptions']['id'] ?? $name;
		$config['elOptions']['placeholder'] = $config['elOptions']['placeholder'] ?? ($select ? 'Select ' : 'Please enter ') . $config['customLabel']. ' here';

		// FOR FORMATING ARRAY elOptions INTO HTML ATTRIBUTES
		foreach ($config['elOptions'] as $attribute => $attributeValue) {
			$config['htmlOptions'] .= $attribute . '="' . $attributeValue . '" ';
		}

		return $config;
	}

	public static function arrayToHtmlAttribute(Array $elOptions) {
		$htmlAttributes = 'test ';
		foreach ($elOptions as $attribute => $attributeValue) {
			$htmlAttributes .= $attribute . '="' . $attributeValue . '" ';
		}
		return $htmlAttributes;
	}

	public function getGlobalConfig()
	{
	    return count($this->config) == 0 ? array_merge([
			'model'                 => $this->model,
			'useModal'              => true,
			'useDatatable'          => true,
			'useUtilities'          => true,
			'useFormBuilder'        => true,
			'useFilter'        		=> true,
			'setupFilterBuilder'    => [],
			'setupFormBuilder'      => [],
			'setupDatatableBuilder' => [
				'useDatatableAction' => true,
				'formPage'           => false,
				'creatable'          => true,
				'editable'           => true,
				'deletable'          => true,
				'lengthMenu'         => [[10, 25, 50, 100], [10, 25, 50, 100]],
				'fixedColumns'       => ['leftColumns' => '0', 'heightMatch' => 'none'],
				'order'              => [[1, 'DESC']],
	        ],
			'customVariables' => [],
			'injectView'      => [],
	    ],$this->config,$this->data) : $this->config;
	}

	public function setFormAlignmentVertical()
	{
		$config = $this->getGlobalConfig();
		$config['formAlignment'] = 'vertical';
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function setCustomVariables($array = [])
	{
		$config = $this->getGlobalConfig();
		$config['customVariables'] = $array;
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function injectView($array = [])
	{
	    // ->injectView(['inject/form_berkas_penilaian'=>['realisasi_id' => $realisasiId]])
		$config = $this->getGlobalConfig();
		$config['injectView'] = $array;
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function includeView($array = [])
	{
	    // ->includeView(['inject/form_berkas_penilaian'=>['realisasi_id' => $realisasiId]])
		$config = $this->getGlobalConfig();
		$config['includeView'] = $array;
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function useModal($v = true)
	{
		$config = $this->getGlobalConfig();
		if(!$v) unset($config['useModal']);
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function disableInfo($value = false)
	{
		$config = $this->getGlobalConfig();
		$config['setupDatatableBuilder']['disableInfo'] = $value;
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function setOrder($array = [[1, 'DESC']]) //table data order by column
	{
		$config = $this->getGlobalConfig();
		$config['setupDatatableBuilder']['order'] = $array;
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function setOrderDatatableColumns($array = []) //table column position, ex: [1 => 'name', 2 => 'age']
	{
		$config = $this->getGlobalConfig();
		$config['setupDatatableBuilder']['orderColumn'] = $array;
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function setLengthMenu($array = [[10, 25, 50, 100], [10, 25, 50, 100]])
	{
		$config = $this->getGlobalConfig();
		$config['setupDatatableBuilder']['lengthMenu'] = $array;
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function setFixedColumns($data = ['leftColumns' => '0', 'heightMatch' => 'none'])
	{
		$config = $this->getGlobalConfig();
		$config['setupDatatableBuilder']['fixedColumns'] = $data;
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function useFormBuilder($v = true)
	{
		$config = $this->getGlobalConfig();
		if(!$v){
			unset($config['setupFormBuilder']);
			$config['useFormBuilder'] = false;
		}
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function useDatatable($v = true)
	{
		$config = $this->getGlobalConfig();
		if(!$v){
			unset($config['setupDatatableBuilder']);
			unset($config['setupFilterBuilder']);
			$config['useDatatable'] = false;
			$config['useFilter'] = false;
		}
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function useFilter($v = true)
	{
		$config = $this->getGlobalConfig();
		if(!$v){
			unset($config['setupFilterBuilder']);
			$config['useFilter'] = false;
		}
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function useUtilities($v = true) //export etc
	{
		$config = $this->getGlobalConfig();
		if($config['useDatatable']){
			$config['useUtilities'] = $v;
			$this->config = $config;
			return $this->getRecentArray();
		}
		return $this->getRecentArray();
	}

	public function useDatatableAction($v = true) //table action edit,delete
	{
		$config = $this->getGlobalConfig();
		if($config['useDatatable']){
			$config['setupDatatableBuilder']['useDatatableAction'] = $v;
			$this->config = $config;
			return $this->getRecentArray();
		}
		throw new \Exception("Datatable must be used");
	}

	public function setFormPage($v = false) //form by page, not modal
	{
		$config = $this->getGlobalConfig();
		$config['setupDatatableBuilder']['formPage'] = $v;
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function setCreatable($v = true) //create handler
	{
		$config = $this->getGlobalConfig();
		$config['setupDatatableBuilder']['creatable'] = $v;
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function setEditable($v = true) //edit handler
	{
		$config = $this->getGlobalConfig();
		$config['setupDatatableBuilder']['editable'] = $v;
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function setDeletable($v = true) //edit handler
	{
		$config = $this->getGlobalConfig();
		$config['setupDatatableBuilder']['delestable'] = $v;
		$this->config = $config;
		return $this->getRecentArray();
	}

	public function setDontEditFormBuilder($v = '0')
	{
		$config = $this->getGlobalConfig();
		if($config['useFormBuilder']){
			$config['setupFormBuilder']['dontEdit'] = $v;
			$this->config = $config;
			return $this->getRecentArray();
		}

		throw new \Exception("Form Builder must be used");
	}

	public function setCustomFormBuilder($v = [])
	{
		$config = $this->getGlobalConfig();
		if($config['useFormBuilder']){
			$config['setupFormBuilder']['custom'] = $v;
			$this->config = $config;
			return $this->getRecentArray();
		}

		throw new \Exception("Form Builder must be used");
	}

	public function setExceptFormBuilderColumns($v = [])
	{
		$config = $this->getGlobalConfig();
		if($config['useFormBuilder']){
			$config['setupFormBuilder']['except'] = $v;
			$this->config = $config;
			return $this->getRecentArray();
		}
		throw new \Exception("Form Builder must be used");
	}

	public function setCustomFilterBuilder($v = [])
	{
		$config = $this->getGlobalConfig();
		if($config['useFilter']){
			$config['setupFilterBuilder']['custom'] = $v;
			$this->config = $config;
			return $this->getRecentArray();
		}

		throw new \Exception("Filter Builder must be used");
	}

	public function setDatatableName($v = null)
	{
		$config = $this->getGlobalConfig();
		if($config['useDatatable']){
			if($v != null){
				$config['setupDatatableBuilder']['name'] = $v;
				$config['setupFormBuilder']['name'] = $v;
				$this->config = $config;
				return $this->getRecentArray();
			}
			throw new \Exception("Datatable name can't be empty");
		}
		throw new \Exception("Datatable must be used");
	}

	public function setDatatableButtons($v = [])
	{
		$config = $this->getGlobalConfig();
		if($config['useDatatable'] && $config['useUtilities']){
			if(is_array($v)){
				$config['setupDatatableBuilder']['button'] = $v;
				$this->config = $config;
				return $this->getRecentArray();
			}
			throw new \Exception("Datatable button must be an array");
		}
		throw new \Exception("Datatable and Utilities must be used");
	}

	public function setDatatableOrder($v = [])
	{
		$config = $this->getGlobalConfig();
		if($config['useDatatable']){
			if(is_array($v)){
				$config['setupDatatableBuilder']['order'] = count($v) > 1 ? $v : array_merge($v,['asc']);
				$this->config = $config;
				return $this->getRecentArray();
			}
			throw new \Exception("Datatable order must be an array");
		}
		throw new \Exception("Datatable must be used");
	}

	public function setDatatableColumnDefs($v = [])
	{
		// setDatatableColumnDefs([0 => ['className','text-center']])
		$config = $this->getGlobalConfig();
		if($config['useDatatable']){
			if(is_array($v)){
				$tmp = [];
				foreach ($v as $key => $val) {
					$tmp[] = [ $val[0] => $val[1], 'targets' => ($key + 1) ];
				}
				$config['setupDatatableBuilder']['columnDefs'] = $tmp;
				$this->config = $config;
				return $this->getRecentArray();
			}
			throw new \Exception("Datatable order must be an array");
		}
		throw new \Exception("Datatable must be used");
	}

	public function setCustomDatatableUrl($v = '')
	{
		$config = $this->getGlobalConfig();
		if($config['useDatatable']){
			if($v != ''){
				$config['setupDatatableBuilder']['customDatatableUrl'] = $v;
				$this->config = $config;
				return $this->getRecentArray();
			}
			throw new \Exception("Datatable url can't empty");
		}
		throw new \Exception("Datatable must be used");
		# code...
	}

	public function setExceptDatatableColumns($v = [])
	{
		$config = $this->getGlobalConfig();
		if($config['useDatatable']){
			$config['setupDatatableBuilder']['except'] = $v;
			$this->config = $config;
			return $this->getRecentArray();
		}
		throw new \Exception("Datatable must be used");
	}

	public function setAdditionalDatatableColumns($v = [])
	{
		$config = $this->getGlobalConfig();
		if($config['useDatatable']){
			$config['setupDatatableBuilder']['additional'] = $v;
			$this->config = $config;
			return $this->getRecentArray();
		}
		throw new \Exception("Datatable must be used");
	}

	public function setAdditionalDatatableButtons($v = [], $position = 'left')
	{
		// availabel position : left & right
		$config = $this->getGlobalConfig();
		if($config['useDatatable']){
			if(is_array($v)){
				$config['setupDatatableBuilder']['button-'.$position] = DatatableBuilderHelper::button($v);
				$this->config = $config;
				return $this->getRecentArray();
			}
			throw new \Exception("Datatable button must be an array");
		}
		throw new \Exception("Datatable must be used");
	}

	public function setCustomDatatableColumns($v = [])
	{
		$config = $this->getGlobalConfig();
		if($config['useDatatable']){
			$config['setupDatatableBuilder']['custom'] = $v;
			$this->config = $config;
			return $this->getRecentArray();
		}
		throw new \Exception("Datatable must be used");
	}

	public function setExceptForeign($v = [])
	{
		$config = $this->getGlobalConfig();
		$config['exceptForeign'] = $v;
		$this->config = $config;
		return $this->getRecentArray();
		
		throw new \Exception("Datatable must be used");
	}

	public function setModel($v = null)
	{
		$config = $this->getGlobalConfig();
		if($v != null){
			$config['model'] = $v;
			$this->config = $config;
			return $this->getRecentArray();
		} 

		throw new \Exception("Model Can't Empty");
	}

	public function execFilter($v = 'true')
	{
		$config = $this->getGlobalConfig();
		if($v != null){
			$config['useFilter'] = $v;
			$this->config = $config;
			return $this->getRecentArray();
		} 

		throw new \Exception("Model Can't Empty");
	}

	public function setExceptFilter($v = 'true')
	{
		$config = $this->getGlobalConfig();
		if($v != null){
			$config['exceptFilter'] = $v;
			$this->config = $config;
			return $this->getRecentArray();
		} 

		throw new \Exception("Model Can't Empty");
	}

	public function setModule($v = null)
	{
		$config = $this->getGlobalConfig();
		if($v != null){
			$config['module'] = $v;
			$this->config = $config;
			return $this->getRecentArray();
		} 

		throw new \Exception("Model Can't Empty");
	}

	public function mergeArray($v = [])
	{
		$config = $this->getGlobalConfig();
		if($v != null){
			$this->config = array_merge($v,$config);
			return $this->getRecentArray();
		} 

		throw new \Exception("Parameter must be an Array and can't be empty");
	}

	public function getRecentArray()
	{
		return new self($this->model,$this->data,$this->config);
	}

	public function get()
	{
		return $this->getGlobalConfig();
	}

}