<?php
declare(strict_types=1);

namespace App\Interfaces;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Unified interface for REST controllers
 */
interface RestApiControllerInterface
{
    /**
     * Display a list of items
     *
     * @param Request $request
     * @return array|Collection
     * @throws AuthorizationException
     */
    public function index(Request $request);

    /**
     * Store a newly created item
     *
     * @param Request $request
     * @return Response
     * @throws AuthorizationException
     */
    public function store(Request $request);

    /**
     * Display the specified item
     *
     * @param  int  $id
     * @return Response
     * @throws AuthorizationException
     */
    public function show(int $id);

    /**
     * Update an item
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     * @throws AuthorizationException
     */
    public function update(Request $request, int $id);

    /**
     * Remove an item
     *
     * @param  int  $id
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(int $id);
}
