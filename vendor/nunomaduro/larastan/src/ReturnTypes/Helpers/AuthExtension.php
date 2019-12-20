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

namespace NunoMaduro\Larastan\ReturnTypes\Helpers;

use PHPStan\Type\Type;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use NunoMaduro\Larastan\Concerns;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;

/**
 * @internal
 */
final class AuthExtension implements DynamicFunctionReturnTypeExtension
{
    use Concerns\HasContainer;

    /**
     * {@inheritdoc}
     */
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return $functionReflection->getName() === 'auth';
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeFromFunctionCall(
        FunctionReflection $functionReflection,
        FuncCall $functionCall,
        Scope $scope
    ): Type {
        if (! isset($functionCall->args[0]->value) || (isset($functionCall->args[0]->value) && $functionCall->args[0]->value === null)) {
            return new ObjectType(get_class($this->resolve(\Illuminate\Contracts\Auth\Factory::class)));
        }

        return new ObjectType(\Illuminate\Contracts\Auth\Guard::class);
    }
}
