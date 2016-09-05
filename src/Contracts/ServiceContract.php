<?php

interface ServiceContract
{
    /**
     * Execute the service actions
     *
     * @param $data
     * @return mixed
     */
    public function execute($data);
}