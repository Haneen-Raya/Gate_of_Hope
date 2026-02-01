<?php

namespace Modules\Programs\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\Programs\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Modules\Programs\Services\ProgramService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Programs\Http\Requests\V1\Program\StoreProgramRequest;
use Modules\Programs\Http\Requests\V1\Program\UpdateProgramRequest;

/**
 * Class ProgramController
 * * API Controller responsible for orchestrating the lifecycle of Hope Gate Programs.
 * This controller acts as a thin layer that delegates complex business logic to
 * the ProgramService and ensures all operations are authorized via ProgramPolicy.
 * * @package Modules\Programs\Http\Controllers\Api\V1
 * @version 1.0.0
 */
class ProgramController extends Controller
{
    use AuthorizesRequests;

    /**
     * Define the middleware for the controller.
     * * Applies Spatie/Laravel policy checks to specific methods using the 'can' middleware.
     * * @return array<int, Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,Modules\Programs\Models\Program', only: ['index']),
            new Middleware('can:view,program', only: ['show']),
            new Middleware('can:create,Modules\Programs\Models\Program', only: ['store']),
            new Middleware('can:update,program', only: ['update']),
            new Middleware('can:delete,program', only: ['destroy']),
        ];
    }

    /**
     * ProgramController constructor.
     * * @param ProgramService $programService The service layer handling core business logic.
     */
    public function __construct(protected ProgramService $programService)
    {
    }

    /**
     * Display a paginated list of programs based on provided filters.
     * * @path GET /api/v1/programs
     * @param Request $request Includes filters: search, status, and per_page.
     * @return JsonResponse Returns a success status with paginated program data.
     */
    public function index(Request $request): JsonResponse
    {
        $programs = $this->programService->listPrograms($request->all());
        return response()->json(['status' => 'success', 'data' => $programs]);
    }

    /**
     * Store a newly created program in the database.
     * * @path POST /api/v1/programs
     * @param StoreProgramRequest $request Contains validated data for creation.
     * @return JsonResponse Returns 201 Created on success with the program object.
     */
    public function store(StoreProgramRequest $request): JsonResponse
    {
        $program = $this->programService->createProgram($request->validated());
        return response()->json(['status' => 'success', 'message' => 'Program created', 'data' => $program], 201);
    }

    /**
     * Display the details of a specific program.
     * * @path GET /api/v1/programs/{id}
     * @param int|string $id The unique identifier of the program.
     * @return JsonResponse Returns the detailed program model with relations.
     */
    public function show($id): JsonResponse
    {
        $program = $this->programService->getProgramById((int)$id);
        return response()->json(['status' => 'success', 'data' => $program]);
    }

    /**
     * Update an existing program's information.
     * * @path PUT /api/v1/programs/{program}
     * @param UpdateProgramRequest $request Validated update data.
     * @param int|string $id The ID of the program to be updated.
     * @return JsonResponse Returns a success message and the updated program.
     */
    public function update(UpdateProgramRequest $request, $id): JsonResponse
    {
        $program = $this->programService->updateProgram((int)$id, $request->validated());
        return response()->json(['status' => 'success', 'message' => 'Program updated', 'data' => $program]);
    }

    /**
     * Remove a program from storage (soft or hard delete based on model config).
     * * @path DELETE /api/v1/programs/{program}
     * @param int|string $id The unique identifier of the program.
     * @return JsonResponse Success notification after deletion.
     */
    public function destroy($id): JsonResponse
    {
        $this->programService->deleteProgram((int)$id);
        return response()->json(['status' => 'success', 'message' => 'Program deleted successfully']);
    }
}
