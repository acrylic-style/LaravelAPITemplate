<?php
function responseError($error = "There was an error while processing your request.", $status) {
    return response()->json(["error" => $error, "status" => $status], $status);
}