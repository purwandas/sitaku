<?php

namespace App\Components\Filters;

class SupplierFilter extends QueryFilters
{
	public function supplier($value)
	{
		return is_array($value) ? $this->builder->whereIn('suppliers.name', $value) : $this->builder->where('suppliers.name', $value);
	}

	public function groupBy($value)
	{
		return $this->builder->groupBy($value);
	}

	public function name($value)
	{
		return is_array($value) ? $this->builder->whereIn('suppliers.name', $value) : $this->builder->where('suppliers.name', $value);
	}
	public function _name($value)
	{
		return $this->builder->where('suppliers.name', 'like', '%'.$value.'%');
	}

	public function address($value)
	{
		return is_array($value) ? $this->builder->whereIn('suppliers.address', $value) : $this->builder->where('suppliers.address', $value);
	}
	public function _address($value)
	{
		return $this->builder->where('suppliers.address', 'like', '%'.$value.'%');
	}

	public function phone($value)
	{
		return is_array($value) ? $this->builder->whereIn('suppliers.phone', $value) : $this->builder->where('suppliers.phone', $value);
	}
	public function _phone($value)
	{
		return $this->builder->where('suppliers.phone', 'like', '%'.$value.'%');
	}

	public function production_id($value)
	{
		return is_array($value) ? $this->builder->whereIn('productions.id', $value) : $this->builder->where('suppliers.id', $value);
	}

}