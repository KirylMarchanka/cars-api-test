<?php

namespace App\Http\Controllers;

use App\Http\Requests\Car\Brand\{CreateRequest, SearchRequest, UpdateRequest};
use App\Http\Resources\CarResource;
use App\Models\{CarBrand, User};
use Illuminate\Http\{JsonResponse, Request};
use Symfony\Component\HttpFoundation\Response;

class CarBrandsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['check.is.brand.owner'])->only(['destroy', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $brands = CarBrand::query()->select(['id', 'name', 'created_by'])->paginate();

        return response()->json($brands);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function store(CreateRequest $request): JsonResponse
    {
        $brand = CarBrand::query()->create($request->validated());

        //@todo Use Api resource
        return response()->json([
            'message' => 'Car brand successfully created',
            'data' => (new CarResource($brand))
        ], Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param CarBrand $brand
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, CarBrand $brand): JsonResponse
    {
        $brand->update($request->validated());

        return response()->json([
            'message' => 'Car brand successfully updated',
            'data' => (new CarResource($brand))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CarBrand $brand
     * @return JsonResponse
     */
    public function destroy(CarBrand $brand): JsonResponse
    {
        $brand->delete();

        return response()->json([
            'message' => 'Car brand successfully deleted'
        ]);
    }

    public function search(SearchRequest $request): JsonResponse
    {
        $name = $request->validated()['name'];
        $brand = CarBrand::query()
            ->where('name', $name)
            ->orWhereHas('CarsModels', fn ($query) => $query->where('name', $name))
            ->with('CarsModels')
            ->firstOrFail(['id', 'name', 'created_by']);

        return response()->json([
            'data' => $brand
        ]);
    }

    public function getUserBrands(Request $request): JsonResponse
    {
        $createdBy = User::getUserByAccessToken($request->bearerToken())->id;
        $brands = CarBrand::query()
            ->select(['id', 'name', 'created_by'])
            ->where('created_by', $createdBy)
            ->paginate();

        return response()->json($brands);
    }
}
