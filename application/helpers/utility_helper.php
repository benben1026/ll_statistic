<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('asset_url'))
{
	function asset_url(){
        return base_url().'public/';
	}    
}

if(!function_exists('css_url'))
{
	function css_url(){
        return asset_url().'css/css/';
	}    
}