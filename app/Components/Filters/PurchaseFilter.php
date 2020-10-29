<?php

namespace App\Components\Filters;

use Carbon\Carbon;

class PurchaseFilter extends QueryFilters
{
	public function purchase($value)
	{
		return is_array($value) ? $this->builder->whereIn('purchases.user_id', $value) : $this->builder->where('purchases.user_id', $value);
	}

	public function groupBy($value)
	{
		return $this->builder->groupBy($value);
	}

	public function user_id($value)
	{
		return is_array($value) ? $this->builder->whereIn('users.id', $value) : $this->builder->where('users.id', $value);
	}

	public function supplier_id($value)
	{
		return is_array($value) ? $this->builder->whereIn('suppliers.id', $value) : $this->builder->where('suppliers.id', $value);
	}

	public function date($value)
	{
		$value = explode(' ~ ', $value);
		$begin = Carbon::parse($value[0])->format('Y-m-d');
		$end   = Carbon::parse($value[1])->format('Y-m-d');
		return $this->builder->whereBetween('purchases.date', [$begin, $end]);
	}

	public function total_payment($value)
	{
		return is_array($value) ? $this->builder->whereIn('purchases.total_payment', $value) : $this->builder->where('purchases.total_payment', $value);
	}
	public function _total_payment($value)
	{
		return $this->builder->where('purchases.total_payment', 'like', '%'.$value.'%');
	}

	public function total_paid($value)
	{
		return is_array($value) ? $this->builder->whereIn('purchases.total_paid', $value) : $this->builder->where('purchases.total_paid', $value);
	}
	public function _total_paid($value)
	{
		return $this->builder->where('purchases.total_paid', 'like', '%'.$value.'%');
	}

	public function total_change($value)
	{
		return is_array($value) ? $this->builder->whereIn('purchases.total_change', $value) : $this->builder->where('purchases.total_change', $value);
	}
	public function _total_change($value)
	{
		return $this->builder->where('purchases.total_change', 'like', '%'.$value.'%');
	}

}