<?php

declare(strict_types=1);

namespace src\app\http\twigextensions;

use Ramsey\Uuid\UuidFactory;
use Twig_Extension;
use Twig_Function;
use Twig_Markup;
use function uniqid;

class UtilitiesTwigExtension extends Twig_Extension
{
    public function getFunctions() : array
    {
        return [
            new Twig_Function('createUniqueId', [$this, 'createUniqueId']),
            new Twig_Function('createUuidV1', [$this, 'createUuidV1']),
            new Twig_Function('createUuidV3', [$this, 'createUuidV3']),
            new Twig_Function('createUuidV4', [$this, 'createUuidV4']),
            new Twig_Function('createUuidV5', [$this, 'createUuidV5']),
        ];
    }

    public function createUniqueId($prefix = '', $moreEntropy = false) : Twig_Markup
    {
        return new Twig_Markup(
            uniqid($prefix, $moreEntropy),
            'UTF-8'
        );
    }

    public function createUuidV1($node = null, $clockSeq = null) : Twig_Markup
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new Twig_Markup(
            (new UuidFactory())->uuid1($node, $clockSeq)->toString(),
            'UTF-8'
        );
    }

    public function createUuidV3($ns, $name) : Twig_Markup
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new Twig_Markup(
            (new UuidFactory())->uuid3($ns, $name)->toString(),
            'UTF-8'
        );
    }

    public function createUuidV4() : Twig_Markup
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new Twig_Markup(
            (new UuidFactory())->uuid4()->toString(),
            'UTF-8'
        );
    }

    public function createUuidV5($ns, $name) : Twig_Markup
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new Twig_Markup(
            (new UuidFactory())->uuid5($ns, $name)->toString(),
            'UTF-8'
        );
    }
}
