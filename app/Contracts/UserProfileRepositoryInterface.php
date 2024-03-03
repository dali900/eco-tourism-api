<?php
namespace App\Contracts;

interface UserProfileRepositoryInterface extends ModelRepositoryInterface {
    
	/**
     * Construct user profile query with subscription and free trial data
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getQuery();

    /**
     * Filter and sort all models
     *
     * @param array $params
     * @param \Illuminate\Database\Query\Builder $customModel
     * @return void
     */
    public function getAllFiltered($params = [], $customModel = null);

    /**
     * Get user profile by id
     *
     * @param integer $userId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getByUserId($userId);

    public function getByMailAndApp($mail, $app);

}