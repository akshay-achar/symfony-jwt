<?php

namespace App\Services;

use Exception;
use App\Constants\GenericConstants;

class HelperService
{

    public function getFormattedPaginationRequest(array $requestData, int $totalRecordsCount = null)
    {
        try {
            $currentPage = $requestData['page'] ?? 1;
            $limit = isset($requestData['perPageCount']) ? ($requestData['perPageCount'] <= GenericConstants::MAX_ROWS_PER_PAGE ? $requestData['perPageCount'] : GenericConstants::ROWS_PER_PAGE) : GenericConstants::ROWS_PER_PAGE;
            $offset = ($currentPage - 1) * $limit;

            if (isset($totalRecordsCount)) {
                $divideRes = $totalRecordsCount / $limit;
                $moduloRes = $totalRecordsCount % $limit;
                if ($moduloRes > 0) {
                    $divideRes += 1;
                }

                $totalPages = floor($divideRes);
                $response['page'] = (int)$currentPage;
                $response['totalRecords'] = (int)$totalRecordsCount;
                $response['totalPages'] = (int)$totalPages;
                $response['perPage'] = (int)$limit;

                $requestData['offset'] = $offset;
                $requestData['limit'] = $limit;
                $requestData['sort'] = $requestData['sort'] ?? null;
                $requestData['sortField'] = $requestData['sortField'] ?? null;

                return [
                    'paginationData' => $response,
                    'filterData' => $requestData
                ];
            } else {
                $requestData['offset'] = $offset;
                $requestData['limit'] = $limit;

                return $requestData;
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
