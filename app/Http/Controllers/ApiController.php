<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{

    /**
     * Método para retornar dados de CEP da API ViaCEP
     *
     * @param string $ceps
     * @return JsonResponse
     */
    public function index(string $ceps):JsonResponse
    {
        $lista_ceps = explode(',', $ceps);
        $resposta = array();

        foreach($lista_ceps as $cep){
            //Retirando o - dos ceps para evitar algum erro
            $cep_formatado = preg_replace('~-~', '', $cep);
            if(strlen($cep_formatado) == 8 && is_numeric($cep_formatado)){
                $url = 'https://viacep.com.br/ws/'.$cep_formatado.'/json/';
                $response = Http::get($url);
                $resposta[] = $response->json();
            }else{
                $erros[] = $cep_formatado;
            }
        }

        //Ordenando o array pelo logradouro
        usort($resposta, function ($a, $b) {
            return $a['logradouro'] <=> $b['logradouro'];
        });

        //Adicionando um campo com CEPs errados
        if(!empty($erros)){
            $resposta[] = array("error" => "CEP's Inválidos", "CEPs" => $erros);
        }

        return response()->json($resposta, 200);
    }
}
