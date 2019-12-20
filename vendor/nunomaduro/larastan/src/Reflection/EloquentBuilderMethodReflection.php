<?php

declare(strict_types=1);

/**
 * This file is part of Larastan.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Larastan\Reflection;

use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use Illuminate\Database\Eloquent\Builder;
use PHPStan\Reflection\ClassMemberReflection;

final class EloquentBuilderMethodReflection implements MethodReflection
{
    /**
     * @var string
     */
    private $methodName;

    /**
     * @var ClassReflection
     */
    private $classReflection;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var Type
     */
    private $returnType;

    public function __construct(string $methodName, ClassReflection $classReflection, array $parameters, ?Type $returnType = null)
    {
        $this->methodName = $methodName;
        $this->classReflection = $classReflection;
        $this->parameters = $parameters;
        $this->returnType = $returnType ?? new ObjectType(Builder::class);
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->classReflection;
    }

    public function isStatic(): bool
    {
        return true;
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function getName(): string
    {
        return $this->methodName;
    }

    public function getPrototype(): ClassMemberReflection
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariants(): array
    {
        return [
            new FunctionVariant(
                $this->parameters,
                false,
                $this->returnType
            ),
        ];
    }
}
