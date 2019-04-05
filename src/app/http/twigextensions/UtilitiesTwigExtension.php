<?php

declare(strict_types=1);

namespace src\app\http\twigextensions;

use Ramsey\Uuid\UuidFactory;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;
use function uniqid;

class UtilitiesTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('createUniqueId', [$this, 'createUniqueId']),
            new TwigFunction('createUuidV1', [$this, 'createUuidV1']),
            new TwigFunction('createUuidV3', [$this, 'createUuidV3']),
            new TwigFunction('createUuidV4', [$this, 'createUuidV4']),
            new TwigFunction('createUuidV5', [$this, 'createUuidV5']),
        ];
    }

    /**
     * @param mixed $prefix
     * @param mixed $moreEntropy
     */
    public function createUniqueId($prefix = '', $moreEntropy = false) : Markup
    {
        return new Markup(
            uniqid($prefix, $moreEntropy),
            'UTF-8'
        );
    }

    /**
     * @param mixed $node
     * @param mixed $clockSeq
     */
    public function createUuidV1($node = null, $clockSeq = null) : Markup
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new Markup(
            (new UuidFactory())->uuid1($node, $clockSeq)->toString(),
            'UTF-8'
        );
    }

    /**
     * @param mixed $ns
     * @param mixed $name
     */
    public function createUuidV3($ns, $name) : Markup
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new Markup(
            (new UuidFactory())->uuid3($ns, $name)->toString(),
            'UTF-8'
        );
    }

    public function createUuidV4() : Markup
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new Markup(
            (new UuidFactory())->uuid4()->toString(),
            'UTF-8'
        );
    }

    /**
     * @param mixed $ns
     * @param mixed $name
     */
    public function createUuidV5($ns, $name) : Markup
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new Markup(
            (new UuidFactory())->uuid5($ns, $name)->toString(),
            'UTF-8'
        );
    }
}
