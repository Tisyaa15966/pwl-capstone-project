<?php

if(!function_exists('hitung_ppn'))
{
    function hitung_ppn($total)
    {
        return $total * 0.12;
    }
}

if(!function_exists('hitung_biaya_admin'))
{
    function hitung_biaya_admin($total)
    {
        if($total <= 15000000){
            return $total * 0.005;
        }

        if($total <= 35000000){
            return $total * 0.007;
        }

        return $total * 0.009;
    }
}

if(!function_exists('hitung_diskon_kupon'))
{
    function hitung_diskon_kupon($total,$kode)
    {
        $kode=strtoupper(trim($kode));

        switch($kode){

            case 'HEMAT20':
                return $total*0.20;

            case 'HEMAT30':
                return $total*0.30;

            case 'MEMBER25':
                return $total*0.25;

            default:
                return 0;
        }
    }
}