<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

interface RestApiControllerInterface
{
    /**
     * Display a list of items
     *
     * @param Request $request
     * @return array|\Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request);

    /**
     * Store a newly created item
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request);

    /**
     * Display the specified item
     *
     * @param Model $model
     * @return JsonResource
     */
    public function show(Model $model): JsonResource;

    /**
     * Update an item
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, int $id);

    /**
     * Remove an item
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(int $id);
}
