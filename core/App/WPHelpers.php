<?php

namespace WPMNForm\Core\App;

trait WPHelpers
{
    public function activating()
    {
        return register_activation_hook(
            $this->getBaseFile(),
            $this->parseHandler(func_get_args())
        );
    }

    public function deactivating()
    {
        return register_deactivation_hook(
            $this->getBaseFile(),
            $this->parseHandler(func_get_args())
        );
    }
    
    public function addAction($hook, $handler, $priority = 10, $numParams = 1)
    {
        return add_action(
            $hook,
            $this->parseHandler($handler),
            $priority,
            $numParams
        );
    }

    public function doAction($hook, $arg = '')
    {
        return do_action($hook,  $arg);
    }

    public function removeAction($hook, $handler, $priority = 10)
    {
        return remove_action(
            $hook,
            $this->parseHandler($handler),
            $priority
        );
    }

    public function removeActions($hook, $priority = 10)
    {
        return remove_all_actions($hook, $priority);
    }

    public function addFilter($hook, $handler, $priority = 10, $numParams = 1)
    {
        return add_filter(
            $hook,
            $this->parseHandler($handler),
            $priority,
            $numParams
        );
    }

    public function doFilter($hook, $value)
    {
        return call_user_func_array([$this, 'applyFilters'], func_get_args());
    }

    public function applyFilter()
    {
        return call_user_func_array([$this, 'applyFilters'], func_get_args());
    }

    public function applyFilters()
    {
        return call_user_func_array('apply_filters', func_get_args());
    }

    public function removeFilter($hook, $handler, $priority = 10)
    {
        return remove_filter(
            $hook,
            $this->parseHandler($handler),
            $priority
        );
    }

    public function removeFilters($hook, $priority = 10)
    {
        return remove_all_filters($hook, $priority);
    }

    public function addShortCode($shortCode, $handler)
    {
        return add_shortcode(
            $this->makeHookableKey($shortCode),
            $this->parseHandler($handler)
        );
    }

    public function doShortCode($shortCode, $ignoreHtml = false)
    {
        return do_shortcode(
            $this->makeHookableKey($shortCode),
            $ignoreHtml
        );
    }

    public function removeShortCode($shortCode)
    {
        return remove_shortcode($this->makeHookableKey($shortCode));
    }

    public function onAjax($action, $handler, $target = null)
    {
        return $this->addAjaxAction(
            $this->makeActionName($action),
            $handler,
            $target
        );
    }

    public function addAjaxAction($action, $handler, $target = null)
    {
        if (is_null($target) || strtolower($target) == 'auth') {
            $this->addAction('wp_ajax_'.$action, $handler);
        }

        if (is_null($target) || strtolower($target) == 'guest') {
            $this->addAction('wp_ajax_nopriv_'.$action, $handler);
        }

        return true;
    }

    public function makeActionName($hook, $prefix = null)
    {
        return $this->makeHookableKey($hook, $prefix);
    }

    public function makeFilterName($hook, $prefix = null)
    {
        return $this->makeHookableKey($hook, $prefix);
    }

    public function makeShortcodeName($hook, $prefix = null)
    {
        return $this->makeHookableKey($hook, $prefix);
    }


    public function makeHookableKey($hook, $prefix = null)
    {
        return ($prefix ? $prefix : $this->config->get('name')).'_'.$hook;
    }
}