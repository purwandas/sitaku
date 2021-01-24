<?php

namespace App\Components\Filters;

class ProductionFilter extends QueryFilters
{
	public function production($value)
	{
		return is_array($value) ? $this->builder->whereIn('productions.name', $value) : $this->builder->where('productions.name', $value);
	}

	public function _production($value)
	{
		return $this->builder->where('productions.name', 'like', '%'.$value.'%');
	}

	public function groupBy($value)
	{
		return $this->builder->groupBy($value);
	}

	public function name($value)
	{
		return is_array($value) ? $this->builder->whereIn('productions.name', $value) : $this->builder->where('productions.name', $value);
	}
	public function _name($value)
	{
		return $this->builder->where('productions.name', 'like', '%'.$value.'%');
	}

}