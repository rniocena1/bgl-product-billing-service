<?php

namespace App\Lib;

class Response
{
    private $status = 200;

    /**
     * Function to return the status
     *
     * @param integer $code
     * @return void
     */
    public function status(int $code)
    {
        $this->status = $code;
        return $this;
    }
    
    /**
     * Function to return the data in json format
     *
     * @param array $data
     * @return void
     */
    public function toJSON($data = [])
    {
        http_response_code($this->status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}