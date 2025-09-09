<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class CepController extends Controller
{
    public function show(string $cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);

        if (strlen($cep) !== 8) {
            return response()->json(['error' => 'CEP inválido'], 400);
        }

        $url = "https://viacep.com.br/ws/{$cep}/json/";

        $client = new Client();

        try {
            $response = $client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['erro']) && $data['erro'] === true) {
                return response()->json(['error' => 'CEP não encontrado.'], 404);
            }

            $formattedData = [
                'cep' => $data['cep'] ?? null,
                'logradouro' => $data['logradouro'] ?? null,
                'complemento' => $data['complemento'] ?? null,
                'unidade' => $data['unidade'] ?? null,
                'bairro' => $data['bairro'] ?? null,
                'localidade' => $data['localidade'] ?? null,
                'uf' => $data['uf'] ?? null,
                'ibge' => $data['ibge'] ?? null,
                'gia' => $data['gia'] ?? null,
                'ddd' => $data['ddd'] ?? null,
                'siafi' => $data['siafi'] ?? null,
            ];

            return response()->json($formattedData, 200);

        } catch (Exception $e) {
            Log::error("Erro ao buscar CEP {$cep}: " . $e->getMessage());
            return response()->json(['error' => 'Não foi possível buscar os dados do CEP'], 500);
        }
    }
}
