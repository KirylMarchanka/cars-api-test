<?php

namespace App\Http\Controllers;

use App\Http\Requests\Car\Model\{CreateRequest, UpdateRequest};
use App\Http\Resources\CarResource;
use App\Models\CarModel;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CarModelsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['check.is.model.owner'])->only(['destroy', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $models = CarModel::query()->select(['id', 'name', 'created_by'])->paginate();

        return response()->json($models);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function store(CreateRequest $request): JsonResponse
    {
        $model = CarModel::query()->create($request->validated());

        return response()->json([
            'message' => 'Car model successfully created',
            'data' => (new CarResource($model))
        ], Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param CarModel $model
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, CarModel $model): JsonResponse
    {
        $model->update($request->validated());

        return response()->json([
            'message' => 'Car brand successfully updated',
            'data' => (new CarResource($model))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CarModel $model
     * @return JsonResponse
     */
    public function destroy(CarModel $model): JsonResponse
    {
        $model->delete();

        return response()->json([
            'message' => 'Car model successfully deleted'
        ]);
    }
}
