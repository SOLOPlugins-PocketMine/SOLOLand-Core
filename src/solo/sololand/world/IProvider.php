<?php

namespace solo\sololand\world;

interface IProvider{
	
	public function __construct(string $path);
	
	public function load($condition = null);
	
	public function save(array $array, $condition = null);
	
}