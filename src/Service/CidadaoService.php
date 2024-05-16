<?php
// src/Service/NisGenerator.php

namespace App\Service;

use App\Entity\Cidadao;
use Doctrine\ORM\EntityManagerInterface;

class CidadaoService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function insert(Cidadao $cidadao): string
    {
        try {            
            $cidadaoExiste = $this->validaCadastro($cidadao);

            if(!$cidadaoExiste){
                $nome = $cidadao->getNome();
                $nis = $this->generateUniqueNis($nome);
                $cidadao->setCreatedAt(new \DateTimeImmutable('now'));
                $cidadao->setUpdatedAt(new \DateTimeImmutable('now'));
                $cidadao->setCodigoNis($nis);
                
                $this->entityManager->persist($cidadao);
                $this->entityManager->flush();          
                
                return true;
            }
            return false;

        } catch (\Exception $e) {
            return $e->getMessage();
        }            
    }

    private function generateUniqueNis(string $nome): string
    {        
        $numero = crc32($nome);        
        
        $nisFormatado = (string) sprintf("%011d", $numero);

        return $nisFormatado;
    }

    private function validaCadastro(Cidadao $cidadao): bool
    {
        $cidadaoLocalizado = $this->entityManager->getRepository(Cidadao::class)->findOneBy(['nome' => $cidadao->getNome()]);

        $response = $cidadaoLocalizado ? true : false;

        return $response;

    }

    public function seach($nis)
    {
        $cidadao = $this->entityManager->getRepository(Cidadao::class)->findOneBy(['codigoNis' => $nis]);

        return $cidadao;

    }
}
