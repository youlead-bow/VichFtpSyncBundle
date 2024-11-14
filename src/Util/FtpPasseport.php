<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\Util;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FtpPasseport
{
    const int DEFAULT_PORT = 21;

    public function __construct(
        private ?string $scheme = null,
        private ?string $host = null,
        private ?string $user = null,
        private ?string $pass = null,
        private ?string $path = null,
        private int $port = self::DEFAULT_PORT,
        private string $protocol = 'https'
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public static function getInstance(string $dsn): self
    {
        $serializer = new Serializer([new ObjectNormalizer()]);
        return $serializer->denormalize(parse_url($dsn), __CLASS__);
    }

    public function getUri(): string
    {
        $uri = $this->protocol . '://' . $this->host;
        if(!is_null($this->path)){
            $uri .= ':' . $this->path;
        }
        if($this->port != self::DEFAULT_PORT){
            $uri .= ':' . $this->port;
        }
        return $uri;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function setScheme(string $scheme): self
    {
        $this->scheme = $scheme;

        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function setPass(string $pass): self
    {
        $this->pass = $pass;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }

    public function setProtocol(string $protocol): self
    {
        $this->protocol = $protocol;

        return $this;
    }
}