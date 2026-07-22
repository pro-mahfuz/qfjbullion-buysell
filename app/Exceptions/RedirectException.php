<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;

class RedirectException extends Exception
{
    protected $message;

    public function __construct($message = "Something went wrong.")
    {
        // Pass the message to the parent Exception class
        parent::__construct($message);
        $this->message = $message;
    }

    /**
     * Report the exception (optional).
     */
    public function report()
    {
        // Log the error, send it to a monitoring service, etc. (optional)
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function render(): RedirectResponse
    {
        // Redirect back with an error message
        return redirect()->back()->with('error', $this->message);
    }
}
