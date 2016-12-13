<?php
if (! function_exists('mes_extenso')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  integer  $mes
     * @return string $mes
     */
    function mes_extenso($mes)
    {
    	$mes_extenso = '';
        switch ($mes) {
        	case 1:
        		$mes_extenso = 'Janeiro';
        		break;
        	case 2:
        		$mes_extenso = 'Feverereiro';
        		break;
        	case 3:
        		$mes_extenso = 'Março';
        		break;
        	case 4:
        		$mes_extenso = 'Abril';
        		break;
        	case 5:
        		$mes_extenso = 'Maio';
        		break;
        	case 6:
        		$mes_extenso = 'Junho';
        		break;
        	case 7:
        		$mes_extenso = 'Julho';
        		break;
        	case 8:
        		$mes_extenso = 'Agosto';
        		break;
        	case 9:
        		$mes_extenso = 'Setembro';
        		break;
        	case 10:
        		$mes_extenso = 'Outubro';
        		break;
        	case 11:
        		$mes_extenso = 'Novembro';
        		break;
        	case 12:
        		$mes_extenso = 'Dezembro';
        		break;
        	default:
        		return 'Valor inválido';
        		break;
        }

        return $mes_extenso;
    }
}