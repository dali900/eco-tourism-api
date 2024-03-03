<?php
namespace App\Contracts;

interface DashboardRepositoryInterface extends ModelRepositoryInterface {

    /**
     * Counts all rows
     *
     * @param string $app
     * @return array
     */
	function countRows(string $app) : array;
}