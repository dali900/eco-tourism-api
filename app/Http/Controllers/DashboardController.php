<?php

namespace App\Http\Controllers;

use App\Contracts\DashboardRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;

class DashboardController extends Controller
{
    /**
     * DashboardRepository
     *
     * @var DashboardRepository
     */
    private $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository) {
        $this->dashboardRepository = $dashboardRepository;
    }

    /**
     * Get dashboard stats
     *
     * @return Response
     */
    function index($app) : JsonResponse {
        $rowCounts = $this->dashboardRepository->countRows($app);
        return $this->responseSuccess([
            'row_counts' => $rowCounts[0],
        ]);
    }
}
