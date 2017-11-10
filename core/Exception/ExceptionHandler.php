<?php

namespace WPMNForm\Core\Exception;

class ExceptionHandler
{
    const APPEND_TO_LOG_FILE = 3;

    /**
     * Core\App\Application
     * @var Object
     */
    protected $plugin = null;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
        $this->registerHandlers();
    }

	public function registerHandlers()
	{
		error_reporting(-1);
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
	}

	public function handleError($severity, $message, $file = '', $line = 0)
    {
        if (error_reporting() & $severity) {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        }
    }

    public function handleException($e)
    {
        try {
            if (@$this->plugin->getPluginSettings()['debug']) {
                $this->report($e);
                $this->render($e);
            }
        } catch (\Exception $e) {
            die($e->getMessage().' : '.__METHOD__.' ('.__LINE__.')');
        }
    }

    public function handleShutdown()
    {
        if (!is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            $this->handleException(new \ErrorException(
                $error['message'], 0, $error['type'], $error['file'], $error['line']
            ));
        }
    }

    public function report($e)
    {
        error_log(
            $e->getMessage() . ' in ' . $e->getFile() . ' (' . $e->getLine() .")\n".
            $e->getTraceAsString(),
            self::APPEND_TO_LOG_FILE,
            $this->plugin->basePath('error.log')
        );
    }

    public function render($e)
    {
        echo $e->getMessage() . ' in ' . $e->getFile() . ' (' . $e->getLine() . ')<br>';
        echo str_replace("\n", '<br>', $e->getTraceAsString());
    }

    protected function isFatal($type)
    {
        return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
    }
}