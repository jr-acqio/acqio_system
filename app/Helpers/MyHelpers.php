<?php
if (! function_exists('mes_extenso')) {
    /**
     * Obtém um inteiro representando o mês e retorna o mês equivalente em string.
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
        		$mes_extenso = 'Fevereiro';
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

        return strtoupper($mes_extenso);
    }
}

if (! function_exists('pessoa_fisica')) {
    /**
     * Obtém string representando o documento (CPF OU CNPJ) e retorna 1 inteiro representando boolean
     *
     * @param  string  $documento
     * @return integer (1 for TRUE, 0 for FALSE)
     */
    function pessoa_fisica($doc){
        if (strlen($doc) == 14) {
            return 1;
        }
        return 0;
    }
}