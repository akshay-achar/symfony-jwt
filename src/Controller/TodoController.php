<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View as View;
use Symfony\Component\HttpFoundation\Request;
use App\Services\TodoService;

/**
 * Class TodoController
 * @Route("/api/todo")
 * @SWG\Tag(name="Todo")
 */
class TodoController extends AbstractFOSRestController
{

    private $todoService;

    public function __construct(TodoService $todoService)
    {
        $this->todoService = $todoService;
    }

    /**
     * Todo List Creation
     * @Rest\Post("/create", name="todo_list_create")
     * @SWG\Response(
     *     response=201,
     *     description="Create the Todo List"
     * )
     * */
    public function create(Request $request)
    {

        $response = $this->todoService->createTodo($request);

        return View::create($response, $response['code']);
    }

    /**
     * Todo Listing
     * @Rest\Get("/list", name="todo_list")
     * @SWG\Response(
     *     response=200,
     *     description="List the Todo"
     * )
     * */
    public function list()
    {
        $response = $this->todoService->listTodo();

        return View::create($response, $response['code']);
    }

    /**
     * Todo List Specific Show
     * @Rest\Get("/{id}/show", name="todo_list_show")
     * @SWG\Response(
     *     response=200,
     *     description="Show the specific Todo"
     * )
     * */
    public function show($id)
    {
        $response = $this->todoService->listTodo(['id' => $id], true);
        return View::create($response, $response['code']);
    }

    /**
     * Todo List Update
     * @Rest\Patch("/{id}/update", name="todo_list_update")
     * @SWG\Response(
     *     response=200,
     *     description="Successfully update the Todo"
     * )
     * */
    public function update(Request $request, $id)
    {
        $response = $this->todoService->updateTodo($request, $id);

        return View::create($response, $response['code']);
    }

    /**
     * Todo List Delete
     * @Rest\Delete("/{id}/delete", name="todo_list_delete")
     * @SWG\Response(
     *     response=200,
     *     description="Successfully delete the Todo"
     * )
     * */
    public function delete($id)
    {
        $response = $this->todoService->deleteTodo($id);
        return View::create($response, $response['code']);
    }
}
