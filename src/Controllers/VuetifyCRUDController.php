<?php

namespace IAmRGroot\VuetifyCRUD\Controllers;

use Exception;
use IAmRGroot\VuetifyCRUD\Fields\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

abstract class VuetifyCRUDController extends Controller
{
    abstract public static function getName(): string;

    abstract public static function getTranslation(): string;

    abstract public static function getDefault(): string;

    abstract protected static function getModel(): string;

    /**
     * @return array<string, string>
     */
    abstract protected static function getValidation(): array;

    private Model $instance;

    public function __construct()
    {
        if ('' === static::getName()) {
            throw new Exception('Prefix not set correctly');
        }

        if (! class_exists(static::getModel())) {
            throw new Exception('Model is not a class');
        }

        $name           = static::getModel();
        $this->instance = new $name();
    }

    public static function routes(?string $permission = null): void
    {
        Route::get(static::getName(), [static::class, 'get'])->middleware(is_null($permission) ? null : "{$permission}.view");
        Route::put(static::getName(), [static::class, 'put'])->middleware(is_null($permission) ? null : "{$permission}.create");
        Route::patch(static::getName() . '/{model}', [static::class, 'patch'])->middleware(is_null($permission) ? null : "{$permission}.edit");
        Route::delete(static::getName() . '/{model}', [static::class, 'delete'])->middleware(is_null($permission) ? null : "{$permission}.delete");
    }

    /**
     * @return Collection<Model|Builder>
     */
    public function get(): Collection
    {
        return $this->instance->query()->get();
    }

    public function put(Request $request): Model
    {
        return $this->store($request, $this->instance->newInstance());
    }

    public function update(Request $request, Model $model): Model
    {
        return $this->store($request, $model);
    }

    public function delete(Model $model): JsonResponse
    {
        $model->delete();

        return Response::json('ok');
    }

    protected function store(Request $request, Model $model): Model
    {
        $validated = $request->validate($this->getValidation());

        $model->fill($validated);
        $model->save();

        return $model;
    }

    /**
     * @return Field[]
     */
    public function fields(): array
    {
        $fields = array_keys(
            $this->instance->getConnection()->getDoctrineSchemaManager()->listTableColumns($this->instance->getTable())
        );

        return array_map(
            fn (string $attribute) => new Field($attribute),
            $fields
        );
    }

    public function getHeaders(): string
    {
        $headers =implode(
            ', ',
            array_map(
                fn (Field $field) => $field->toHeader(),
                $this->fields()
            )
        );

        return "[ {$headers} ]";
    }

    public function renderForm(): string
    {
        return 'TODO';
    }
}
