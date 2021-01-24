<?php

namespace App\Components\Filters;

class UnitFilter extends QueryFilters
{
	public function unit($value)
	{
		return is_array($value) ? $this->builder->whereIn('units.name', $value) : $this->builder->where('units.name', $value);
	}

	public function _unit($value)
	{
		return $this->builder->where('units.name', 'like', '%'.$value.'%');
	}

	public function groupBy($value)
	{
		return $this->builder->groupBy($value);
	}

	public function name($value)
	{
		return is_array($value) ? $this->builder->whereIn('units.name', $value) : $this->builder->where('units.name', $value);
	}
	public function _name($value)
	{
		return $this->builder->where('units.name', 'like', '%'.$value.'%');
	}

	public function conversion($value)
	{
		return is_array($value) ? $this->builder->whereIn('units.conversion', $value) : $this->builder->where('units.conversion', $value);
	}
	public function _conversion($value)
	{
		return $this->builder->where('units.conversion', 'like', '%'.$value.'%');
	}

}