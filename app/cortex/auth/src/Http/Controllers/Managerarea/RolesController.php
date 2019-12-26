<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Controllers\Managerarea;

use Exception;
use Cortex\Auth\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Cortex\Foundation\DataTables\LogsDataTable;
use Cortex\Foundation\Importers\DefaultImporter;
use Cortex\Foundation\DataTables\ImportLogsDataTable;
use Cortex\Auth\DataTables\Managerarea\RolesDataTable;
use Cortex\Foundation\Http\Requests\ImportFormRequest;
use Cortex\Foundation\DataTables\ImportRecordsDataTable;
use Cortex\Auth\Http\Requests\Managerarea\RoleFormRequest;
use Cortex\Foundation\Http\Controllers\AuthorizedController;
use Cortex\Auth\Http\Requests\Managerarea\RoleFormProcessRequest;

class RolesController extends AuthorizedController
{
    /**
     * {@inheritdoc}
     */
    protected $resource = Role::class;

    /**
     * List all roles.
     *
     * @param \Cortex\Auth\DataTables\Managerarea\RolesDataTable $rolesDataTable
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(RolesDataTable $rolesDataTable)
    {
        return $rolesDataTable->with([
            'id' => 'managerarea-roles-index-table',
        ])->render('cortex/foundation::managerarea.pages.datatable-index');
    }

    /**
     * List role logs.
     *
     * @param \Cortex\Auth\Models\Role                    $role
     * @param \Cortex\Foundation\DataTables\LogsDataTable $logsDataTable
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function logs(Role $role, LogsDataTable $logsDataTable)
    {
        return $logsDataTable->with([
            'resource' => $role,
            'tabs' => 'managerarea.roles.tabs',
            'id' => "managerarea-roles-{$role->getRouteKey()}-logs-table",
        ])->render('cortex/foundation::managerarea.pages.datatable-tab');
    }

    /**
     * Import roles.
     *
     * @param \Cortex\Auth\Models\Role                             $role
     * @param \Cortex\Foundation\DataTables\ImportRecordsDataTable $importRecordsDataTable
     *
     * @return \Illuminate\View\View
     */
    public function import(Role $role, ImportRecordsDataTable $importRecordsDataTable)
    {
        return $importRecordsDataTable->with([
            'resource' => $role,
            'tabs' => 'managerarea.roles.tabs',
            'url' => route('managerarea.roles.stash'),
            'id' => "managerarea-roles-{$role->getRouteKey()}-import-table",
        ])->render('cortex/foundation::managerarea.pages.datatable-dropzone');
    }

    /**
     * Stash roles.
     *
     * @param \Cortex\Foundation\Http\Requests\ImportFormRequest $request
     * @param \Cortex\Foundation\Importers\DefaultImporter       $importer
     *
     * @return void
     */
    public function stash(ImportFormRequest $request, DefaultImporter $importer)
    {
        // Handle the import
        $importer->config['resource'] = $this->resource;
        $importer->handleImport();
    }

    /**
     * Hoard roles.
     *
     * @param \Cortex\Foundation\Http\Requests\ImportFormRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function hoard(ImportFormRequest $request)
    {
        foreach ((array) $request->get('selected_ids') as $recordId) {
            $record = app('cortex.foundation.import_record')->find($recordId);

            try {
                $fillable = collect($record['data'])->intersectByKeys(array_flip(app('rinvex.auth.role')->getFillable()))->toArray();

                tap(app('rinvex.auth.role')->firstOrNew($fillable), function ($instance) use ($record) {
                    $instance->save() && $record->delete();
                });
            } catch (Exception $exception) {
                $record->notes = $exception->getMessage().(method_exists($exception, 'getMessageBag') ? "\n".json_encode($exception->getMessageBag())."\n\n" : '');
                $record->status = 'fail';
                $record->save();
            }
        }

        return intend([
            'back' => true,
            'with' => ['success' => trans('cortex/foundation::messages.import_complete')],
        ]);
    }

    /**
     * List role import logs.
     *
     * @param \Cortex\Foundation\DataTables\ImportLogsDataTable $importLogsDatatable
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function importLogs(ImportLogsDataTable $importLogsDatatable)
    {
        return $importLogsDatatable->with([
            'resource' => trans('cortex/auth::common.role'),
            'tabs' => 'managerarea.roles.tabs',
            'id' => 'managerarea-roles-import-logs-table',
        ])->render('cortex/foundation::managerarea.pages.datatable-tab');
    }

    /**
     * Create new role.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Cortex\Auth\Models\Role $role
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request, Role $role)
    {
        return $this->form($request, $role);
    }

    /**
     * Edit given role.
     *
     * @param \Cortex\Auth\Http\Requests\Managerarea\RoleFormRequest $request
     * @param \Cortex\Auth\Models\Role                               $role
     *
     * @return \Illuminate\View\View
     */
    public function edit(RoleFormRequest $request, Role $role)
    {
        return $this->form($request, $role);
    }

    /**
     * Show role create/edit form.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Cortex\Auth\Models\Role $role
     *
     * @return \Illuminate\View\View
     */
    protected function form(Request $request, Role $role)
    {
        $abilities = $request->user($this->getGuard())->getManagedAbilities();

        return view('cortex/auth::managerarea.pages.role', compact('role', 'abilities'));
    }

    /**
     * Store new role.
     *
     * @param \Cortex\Auth\Http\Requests\Managerarea\RoleFormProcessRequest $request
     * @param \Cortex\Auth\Models\Role                                      $role
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(RoleFormProcessRequest $request, Role $role)
    {
        return $this->process($request, $role);
    }

    /**
     * Update given role.
     *
     * @param \Cortex\Auth\Http\Requests\Managerarea\RoleFormProcessRequest $request
     * @param \Cortex\Auth\Models\Role                                      $role
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(RoleFormProcessRequest $request, Role $role)
    {
        return $this->process($request, $role);
    }

    /**
     * Process stored/updated role.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param \Cortex\Auth\Models\Role                $role
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function process(FormRequest $request, Role $role)
    {
        // Prepare required input fields
        $data = $request->validated();

        // Save role
        $role->fill($data)->save();

        return intend([
            'url' => route('managerarea.roles.index'),
            'with' => ['success' => trans('cortex/foundation::messages.resource_saved', ['resource' => trans('cortex/auth::common.role'), 'identifier' => $role->title])],
        ]);
    }

    /**
     * Destroy given role.
     *
     * @param \Cortex\Auth\Models\Role $role
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return intend([
            'url' => route('managerarea.roles.index'),
            'with' => ['warning' => trans('cortex/foundation::messages.resource_deleted', ['resource' => trans('cortex/auth::common.role'), 'identifier' => $role->title])],
        ]);
    }
}
