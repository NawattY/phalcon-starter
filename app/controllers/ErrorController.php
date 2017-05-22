<?php

class ErrorController extends ControllerBase
{
    public function page404Action()
    {
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setRawHeader('HTTP/1.1 404 Not Found');
        $this->response->setStatusCode(404, 'Not Fount');
        $this->response->setJsonContent([
            'status' => '404',
            'message' => 'service not found or unavailable',
            'data' => '',
        ]);

        return $this->response;
    }
}
