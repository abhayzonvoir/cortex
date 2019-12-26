<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Controllers\Managerarea;

use Cortex\Auth\Models\Manager;
use Spatie\MediaLibrary\Models\Media;
use Cortex\Foundation\Http\Controllers\AuthorizedController;

class ManagersMediaController extends AuthorizedController
{
    /**
     * {@inheritdoc}
     */
    protected $resource = Manager::class;

    /**
     * {@inheritdoc}
     */
    public function authorizeResource($model, $parameter = null, array $options = [], $request = null): void
    {
        $middleware = [];
        $parameter = $parameter ?: snake_case(class_basename($model));

        foreach ($this->mapResourceAbilities() as $method => $ability) {
            $modelName = in_array($method, $this->resourceMethodsWithoutModels()) ? $model : $parameter;

            $middleware["can:update,{$modelName}"][] = $method;
            $middleware["can:{$ability},media"][] = $method;
        }

        foreach ($middleware as $middlewareName => $methods) {
            $this->middleware($middlewareName, $options)->only($methods);
        }
    }

    /**
     * Destroy given manager media.
     *
     * @param \Cortex\Auth\Models\Manager       $manager
     * @param \Spatie\MediaLibrary\Models\Media $media
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Manager $manager, Media $media)
    {
        $manager->media()->where($media->getKeyName(), $media->getKey())->first()->delete();

        return intend([
            'url' => route('managerarea.managers.edit', ['manager' => $manager]),
            'with' => ['warning' => trans('cortex/foundation::messages.resource_deleted', ['resource' => trans('cortex/foundation::common.media'), 'identifier' => $media->getRouteKey()])],
        ]);
    }
}
