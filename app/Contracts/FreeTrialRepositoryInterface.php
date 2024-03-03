<?php
namespace App\Contracts;

interface FreeTrialRepositoryInterface extends ModelRepositoryInterface{
    /**
     * Prepare data for creating a new model.
     * If there is no start or end date, default once will be set.
     * Sets free trial status.
     *
     * @param array $data 
     * - 'free_trial_plan_id' => (int) FreeTrialPlan. Required,
     * - 'user_id' => (int) User. Required
     * 
     * @return array
     */
	public function prepareData(array $data): array;
}