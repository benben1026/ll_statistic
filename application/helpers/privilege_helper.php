<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('load_privilege_list'))
{
    function load_privilege_list($priv_dec = 0, $inherit_dec = 0)
    {
    	$output = array();
    	$ci  =& get_instance();
    	$query = $ci->db->query("SELECT * FROM privilege ORDER BY `index`");
    	$rows = $query->result_array();
    	$priv_bin = decbin($priv_dec);
        $inherit_bin = decbin($inherit_dec);
    	//print $priv_bin;
    	for($i = 0; $i < count($rows); $i++){
            if($i < strlen($inherit_bin)){
                if($i < strlen($priv_bin))
                    $output[$rows[$i]['name']] = $inherit_bin[strlen($inherit_bin) - $i - 1] == 1 ? -1 : intval($priv_bin[strlen($priv_bin) - $i - 1]);
                else
                    $output[$rows[$i]['name']] = $inherit_bin[strlen($inherit_bin) - $i - 1] == 1 ? -1 : 0;
            }else{
                if($i < strlen($priv_bin))
                    $output[$rows[$i]['name']] = intval($priv_bin[strlen($priv_bin) - $i - 1]);
                else
                    $output[$rows[$i]['name']] = 0;
            }
    	}
        return $output;
    }
}

if(! function_exists('load_privilege'))
{
    function load_privilege(){
        $ci  =& get_instance();
        $query = $ci->db->query("SELECT * FROM  privilege ORDER BY `index`");
        $rows = $query->result_array();
        return $rows;
    }
}

if(! function_exists('update_privilege'))
{
    function update_privilege($privilege, $role_id){
        $active = $privilege[0] == 1 ? 1 : 0;
        $if_inherit = "";
        $priv = "";

        for($i = 1; $i < count($privilege); $i++){
            if($privilege[$i] == -1){
                $if_inherit .= "1";
                $priv .= "0";
            }else{
                $if_inherit .= "0";
                $priv .= $privilege[$i] == 1 ? "1" : "0";
            }
        }
        
        $ci  =& get_instance();
        return $ci->db->query("UPDATE role SET active=?, privilege=?, if_inherit=? WHERE id=?", array($active, bindec($priv), bindec($if_inherit), $role_id));
    }
}