<?php

namespace App\Service;

use App\Util\Config;
use Slim\Psr7\UploadedFile;

class ArquivoService {

    public function getMimeType(string $filePath) {
        if(file_exists($filePath))
            return mime_content_type($filePath);;
        
        return "";
    }

    public function salvarArquivo(UploadedFile $arquivo) {
        $path = Config::PATH_FILES;

        $extencao = pathinfo($arquivo->getClientFilename(), PATHINFO_EXTENSION);

        $nomeUUID = bin2hex(random_bytes(8));
        $nomeSalvar = sprintf('%s.%0.8s', $nomeUUID, $extencao);

        $arquivo->moveTo($path . "/" . $nomeSalvar);

        return $nomeSalvar;
    }

    public function removerArquivo($nomeArquivo) {
        $caminhoArquivo = Config::PATH_FILES . "/" . $nomeArquivo;

        if(file_exists($caminhoArquivo))
            unlink($caminhoArquivo);
    }
    
}