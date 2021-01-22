<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use App\Constants\StatusConstants;
use App\Entity\Task;
use App\Services\ValidationService;
use Symfony\Component\HttpFoundation\Request;

class TodoService
{

    private $entityManager;

    private $logger;

    private $validationService;

    private $helperService;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, ValidationService $validationService, HelperService $helperService)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->validationService = $validationService;
        $this->helperService = $helperService;
    }

    public function createTodo($request)
    {
        $responseData = ['code' => Response::HTTP_BAD_REQUEST, 'message' => ''];
        try {

            $validationResult = $this->validationService->validateCollection($request, ['name', 'description', 'status'], 'todo');

            if ($validationResult === true) {
                $requestData = json_decode($request->getContent(), true);

                $task = new Task();

                $task->setName($requestData['name']);
                $task->setDescription($requestData['description']);
                $task->setStatus($requestData['status']);

                $this->entityManager->persist($task);
                $this->entityManager->flush();

                $responseData = ['code' => Response::HTTP_CREATED, 'message' => 'Created the todo listed succesfully'];
            } else {
                $responseData['message'] = $validationResult;
            }
        } catch (Exception $exception) {
            $responseData['code'] = Response::HTTP_INTERNAL_SERVER_ERROR;
            $responseData['message'] = $exception->getMessage();
            $this->logger->log('error', $exception->getMessage(), ['exception' => $exception]);
        }
        return $responseData;
    }

    public function listTodo($requestData = [], $detailed = false)
    {
        $responseData = ['code' => Response::HTTP_BAD_REQUEST, 'message' => ''];

        try {
            if (!$detailed) {
                $count = $this->entityManager->getRepository(Task::class)->getTodoList($requestData, true);
                $formattedRequestData = $this->helperService->getFormattedPaginationRequest($requestData, $count);
                $responseData = $formattedRequestData['paginationData'];
                $requestData = $formattedRequestData['filterData'];
            }

            $result = $this->entityManager->getRepository(Task::class)->getTodoList($requestData);

            $responseData['code'] = Response::HTTP_OK;
            $responseData['data'] = $result;
        } catch (Exception $exception) {
            $responseData['code'] = Response::HTTP_INTERNAL_SERVER_ERROR;
            $responseData['message'] = $exception->getMessage();
            $this->logger->log('error', $exception->getMessage(), ['exception' => $exception]);
        }

        return $responseData;
    }

    public function updateTodo($request, $id)
    {
        $responseData = ['code' => Response::HTTP_BAD_REQUEST, 'message' => ''];

        try {
            $idValidationResult = $this->validationService->validateCollection($request, ['id'], 'idInteger', ['id' => (int) $id]);

            if ($idValidationResult === true) {

                $validationResult = $this->validationService->validateCollection($request, ['name', 'description', 'status'], 'todo');

                if ($validationResult === true) {

                    $requestData = json_decode($request->getContent(), true);

                    $task = $this->entityManager->getRepository(Task::class)->find($id);

                    if ($task instanceof Task) {
                        $task->setName($requestData['name']);
                        $task->setDescription($requestData['description']);
                        $task->setStatus($requestData['status']);

                        $this->entityManager->persist($task);
                        $this->entityManager->flush();
                        $responseData = ['code' => Response::HTTP_OK, 'message' => 'Updated the todo listed succesfully'];
                    } else {
                        $responseData['message'] = 'Task Not Found';
                    }
                } else {
                    $responseData['message'] = $validationResult;
                }
            } else {
                $responseData['message'] = $idValidationResult;
            }
        } catch (Exception $exception) {
            $responseData['code'] = Response::HTTP_INTERNAL_SERVER_ERROR;
            $responseData['message'] = $exception->getMessage();
            $this->logger->log('error', $exception->getMessage(), ['exception' => $exception]);
        }

        return $responseData;
    }

    public function deleteTodo($id)
    {
        $responseData = ['code' => Response::HTTP_BAD_REQUEST, 'message' => ''];

        try {
            $idValidationResult = $this->validationService->validateCollection(new Request(), ['id'], 'idInteger', ['id' => (int) $id]);

            if ($idValidationResult === true) {
                $task = $this->entityManager->getRepository(Task::class)->find($id);

                if ($task instanceof Task) {
                    $task->setStatus(StatusConstants::DELETED);

                    $this->entityManager->persist($task);
                    $this->entityManager->flush();
                    $responseData = ['code' => Response::HTTP_OK, 'message' => 'Deleted the todo listed succesfully'];
                } else {
                    $responseData['message'] = 'Task Not Found';
                }
            } else {
                $responseData['message'] = $idValidationResult;
            }
        } catch (Exception $exception) {
            $responseData['code'] = Response::HTTP_INTERNAL_SERVER_ERROR;
            $responseData['message'] = $exception->getMessage();
            $this->logger->log('error', $exception->getMessage(), ['exception' => $exception]);
        }

        return $responseData;
    }
}
