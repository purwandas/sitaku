<?php

namespace App\Components\Filters;

class UserFilter extends QueryFilters
{
	public function user($value)
	{
		return is_array($value) ? $this->builder->whereIn('users.name', $value) : $this->builder->where('users.name', $value);
	}

	public function groupBy($value)
	{
		return $this->builder->groupBy($value);
	}

	public function name($value)
	{
		return is_array($value) ? $this->builder->whereIn('users.name', $value) : $this->builder->where('users.name', $value);
	}
	public function _name($value)
	{
		return $this->builder->where('users.name', 'like', '%'.$value.'%');
	}

	public function email($value)
	{
		return is_array($value) ? $this->builder->whereIn('users.email', $value) : $this->builder->where('users.email', $value);
	}
	public function _email($value)
	{
		return $this->builder->where('users.email', 'like', '%'.$value.'%');
	}

	public function password($value)
	{
		return is_array($value) ? $this->builder->whereIn('users.password', $value) : $this->builder->where('users.password', $value);
	}
	public function _password($value)
	{
		return $this->builder->where('users.password', 'like', '%'.$value.'%');
	}

	public function role_id($value)
	{
		return is_array($value) ? $this->builder->whereIn('roles.id', $value) : $this->builder->where('users.id', $value);
	}

}